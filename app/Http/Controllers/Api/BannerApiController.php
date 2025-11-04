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

        $assignments = Assignment::with('banner')
            ->where('zone_id', $zone->id)
            ->where('site_domain', $siteDomain)
            ->whereHas('banner', function ($q) {
                $q->where('active', true)
                ->where(function ($q) {
                    $now = now();
                    $q->whereNull('start_date')
                        ->orWhere('start_date', '<=', $now);
                })
                ->where(function ($q) {
                    $now = now();
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', $now);
                });
            })
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json(['error' => 'No banners available'], 404);
        }

        // 🌀 Rotación aleatoria de banners disponibles
        $assignment = $assignments->random();
        $banner = $assignment->banner;

        // 💡 Generar HTML con tracking contextual
        $html = $this->generateBannerHtml(
            $banner,
            $assignment,
            $zoneName,
            $siteDomain
        );

        return response()->json([
            'id'            => $banner->id,
            'assignment_id' => $assignment->id,
            'html'          => $html,
        ]);
    }


    private function generateBannerHtml($banner, $assignment = null, $zone = null, $site = null)
    {
        $baseUrl  = config('app.url');
        $redirect = $banner->link_url ? urlencode($banner->link_url) : '#';

        // 🔗 Parámetros de tracking comunes
        $params = http_build_query([
            'assignment' => $assignment->id ?? null,
            'zone'       => $zone,
            'site'       => $site,
            'redirect'   => $redirect,
        ]);

        // === Tipo Imagen ===
        if ($banner->type === 'image') {
            $imgSrc = $baseUrl . Storage::url($banner->image_path);

            return "
            <a href='{$baseUrl}/api/track/click/{$banner->id}?{$params}' target='_blank'>
                <img src='{$imgSrc}'
                    style='width:100%;height:auto;transition:transform .3s ease-in-out;'
                    onmouseover='this.style.transform=\"scale(1.05)\"'
                    onmouseout='this.style.transform=\"scale(1)\"'>
            </a>

            <!-- Script de tracking de vista -->
            <script>
                fetch('{$baseUrl}/api/track/view/{$banner->id}?{$params}')
                    .catch(() => {});
            </script>
            ";
        }

        // === Tipo Video ===
        elseif ($banner->type === 'video') {
            $videoSrc = $baseUrl . Storage::url($banner->video_path);

            return "
            <div style='position:relative;width:100%;'>
                <video controls autoplay muted loop style='width:100%;height:auto;border-radius:6px;'>
                    <source src='{$videoSrc}' type='video/mp4'>
                    Tu navegador no soporta video HTML5.
                </video>
                <a href='{$baseUrl}/api/track/click/{$banner->id}?{$params}'
                target='_blank'
                style='position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;'></a>
            </div>

            <!-- Script de tracking de vista -->
            <script>
                fetch('{$baseUrl}/api/track/view/{$banner->id}?{$params}')
                    .catch(() => {});
            </script>
            ";
        }

        // === Tipo HTML/Script ===
        else {
            // Inyectar tracking de vista también aquí
            $htmlCode = $banner->html_code;
            $trackJs  = "<script>fetch('{$baseUrl}/api/track/view/{$banner->id}?{$params}').catch(()=>{});</script>";
            return $htmlCode . $trackJs;
        }
    }


}
