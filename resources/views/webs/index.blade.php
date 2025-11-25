@extends('layouts.admin')
@section('title', 'Webs')

@section('content')
<style>
/* Tooltip color variants to match badge backgrounds (local override) */
.tooltip-info .tooltip-inner { background-color: var(--bs-info) !important; color: var(--bs-white, #fff) !important; border: none; max-width: 90vw; white-space: nowrap; }
.tooltip-secondary .tooltip-inner { background-color: var(--bs-secondary) !important; color: var(--bs-white, #fff) !important; border: none; max-width: 90vw; white-space: nowrap; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-globe me-2"></i> Webs</h1>
    <a href="{{ route('webs.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Nuevo
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <div class="mb-3">
            <input type="search" class="form-control table-filter" placeholder="Filtrar filas de la tabla..." aria-label="Filtrar sitios" data-target="#webs-table">
        </div>
        <table id="webs-table" class="table table-sm align-middle">
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
                    <td class="text-start">
                        @if($w->zones && $w->zones->isNotEmpty())
                            <div class="d-flex flex-column">
                                @foreach($w->zones as $z)
                                    @php
                                        $size = (!is_null($z->width) || !is_null($z->height)) ? trim(($z->width ?? '') . 'x' . ($z->height ?? '')) : null;
                                    @endphp
                                    <div class="mb-1">
                                        <small class="">
                                            {{-- Zona: Nombre - Tamaño --}}
                                            {{ $z->name }}@if($size) - {{ $size }}@endif
                                        </small>
                                        @if($z->banners && $z->banners->isNotEmpty())
                                            <div class="mb-1 ms-4">
                                                @foreach($z->banners as $b)
                                                    <small class="d-block">
                                                        <span class="text-muted">Banner: </span>
                                                        <strong>{{ $b->name }}</strong>
                                                        @if($b->active)
                                                            <span class="ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-success" title="Activo">
                                                                <i class="fas fa-star text-success"></i>
                                                            </span>
                                                        @else
                                                            <span class="ms-2" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger" title="Inactivo">
                                                                <i class="fas fa-star text-danger"></i>
                                                            </span>
                                                        @endif
                                                    </small>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="mt-1"><small class="text-muted">Sin Banner</small></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <small class="text-muted">Sin zonas</small>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('webs.edit', $w) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-warning" title="Editar">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        @if($w->zones && $w->zones->isNotEmpty())
                            <form action="{{ route('webs.forceDestroy', $w) }}" method="POST" class="d-inline" onsubmit="return confirm('Esta web tiene zonas y puede contener banners. ¿Eliminar también las zonas y relaciones? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger" title="Eliminar (forzar)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @else
                            <form action="{{ route('webs.destroy', $w) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este sitio?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
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
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => {
        try { if (el._tooltip) el._tooltip.dispose(); } catch(e){}
        el._tooltip = new bootstrap.Tooltip(el, { html: true, delay: { show: 0, hide: 100 } });
    });
});
</script>
@endpush
