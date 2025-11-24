@extends('layouts.admin')
@section('title', 'Webs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-globe me-2"></i> Webs</h1>
    <a href="{{ route('webs.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nuevo
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Dominio</th>
                    <th class="text-center">Zonas</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($webs as $w)
                <tr>
                    <td>{{ $w->id }}</td>
                    <td>{{ $w->site_domain }}</td>
                    <td class="text-center">
                        @if(isset($w->zones_count))
                            @php $names = $w->zones->pluck('name')->filter()->values()->all(); @endphp
                            <span class="badge bg-info" data-bs-toggle="tooltip" data-bs-html="false" title="{{ e(implode(', ', $names) ?: 'Sin zonas') }}">{{ $w->zones_count }}</span>
                        @else
                            <span class="badge bg-secondary">0</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('webs.edit', $w) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar web">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        <form action="{{ route('webs.destroy', $w) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este sitio?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">No hay sitios registrados aún.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $webs->links() }}
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
