@extends('layouts.admin')
@section('title', 'Asignaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-link me-2"></i> Asignaciones</h1>
    <a href="{{ route('assignments.create') }}" class="btn btn-primary" data-bs-toggle="tooltip" title="Crear nueva asignación">
        <i class="fas fa-plus me-1"></i> Nueva
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Banner</th>
                    <th>Zona</th>
                    <th>Dominio</th>
                    <th>Rotación</th>
                    <th class="text-center">Peso</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($assignments as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>
                        <i class="fas fa-image text-info me-1"></i>
                        {{ $a->banner->name ?? '—' }}
                    </td>
                    <td>
                        <i class="fas fa-layer-group text-secondary me-1"></i>
                        {{ $a->zone->name ?? '—' }}
                    </td>
                    <td>
                        <i class="fas fa-globe text-muted me-1"></i>
                        {{ $a->site_domain }}
                    </td>
                    <td>
                        @if($a->rotation_mode === 'random')
                            <span class="badge bg-info" data-bs-toggle="tooltip" title="Rotación aleatoria"><i class="fas fa-random"></i> Random</span>
                        @else
                            <span class="badge bg-primary" data-bs-toggle="tooltip" title="Rotación secuencial"><i class="fas fa-sync"></i> Secuencial</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-light text-dark">{{ $a->weight }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('assignments.edit', $a) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar asignación">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('assignments.destroy', $a) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta asignación?')" data-bs-toggle="tooltip" title="Eliminar asignación">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay asignaciones registradas aún.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $assignments->links() }}
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
