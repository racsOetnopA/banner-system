@extends('layouts.admin')
@section('title', 'Editar Asignación')

@section('content')
<div class="container-fluid">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-link me-2"></i> Editar Asignación</h1>
    <a href="{{ route('assignments.index') }}" class="btn btn-secondary" data-bs-toggle="tooltip" title="Volver al listado">
      <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('assignments.update', $assignment) }}">
        @csrf @method('PUT')
        @include('assignments.partials.form', ['assignment' => $assignment])
      </form>
    </div>
  </div>

</div>
@endsection
