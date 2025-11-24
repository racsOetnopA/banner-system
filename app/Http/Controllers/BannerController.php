<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index() {
        $banners = Banner::with('zones')->withCount('zones')->latest()->paginate(12);
        return view('banners.index', compact('banners'));
    }

    public function create() {
        $zones = \App\Models\Zone::with('web')->orderBy('name')->get();
        return view('banners.create', compact('zones'));
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
        // sync zones
        $banner->zones()->sync($request->input('zones', []));
        return redirect()->route('banners.index')->with('success','Banner creado.');
    }

    public function edit(Banner $banner) {
        $zones = \App\Models\Zone::with('web')->orderBy('name')->get();
        return view('banners.edit', compact('banner','zones'));
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
        $banner->zones()->sync($request->input('zones', []));
        return redirect()->route('banners.index')->with('success','Banner actualizado.');
    }

    public function destroy(Banner $banner) {
        if ($banner->image_path) Storage::disk('public')->delete($banner->image_path);
        $banner->delete();
        return redirect()->route('banners.index')->with('success','Banner eliminado.');
    }
}
