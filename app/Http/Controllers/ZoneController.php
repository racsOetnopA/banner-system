<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Http\Requests\StoreZoneRequest;
use App\Models\Web;

class ZoneController extends Controller
{
    public function index(){ $zones = Zone::latest()->paginate(12); return view('zones.index', compact('zones')); }
    public function create(){ $webs = Web::orderBy('site_domain')->get(); return view('zones.create', compact('webs')); }
    public function store(StoreZoneRequest $request){ Zone::create($request->validated()); return redirect()->route('zones.index')->with('success','Zona creada.'); }
    public function edit(Zone $zone){ $webs = Web::orderBy('site_domain')->get(); return view('zones.edit', compact('zone','webs')); }
    public function update(StoreZoneRequest $request, Zone $zone){ $zone->update($request->validated()); return redirect()->route('zones.index')->with('success','Zona actualizada.'); }
    public function destroy(Zone $zone){ $zone->delete(); return redirect()->route('zones.index')->with('success','Zona eliminada.'); }
}
