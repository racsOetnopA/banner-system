@extends('layouts.admin')
@section('title', 'Nuevo Banner')

@section('content')
<div class="container-fluid">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">
      <i class="fas fa-image me-2"></i> Crear Nuevo Banner
    </h1>
    <a href="{{ route('banners.index') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Volver al listado">
      <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data">
        @csrf
        @include('banners.partials.form', ['banner' => null])
      </form>
    </div>
  </div>

</div>
@endsection
