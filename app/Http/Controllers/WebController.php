<?php

namespace App\Http\Controllers;

use App\Models\Web;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index()
    {
        $webs = Web::with(['zones' => function($q){ $q->select('id','name','web_id'); }])->withCount('zones')->latest()->paginate(15);
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
        $web->delete();
        return redirect()->route('webs.index')->with('success', 'Web eliminada.');
    }
}
