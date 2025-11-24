@extends('layouts.admin')
@section('title', 'Webs')

@section('content')
<style>
/* Tooltip color variants to match badge backgrounds (local override) */
.tooltip-info .tooltip-inner { background-color: var(--bs-info) !important; color: var(--bs-white, #fff) !important; border: none; }
.tooltip-secondary .tooltip-inner { background-color: var(--bs-secondary) !important; color: var(--bs-white, #fff) !important; border: none; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-globe me-2"></i> Webs</h1>
    <a href="{{ route('webs.create') }}" class="btn btn-success">
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
                            @php
                                $zoneInfo = $w->zones->map(function($z){
                                    $site = $z->web->site_domain ?? '—';
                                    return trim($z->name . ' - ' . $site);
                                })->filter()->values()->all();
                                $escaped = array_map('e', $zoneInfo);
                                $tooltipHtml = $escaped ? implode('<br>', $escaped) : 'Sin zonas';
                            @endphp
                            <span class="badge bg-info"
                                  data-bs-custom-class="tooltip-info"
                                  data-bs-toggle="tooltip"
                                  data-bs-html="true"
                                  title="{!! $tooltipHtml !!}">{{ $w->zones_count }}</span>
                        @else
                            <span class="badge bg-secondary" data-bs-custom-class="tooltip-secondary" data-bs-toggle="tooltip" title="Sin zonas">0</span>
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
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => {
        try { if (el._tooltip) el._tooltip.dispose(); } catch(e){}
        el._tooltip = new bootstrap.Tooltip(el, { html: true, delay: { show: 0, hide: 100 } });
    });
});
</script>
@endpush
