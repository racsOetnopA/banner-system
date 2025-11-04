<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Http\Requests\StoreZoneRequest;

class ZoneController extends Controller
{
    public function index(){ $zones = Zone::latest()->paginate(12); return view('zones.index', compact('zones')); }
    public function create(){ return view('zones.create'); }
    public function store(StoreZoneRequest $request){ Zone::create($request->validated()); return redirect()->route('zones.index')->with('success','Zona creada.'); }
    public function edit(Zone $zone){ return view('zones.edit', compact('zone')); }
    public function update(StoreZoneRequest $request, Zone $zone){ $zone->update($request->validated()); return redirect()->route('zones.index')->with('success','Zona actualizada.'); }
    public function destroy(Zone $zone){ $zone->delete(); return redirect()->route('zones.index')->with('success','Zona eliminada.'); }
}
