<?php

namespace App\Http\Controllers;

use App\Models\Web;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebController extends Controller
{
    public function index()
    {
                // Cargar todas las zonas y eager-load de banners (id, name, active)
                $webs = Web::with(['zones' => function($q){
                        $q->select('id','name','web_id','width','height')
                            ->with(['banners' => function($qb){ $qb->select('banners.id','banners.name','banners.active'); }]);
                }])->withCount('zones')->latest()->paginate(15);
        return view('webs.index', compact('webs'));
    }

    public function create()
    {
        return view('webs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'site_domain' => 'required|string|max:255|unique:webs,site_domain',
        ]);
        Web::create($data);
        return redirect()->route('webs.index')->with('success', 'Web creada.');
    }

    public function edit(Web $web)
    {
        return view('webs.edit', compact('web'));
    }

    public function update(Request $request, Web $web)
    {
        $data = $request->validate([
            'site_domain' => 'required|string|max:255|unique:webs,site_domain,' . $web->id,
        ]);
        $web->update($data);
        return redirect()->route('webs.index')->with('success', 'Web actualizada.');
    }

    public function destroy(Web $web)
    {
        // Evitar error de constraint: si la web tiene zonas relacionadas no se
        // puede eliminar. Informar al usuario en lugar de lanzar excepción SQL.
        $zonesCount = $web->zones()->count();
        if ($zonesCount > 0) {
            return redirect()->route('webs.index')
                ->with('error', "No se puede eliminar esta web porque tiene {$zonesCount} zona(s) asociada(s). Elimina o reasigna las zonas antes.");
        }

        $web->delete();
        return redirect()->route('webs.index')->with('success', 'Web eliminada.');
    }

    /**
     * Force delete a Web and its dependent Zones and pivot relations with Banners.
     * This action deletes related records (banner_zone) for the zones of the web,
     * then deletes the zones and finally the web itself inside a DB transaction.
     */
    public function forceDestroy(Web $web)
    {
        DB::transaction(function () use ($web) {
            // Obtener ids de las zonas relacionadas
            $zoneIds = $web->zones()->pluck('id')->all();

            if (!empty($zoneIds)) {
                // Borrar relaciones pivot banner_zone para esas zonas
                DB::table('banner_zone')->whereIn('zone_id', $zoneIds)->delete();

                // Borrar las zonas
                Zone::whereIn('id', $zoneIds)->delete();
            }

            // Finalmente borrar la web
            $web->delete();
        });

        return redirect()->route('webs.index')->with('success', 'Web y sus zonas relacionadas eliminadas.');
    }
}
