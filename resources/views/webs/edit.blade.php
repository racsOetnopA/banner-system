@extends('layouts.admin')
@section('title', 'Editar Web')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-globe me-2"></i> Editar Web</h1>
    <a href="{{ route('webs.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <x-web-form :action="route('webs.update', $web)" method="PUT" :web="$web" />
    </div>
</div>
@endsection
