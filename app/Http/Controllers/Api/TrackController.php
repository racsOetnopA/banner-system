<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BannerClick;
use App\Models\BannerView;
use App\Models\Assignment;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    /**
     * Registrar una vista (cuando el banner se carga).
     * Ruta: GET /api/track/view/{id}?assignment=&zone=&site=
     */
    public function view($id, Request $request)
    {
        $assignmentId = $request->query('assignment');
        $zone = $request->query('zone');
        $site = $request->query('site');

        $assignment = $assignmentId ? Assignment::find($assignmentId) : null;

        BannerView::create([
            'banner_id'     => $id,
            'assignment_id' => $assignment ? $assignment->id : null,
            'zone_id'       => $assignment ? $assignment->zone_id : null,
            'site_domain'   => $site,
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Registrar un clic (cuando el usuario hace clic en el banner).
     * Ruta: GET /api/track/click/{id}?assignment=&zone=&site=&redirect=
     */
    public function click($id, Request $request)
    {
        $assignmentId = $request->query('assignment');
        $zone = $request->query('zone');
        $site = $request->query('site');
        $redirect = $request->query('redirect');

        $assignment = $assignmentId ? Assignment::find($assignmentId) : null;

        BannerClick::create([
            'banner_id'     => $id,
            'assignment_id' => $assignment ? $assignment->id : null,
            'zone_id'       => $assignment ? $assignment->zone_id : null,
            'site_domain'   => $site,
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
        ]);

        return redirect($redirect ?? '/');
    }
}
