<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index() {
        // Eager-load zones with width/height and the related web so tooltips can show measurements and site
        // Use fully-qualified column names to avoid ambiguous `id` when joining the pivot table
        $banners = Banner::with([
            'zones' => function($q){
                $q->select('zones.id as id','zones.name','zones.web_id','zones.width','zones.height');
            },
            'zones.web'
        ])->withCount('zones')->latest()->paginate(12);
        return view('banners.index', compact('banners'));
    }

    public function create() {
        $zones = \App\Models\Zone::with('web')->orderBy('name')->get();
        // zones that already have a principal banner active
        $zonesWithPrincipal = DB::table('banner_zone')
            ->join('banners','banners.id','=','banner_zone.banner_id')
            ->where('banner_zone.principal', 1)
            ->where('banners.active', 1)
            ->pluck('banner_zone.zone_id')
            ->unique()
            ->values()
            ->all();

        return view('banners.create', compact('zones','zonesWithPrincipal'));
    }

    public function store(StoreBannerRequest $request) {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if ($data['type'] === 'video' && $request->hasFile('video')) {
            $data['video_path'] = $request->file('video')->store('banners/videos', 'public');
            $data['image_path'] = $data['html_code'] = null;
        } elseif ($data['type'] === 'image' && $request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('banners', 'public');
            $data['video_path'] = $data['html_code'] = null;
        } elseif ($data['type'] === 'html') {
            $data['video_path'] = $data['image_path'] = null;
        }

        $banner = Banner::create($data);
        // sync zones and set pivot principal according to the 'principal' checkbox
        $zones = $request->input('zones', []);
        $isPrincipal = $request->boolean('principal');

        if ($isPrincipal && !empty($zones)) {
            // For each selected zone, remove principal flag from any other banner in that zone
            DB::table('banner_zone')->whereIn('zone_id', $zones)->update(['principal' => false]);
        }

        $sync = [];
        foreach ($zones as $z) {
            $sync[$z] = ['principal' => $isPrincipal];
        }
        $banner->zones()->sync($sync);
        return redirect()->route('banners.index')->with('success','Banner creado.');
    }

    public function edit(Banner $banner) {
        $zones = \App\Models\Zone::with('web')->orderBy('name')->get();
        // zones that already have a principal banner active, excluding this banner
        $zonesWithPrincipal = DB::table('banner_zone')
            ->join('banners','banners.id','=','banner_zone.banner_id')
            ->where('banner_zone.principal', 1)
            ->where('banners.active', 1)
            ->where('banner_zone.banner_id', '!=', $banner->id)
            ->pluck('banner_zone.zone_id')
            ->unique()
            ->values()
            ->all();

        return view('banners.edit', compact('banner','zones','zonesWithPrincipal'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner) {
        $data = $request->validated();
        $data['active'] = $request->boolean('active');

        if (($data['type'] ?? null) === 'image' && $request->hasFile('image')) {
            if ($banner->image_path) Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
            $data['html_code'] = null;
        } elseif (($data['type'] ?? null) === 'html') {
            if ($banner->image_path) Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = null;
        }

        $banner->update($data);
        $zones = $request->input('zones', []);
        $isPrincipal = $request->boolean('principal');

        if ($isPrincipal && !empty($zones)) {
            // Remove principal flag from other banners in the selected zones
            DB::table('banner_zone')->whereIn('zone_id', $zones)->where('banner_id', '!=', $banner->id)->update(['principal' => false]);
        }

        $sync = [];
        foreach ($zones as $z) {
            $sync[$z] = ['principal' => $isPrincipal];
        }
        $banner->zones()->sync($sync);
        return redirect()->route('banners.index')->with('success','Banner actualizado.');
    }

    public function destroy(Banner $banner) {
        if ($banner->image_path) Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return redirect()->route('banners.index')->with('success','Banner eliminado.');
    }

    /**
     * Toggle the principal flag for a given banner-zone association.
     * Expects AJAX POST with boolean 'principal'. Enforces single principal per zone.
     */
    public function togglePrincipal(Request $request, Banner $banner, \App\Models\Zone $zone)
    {
        $makePrincipal = $request->boolean('principal');

        // Ensure the banner is associated with the zone
        $exists = DB::table('banner_zone')
            ->where('banner_id', $banner->id)
            ->where('zone_id', $zone->id)
            ->exists();

        if (! $exists) {
            return response()->json(['status' => 'error', 'message' => 'El banner no está asignado a la zona.'], 400);
        }

        if ($makePrincipal) {
            // Clear principal flag from any other banner in that zone
            DB::table('banner_zone')->where('zone_id', $zone->id)->update(['principal' => false]);
            // Set this association as principal
            DB::table('banner_zone')
                ->where('banner_id', $banner->id)
                ->where('zone_id', $zone->id)
                ->update(['principal' => true]);
        } else {
            // Remove principal flag from this association
            DB::table('banner_zone')
                ->where('banner_id', $banner->id)
                ->where('zone_id', $zone->id)
                ->update(['principal' => false]);
        }

        return response()->json(['status' => 'ok', 'principal' => $makePrincipal]);
    }
}
