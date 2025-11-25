<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Assignment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BannerApiController extends Controller
{
    public function show(Request $request)
    {
        $zoneName = $request->get('zone');
        $siteDomain = $request->get('site');

        if (!$zoneName || !$siteDomain) {
            return response()->json(['error' => 'zone and site parameters required'], 400);
        }

        $zone = Zone::where('name', $zoneName)->first();
        if (!$zone) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        // Obtener banners desde la zona (método centralizado)
        $banners = $zone->bannersForDisplay();

        // Si no hay banners, devolver 404
        if ($banners->isEmpty()) {
            return response()->json(['error' => 'No banners available'], 404);
        }

        // Separar principales y el resto
        $principals = $banners->filter(fn($b) => (bool) ($b->principal ?? false));
        $others = $banners->filter(fn($b) => ! (bool) ($b->principal ?? false));

        $session = session();
        $sessionId = $session->getId() ?? request()->ip();

        $orderKey = "banner_rotation.order.{$siteDomain}.{$zone->id}";
        $indexKey = "banner_rotation.index.{$siteDomain}.{$zone->id}";
        $firstServedKey = "banner_rotation.first_served.{$siteDomain}.{$zone->id}";

        // Build deterministic per-session order for non-principal banners if missing
        if (! $session->has($orderKey)) {
            $ordered = $others->sortBy(function ($b) use ($sessionId) {
                return md5($sessionId . '::' . $b->id);
            })->pluck('id')->values()->all();
            $session->put($orderKey, $ordered);
            $session->put($indexKey, 0);
            $session->put($firstServedKey, false);
        }

        // Support explicit slot parameter (0 = first slot)
        $slot = $request->query('slot');
        if (! is_null($slot)) {
            $slot = intval($slot);
            if ($slot === 0 && $principals->isNotEmpty()) {
                // First slot always principal (pick most recent principal)
                $banner = $principals->sortByDesc('created_at')->first();
            } else {
                $order = $session->get($orderKey, []);
                if (empty($order)) {
                    // fallback to principals or any banner
                    $banner = $principals->first() ?? $banners->first();
                } else {
                    $idx = ($slot - 1) % count($order);
                    $bannerId = $order[$idx];
                    $banner = $banners->firstWhere('id', $bannerId) ?? $banners->first();
                }
            }
        } else {
            // No slot param: use session pointer rotation
            // If there's a principal and it hasn't been served yet this session, serve it first
            if ($principals->isNotEmpty() && ! $session->get($firstServedKey, false)) {
                $banner = $principals->sortByDesc('created_at')->first();
                $session->put($firstServedKey, true);
            } else {
                $order = $session->get($orderKey, []);
                if (empty($order)) {
                    $banner = $principals->first() ?? $banners->first();
                } else {
                    $idx = $session->get($indexKey, 0) % count($order);
                    $bannerId = $order[$idx];
                    $banner = $banners->firstWhere('id', $bannerId) ?? $banners->first();
                    // advance pointer
                    $session->put($indexKey, ($idx + 1) % count($order));
                }
            }
        }

        // 💡 Generar HTML con tracking contextual
        $html = $this->generateBannerHtml($banner, null, $zoneName, $siteDomain);

        // Determinar si el banner ya realiza su propio tracking de vista.
        $viewTracked = false;
        if ($banner->type === 'html') {
            $htmlCode = (string) ($banner->html_code ?? '');
            if ($htmlCode !== '' && (
                stripos($htmlCode, '/api/track/view') !== false ||
                stripos($htmlCode, 'data-banner-view-tracked') !== false
            )) {
                $viewTracked = true;
            }
        }

        return response()->json([
            'id'            => $banner->id,
            'assignment_id' => null,
            'html'          => $html,
            'view_tracked'  => $viewTracked,
        ]);
    }


    private function generateBannerHtml($banner, $assignment = null, $zone = null, $site = null)
    {
        $baseUrl = config('app.url');

        // Build tracking params; include 'redirect' only if banner has a destination
        $paramsArr = [
            'assignment' => $assignment->id ?? null,
            'zone'       => $zone,
            'site'       => $site,
        ];
        if (!empty($banner->link_url)) {
            $paramsArr['redirect'] = $banner->link_url;
        }
        $params = http_build_query($paramsArr);

        // === Tipo Imagen ===
        if ($banner->type === 'image') {
            $imgSrc = $baseUrl . Storage::url($banner->image_path);

            // Si no hay enlace destino, no envolver en <a> (no se permite acción al click)
            if (!empty($banner->link_url)) {
                $clickUrl = "{$baseUrl}/api/track/click/{$banner->id}?{$params}";
                $imgHtml = "<a href='{$clickUrl}' target='_blank'>
                    <img src='{$imgSrc}' style='width:100%;height:auto;transition:transform .3s ease-in-out;' onmouseover=\"this.style.transform='scale(1.05)'\" onmouseout=\"this.style.transform='scale(1)'\">
                </a>";
            } else {
                $imgHtml = "<img src='{$imgSrc}' style='width:100%;height:auto;transition:transform .3s ease-in-out;cursor:default;' onmouseover=\"this.style.transform='scale(1.05)'\" onmouseout=\"this.style.transform='scale(1)'\">";
            }

            return "{$imgHtml}";
        }

        // === Tipo Video ===
        elseif ($banner->type === 'video') {
            $videoSrc = $baseUrl . Storage::url($banner->video_path);

            $videoBlock = "<div style='position:relative;width:100%;'>
                <video controls autoplay muted loop style='width:100%;height:auto;border-radius:6px;'>
                    <source src='{$videoSrc}' type='video/mp4'>
                    Tu navegador no soporta video HTML5.
                </video>";

            // Solo añadir overlay clicable si existe enlace destino
            if (!empty($banner->link_url)) {
                $clickUrl = "{$baseUrl}/api/track/click/{$banner->id}?{$params}";
                $videoBlock .= "<a href='{$clickUrl}' target='_blank' style='position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;'></a>";
            }

            $videoBlock .= "</div>";

            return "{$videoBlock}";
        }

        // === Tipo HTML/Script ===
        else {
            // Si el banner es HTML/Script, devolvemos su código tal cual.
            // El tracking de vista lo realiza ahora `public/js/banner.js` para evitar duplicados.
            return $banner->html_code;
        }
    }


}
