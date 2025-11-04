<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

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

        // SIN zonas (no hay datos suficientes aún)
        $viewsByZone = collect(); // deja vacío de momento

        return view('dashboard.index', compact(
            'totalViews','totalClicks','ctr',
            'viewsByDay','clicksByDay',
            'viewsByZone','viewsByBanner','clicksByBanner'
        ));
    }
}
