<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Banner;
use App\Models\Zone;
use App\Http\Requests\StoreAssignmentRequest;

class AssignmentController extends Controller
{
    public function index(){
        $assignments = Assignment::with(['banner','zone'])->latest()->paginate(15);
        $zones = Zone::with('web')->orderBy('name')->get();
        return view('assignments.index', compact('assignments','zones'));
    }

    public function create(){
        $banners = Banner::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();
        return view('assignments.create', compact('banners','zones'));
    }

    public function store(StoreAssignmentRequest $request){
        Assignment::create($request->validated());
        return redirect()->route('assignments.index')->with('success','Asignación creada.');
    }

    public function edit(Assignment $assignment){
        $banners = Banner::orderBy('name')->get();
        $zones = Zone::orderBy('name')->get();
        return view('assignments.edit', compact('assignment','banners','zones'));
    }

    public function update(StoreAssignmentRequest $request, Assignment $assignment){
        $assignment->update($request->validated());
        return redirect()->route('assignments.index')->with('success','Asignación actualizada.');
    }

    public function destroy(Assignment $assignment){
        $assignment->delete();
        return redirect()->route('assignments.index')->with('success','Asignación eliminada.');
    }
}
