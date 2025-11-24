@extends('layouts.admin')
@section('title', 'Crear Web')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-globe me-2"></i> Crear Web</h1>
    <a href="{{ route('webs.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <x-web-form :action="route('webs.store')" method="POST" />
    </div>
</div>
@endsection
