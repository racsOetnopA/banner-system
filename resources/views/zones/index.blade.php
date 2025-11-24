@extends('layouts.admin')
@section('title', 'Zonas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-layer-group me-2"></i> Zonas</h1>
    <a href="{{ route('zones.create') }}" class="btn btn-success" data-bs-toggle="tooltip" title="Crear nueva zona">
        <i class="fas fa-plus me-1"></i> Nueva
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Tamaño</th>
                    <th class="text-center">Sitio</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($zones as $z)
                <tr>
                    <td>{{ $z->id }}</td>
                    <td>
                        <i class="fas fa-layer-group text-primary me-1"></i>
                        <strong>{{ $z->name }}</strong>
                    </td>
                    <td>{{ $z->description ?? '—' }}</td>
                    <td class="text-center">
                        @if($z->width && $z->height)
                            {{ $z->width }}×{{ $z->height }} px
                        @else
                            <span class="text-muted small">Auto</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $z->web->site_domain ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('zones.edit', $z) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar zona">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        <form action="{{ route('zones.destroy', $z) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta zona?')" data-bs-toggle="tooltip" title="Eliminar zona">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No hay zonas registradas aún.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $zones->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
});
</script>
@endpush
