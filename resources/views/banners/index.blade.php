@extends('layouts.admin')


<style>
.table.table-sm thead tr > th {
    text-align: center !important;
}

/* Tooltip color variants to match badge backgrounds */
.tooltip-info .tooltip-inner {
    background-color: var(--bs-info) !important;
    color: var(--bs-white, #fff) !important;
    border: none;
}
.tooltip-secondary .tooltip-inner {
    background-color: var(--bs-secondary) !important;
    color: var(--bs-white, #fff) !important;
    border: none;
}
</style>

@section('title', 'Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-image me-2"></i> Banners</h1>
    <a href="{{ route('banners.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-1"></i> Nuevo
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Activo</th>
                    <th >Zonas</th>
                    <th>Fechas</th>
                    <th >Vista previa</th>
                    <th style="text-align: end !important">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($banners as $b)
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>{{ $b->name }}</td>
                    <td class="text-center">
                        @if($b->type === 'image')
                            <span class="badge bg-info"><i class="fas fa-image me-1"></i> Imagen</span>
                        @elseif($b->type === 'video')
                            <span class="badge bg-primary"><i class="fas fa-video me-1"></i> Video</span>
                        @elseif($b->type === 'html')
                            <span class="badge bg-warning text-dark"><i class="fas fa-code me-1"></i> HTML</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {!! $b->active
                            ? '<span class="badge bg-success">Sí</span>'
                            : '<span class="badge bg-danger">No</span>' !!}
                    </td>
                    <td class="text-center">
                        @if(isset($b->zones_count))
                            @php
                                $zoneInfo = $b->zones->map(function($z){
                                    $site = $z->web->site_domain ?? '—';
                                    return trim($z->name . ' - ' . $site);
                                })->filter()->values()->all();
                                // escape each item but join with <br> for line breaks
                                $escaped = array_map('e', $zoneInfo);
                                $tooltipHtml = $escaped ? implode('<br>', $escaped) : 'Sin zonas';
                            @endphp
                            <span class="badge bg-info"
                                  data-bs-toggle="tooltip"
                                  data-bs-html="true"
                                  data-bs-custom-class="tooltip-info"
                                  title="{!! $tooltipHtml !!}">{{ $b->zones_count }}</span>
                        @else
                            <span class="badge bg-secondary" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-secondary" title="Sin zonas">0</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($b->start_date)
                            {{ $b->start_date->format('Y-m-d') }}
                        @endif
                        —
                        @if($b->end_date)
                            {{ $b->end_date->format('Y-m-d') }}
                        @endif
                    </td>

                    {{-- Vista previa dinámica --}}
                    <td class="text-center" style="width:120px;">
                        <div class="d-flex justify-content-center align-items-center" style="height:50px;">
                        @if($b->type === 'image' && $b->image_path)
                            <img src="{{ Storage::url($b->image_path) }}"
                                 class="rounded shadow-sm d-block"
                                 style="height:50px;max-width:100px;object-fit:cover;"
                                 alt="Imagen del banner">
                        @elseif($b->type === 'video' && $b->video_path)
                            <video src="{{ Storage::url($b->video_path) }}"
                                   class="rounded shadow-sm d-block"
                                   style="height:50px;max-width:100px;object-fit:cover;"
                                   muted
                                   onmouseover="this.play()" onmouseout="this.pause()"></video>
                        @elseif($b->type === 'html')
                            <span class="text-muted small">[HTML / Script]</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                        </div>
                    </td>

                    {{-- Acciones --}}
                    <td class="text-end">
                        <a href="{{ route('banners.edit', $b) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar banner">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        <form action="{{ route('banners.destroy', $b) }}" method="POST"
                              class="d-inline" onsubmit="return confirm('¿Eliminar este banner?')" data-bs-toggle="tooltip" title="Eliminar banner">
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
                        No hay banners registrados aún.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        <div class="mt-3">
            {{ $banners->links() }}
        </div>
    </div>
</div>
@endsection

            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Re-initialize tooltips for this page to ensure html:true and custom classes are applied
                const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(el => {
                    try {
                        // Dispose existing tooltip instance if any
                        if (el._tooltip) {
                            el._tooltip.dispose();
                        }
                    } catch(e) {}
                    // Create tooltip with html enabled and no show delay
                    const tip = new bootstrap.Tooltip(el, { html: true, delay: { show: 0, hide: 100 } });
                    // store reference to allow disposal later
                    el._tooltip = tip;
                });
            });
            </script>
            @endpush
