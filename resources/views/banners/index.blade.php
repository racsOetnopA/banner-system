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
    max-width: 90vw;
    white-space: nowrap;
}
.tooltip-secondary .tooltip-inner {
    background-color: var(--bs-secondary) !important;
    color: var(--bs-white, #fff) !important;
    border: none;
    max-width: 90vw;
    white-space: nowrap;
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
                    <th>Zonas</th>
                    <th>Activo</th>
                    <th>Fechas</th>
                    <th>Tipo</th>
                    <th>Vista previa</th>
                    <th style="text-align: end !important">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($banners as $b)
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>{{ $b->name }}</td>
                    <td class="text-start">
                        @if($b->zones->isNotEmpty())
                            <div class="d-flex flex-column">
                                @foreach($b->zones as $z)
                                    @php
                                        $site = $z->web->site_domain ?? '—';
                                        $size = (!is_null($z->width) || !is_null($z->height)) ? trim(($z->width ?? '') . 'x' . ($z->height ?? '')) : null;
                                        $parts = [$z->name];
                                        if ($size) $parts[] = $size;
                                        if ($site) $parts[] = $site;
                                        $label = trim(implode(' - ', $parts));
                                    @endphp
                                    <div class="mb-1 d-flex justify-content-between" style="gap:8px;">
                                        <small class="text-muted">{{ $label }}</small>
                                        <button type="button"
                                                class="btn btn-sm btn-toggle-principal"
                                                data-bs-toggle="tooltip"
                                                data-bs-custom-class="tooltip-light"
                                                title="{{ ($z->pivot->principal ?? false) ? 'Quitar Principal' : 'Hacer Principal' }}"
                                                data-url="{{ route('banners.toggle-principal', ['banner' => $b->id, 'zone' => $z->id]) }}"
                                                data-zone-id="{{ $z->id }}"
                                                data-banner-id="{{ $b->id }}"
                                                data-principal="{{ ($z->pivot->principal ?? false) ? 1 : 0 }}">
                                            @if($z->pivot->principal ?? false)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small">Sin zonas</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {!! $b->active
                            ? '<span class="badge bg-success">Sí</span>'
                            : '<span class="badge bg-danger">No</span>' !!}
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
                    <td class="text-center">
                        @if($b->type === 'image')
                            <span class="badge bg-info"><i class="fas fa-image me-1"></i> Imagen</span>
                        @elseif($b->type === 'video')
                            <span class="badge bg-primary"><i class="fas fa-video me-1"></i> Video</span>
                        @elseif($b->type === 'html')
                            <span class="badge bg-warning text-dark"><i class="fas fa-code me-1"></i> HTML</span>
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
                        <a href="{{ route('banners.edit', $b) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-warning" title="Editar">
                            <i class="fas fa-edit text-white"></i>
                        </a>
                        <form action="{{ route('banners.destroy', $b) }}" method="POST"
                              class="d-inline" onsubmit="return confirm('¿Eliminar este banner?')" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger" title="Eliminar">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
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

                // Handle toggle principal buttons (AJAX)
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : null;

                function updateZoneButtons(zoneId, bannerIdThatIsPrincipal) {
                    // For the given zoneId, mark all buttons: the one matching bannerIdThatIsPrincipal becomes principal
                    document.querySelectorAll('.btn-toggle-principal[data-zone-id="' + zoneId + '"]').forEach(btn => {
                        const bId = btn.getAttribute('data-banner-id');
                        if (bId === String(bannerIdThatIsPrincipal)) {
                            btn.setAttribute('data-principal', '1');
                            // btn.classList.remove('btn-outline-secondary');
                            // btn.classList.add('btn-success');
                            // set icon only and tooltip
                            btn.innerHTML = '<i class="fas fa-star text-warning"></i>';
                            btn.setAttribute('title', 'Quitar Principal');
                        } else {
                            btn.setAttribute('data-principal', '0');
                            // btn.classList.remove('btn-success');
                            // btn.classList.add('btn-outline-secondary');
                            btn.innerHTML = '<i class="far fa-star"></i>';
                            btn.setAttribute('title', 'Hacer Principal');
                        }
                        // Rebuild tooltip to pick new title
                        try { if (btn._tooltip) btn._tooltip.dispose(); } catch(e) {}
                        btn._tooltip = new bootstrap.Tooltip(btn, { delay: { show: 0, hide: 100 } });
                    });
                }

                document.querySelectorAll('.btn-toggle-principal').forEach(btn => {
                    // initialize button styling based on current state
                    const isP = btn.getAttribute('data-principal') === '1';
                    if (isP) {
                        // btn.classList.add('btn-success');
                        btn.classList.add('btn-outline-light');
                        // ensure icon and title reflect state
                        btn.innerHTML = '<i class="fas fa-star text-warning"></i>';
                        btn.setAttribute('title', 'Quitar Principal');
                    } else {
                        btn.classList.add('btn-outline-light');
                        btn.innerHTML = '<i class="far fa-star"></i>';
                        btn.setAttribute('title', 'Hacer Principal');
                    }

                    btn.addEventListener('click', async (e) => {
                        e.preventDefault();
                        const url = btn.getAttribute('data-url');
                        const zoneId = btn.getAttribute('data-zone-id');
                        const bannerId = btn.getAttribute('data-banner-id');
                        const currently = btn.getAttribute('data-principal') === '1';
                        const desired = currently ? 0 : 1;

                        btn.disabled = true;
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ principal: desired })
                        }).catch(err => null);

                        btn.disabled = false;
                        if (!res) { alert('Error de red.'); return; }
                        const data = await res.json().catch(() => null);
                        if (!data || data.status !== 'ok') {
                            alert(data?.message || 'Error al actualizar principal');
                            return;
                        }

                        if (desired === 1) {
                            // This banner became principal for the zone: update all buttons for that zone
                            updateZoneButtons(zoneId, bannerId);
                        } else {
                            // Unset principal on this banner for zone
                            btn.setAttribute('data-principal', '0');
                            btn.classList.remove('btn-success');
                            btn.classList.add('btn-outline-secondary');
                            btn.innerHTML = '<i class="far fa-star"></i>';
                            btn.setAttribute('title', 'Hacer Principal');
                            try { if (btn._tooltip) btn._tooltip.dispose(); } catch(e) {}
                            btn._tooltip = new bootstrap.Tooltip(btn, { delay: { show: 0, hide: 100 } });
                        }
                    });
                });
            });
            </script>
            @endpush
