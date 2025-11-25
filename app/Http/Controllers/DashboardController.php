<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\Web;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Totales
        $totalViews  = DB::table('banner_views')->count();
        $totalClicks = DB::table('banner_clicks')->count();
        $ctr = $totalViews > 0 ? round($totalClicks / $totalViews * 100, 2) : 0;

        // Series (últimos 7 días)
        $viewsByDay = DB::table('banner_views')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')->orderBy('date')->get();

        $clicksByDay = DB::table('banner_clicks')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')->orderBy('date')->get();

        // Top banners
        $viewsByBanner = DB::table('banner_views')
            ->selectRaw('banner_id, COUNT(*) as total')
            ->groupBy('banner_id')->orderByDesc('total')->limit(10)->get();

        $clicksByBanner = DB::table('banner_clicks')
            ->selectRaw('banner_id, COUNT(*) as total')
            ->groupBy('banner_id')->orderByDesc('total')->limit(10)->get();

        // zonas (por defecto para el donut) - sumarizar por zona global
        $viewsByZone = DB::table('banner_zone')
            ->join('banner_views','banner_zone.banner_id','=','banner_views.banner_id')
            ->join('zones','zones.id','=','banner_zone.zone_id')
            ->selectRaw('zones.id, zones.name, COUNT(*) as total')
            ->groupBy('zones.id','zones.name')
            ->orderByDesc('total')
            ->get();

        // lista de webs para selector
        $webs = Web::orderBy('site_domain')->get();

        return view('dashboard.index', compact(
            'totalViews','totalClicks','ctr',
            'viewsByDay','clicksByDay',
            'viewsByZone','viewsByBanner','clicksByBanner','webs'
        ));
    }

    /**
     * Devuelve estadísticas por zona para un sitio y rango de fechas.
     * Parámetros: web_id, start (Y-m-d), end (Y-m-d)
     */
    public function zoneStats(Request $request)
    {
        $webId = $request->query('web_id');
        $start = $request->query('start');
        $end = $request->query('end');

        $startDt = $start ? Carbon::parse($start)->startOfDay() : null;
        $endDt = $end ? Carbon::parse($end)->endOfDay() : null;

        // obtener zonas del sitio
        $zones = Zone::where('web_id', $webId)->orderBy('name')->get();

        // contar vistas por zona
        $viewsQuery = DB::table('banner_zone')
            ->join('banner_views','banner_zone.banner_id','=','banner_views.banner_id')
            ->join('zones','zones.id','=','banner_zone.zone_id')
            ->select('zones.id','zones.name', DB::raw('COUNT(*) as views'))
            ->where('zones.web_id', $webId);
        if ($startDt) $viewsQuery->where('banner_views.created_at', '>=', $startDt);
        if ($endDt) $viewsQuery->where('banner_views.created_at', '<=', $endDt);
        $views = $viewsQuery->groupBy('zones.id','zones.name')->get()->keyBy('id');

        // contar clicks por zona
        $clicksQuery = DB::table('banner_zone')
            ->join('banner_clicks','banner_zone.banner_id','=','banner_clicks.banner_id')
            ->join('zones','zones.id','=','banner_zone.zone_id')
            ->select('zones.id','zones.name', DB::raw('COUNT(*) as clicks'))
            ->where('zones.web_id', $webId);
        if ($startDt) $clicksQuery->where('banner_clicks.created_at', '>=', $startDt);
        if ($endDt) $clicksQuery->where('banner_clicks.created_at', '<=', $endDt);
        $clicks = $clicksQuery->groupBy('zones.id','zones.name')->get()->keyBy('id');

        $data = $zones->map(function($z) use ($views, $clicks) {
            $v = $views->has($z->id) ? (int)$views->get($z->id)->views : 0;
            $c = $clicks->has($z->id) ? (int)$clicks->get($z->id)->clicks : 0;
            return [
                'id' => $z->id,
                'name' => $z->name,
                'views' => $v,
                'clicks' => $c,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * Devuelve series de Vistas y Clics por día, filtrables por sitio y rango.
     * Parámetros: web_id, start (Y-m-d), end (Y-m-d)
     */
    public function series(Request $request)
    {
        $webId = $request->query('web_id');
        $start = $request->query('start');
        $end = $request->query('end');

        $startDt = $start ? Carbon::parse($start)->startOfDay() : now()->subDays(6)->startOfDay();
        $endDt = $end ? Carbon::parse($end)->endOfDay() : now()->endOfDay();

        // Vistas por día
        $viewsQuery = DB::table('banner_views as bv')
            ->selectRaw('DATE(bv.created_at) as date, COUNT(*) as total')
            ->whereBetween('bv.created_at', [$startDt, $endDt]);

        // Clics por día
        $clicksQuery = DB::table('banner_clicks as bc')
            ->selectRaw('DATE(bc.created_at) as date, COUNT(*) as total')
            ->whereBetween('bc.created_at', [$startDt, $endDt]);

        if ($webId) {
            // aplicar join para filtrar por web_id (a través de banner_zone -> zones)
            $viewsQuery->join('banner_zone as bz', 'bz.banner_id', '=', 'bv.banner_id')
                ->join('zones as z', 'z.id', '=', 'bz.zone_id')
                ->where('z.web_id', $webId);

            $clicksQuery->join('banner_zone as bz', 'bz.banner_id', '=', 'bc.banner_id')
                ->join('zones as z', 'z.id', '=', 'bz.zone_id')
                ->where('z.web_id', $webId);
        }

        $views = $viewsQuery->groupBy('date')->orderBy('date')->get();
        $clicks = $clicksQuery->groupBy('date')->orderBy('date')->get();

        return response()->json([
            'views' => $views,
            'clicks' => $clicks,
        ]);
    }
}
