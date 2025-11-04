<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Assignment;

class TrackController extends Controller
{
    /**
     * Registrar una vista (cuando se carga el banner).
     */
    public function view(Request $request)
    {
        // Validar datos mínimos
        $data = $request->validate([
            'banner_id'     => 'required|integer|exists:banners,id',
            'assignment_id' => 'nullable|integer|exists:assignments,id',
            'zone'          => 'nullable|string',
            'site'          => 'nullable|string',
        ]);

        // Buscar la asignación si está disponible
        $assignment = $data['assignment_id']
            ? Assignment::find($data['assignment_id'])
            : null;

        // Insertar registro de vista
        DB::table('banner_views')->insert([
            'banner_id'     => $data['banner_id'],
            'assignment_id' => $assignment ? $assignment->id : null,
            'zone_id'       => $assignment ? $assignment->zone_id : null,
            'site_domain'   => $data['site'] ?? null,
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->noContent();
    }

    /**
     * Registrar un clic (cuando el usuario hace clic en el banner).
     */
    public function click(Request $request, $bannerId)
    {
        $assignmentId = $request->query('assignment');
        $assignment   = $assignmentId ? Assignment::find($assignmentId) : null;

        // Guardar click
        DB::table('banner_clicks')->insert([
            'banner_id'     => $bannerId,
            'assignment_id' => $assignment ? $assignment->id : null,
            'zone_id'       => $assignment ? $assignment->zone_id : null,
            'site_domain'   => $request->query('site'),
            'ip'            => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Redirigir al destino real
        $redirect = $request->query('redirect');
        return $redirect ? redirect($redirect) : redirect('/');
    }
}
