@php
    // helper: valor con old() o modelo
    $val = fn($k, $d = '') => old($k, isset($zone) ? $zone->$k : $d);
@endphp

<div class="row g-3">

    {{-- Nombre --}}
    <div class="col-md-4">
        <label class="form-label">
            Nombre de la zona
            <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip"
                title="Identificador único, ej: header, sidebar, footer."></i>
        </label>
        <input type="text" id="zone-name" name="name" class="form-control" required value="{{ $val('name') }}"
            placeholder="Ej: header">
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Ancho --}}
    <div class="col-md-3">
        <label class="form-label">
            Ancho (px)
            <i class="fas fa-arrows-left-right text-muted ms-1" data-bs-toggle="tooltip"
                title="Opcional. Déjalo vacío para auto."></i>
        </label>
        <input type="number" name="width" class="form-control" value="{{ $val('width') }}" placeholder="Ej: 728">
        @error('width')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Alto --}}
    <div class="col-md-3">
        <label class="form-label">
            Alto (px)
            <i class="fas fa-arrows-up-down text-muted ms-1" data-bs-toggle="tooltip"
                title="Opcional. Déjalo vacío para auto."></i>
        </label>
        <input type="number" name="height" class="form-control" value="{{ $val('height') }}" placeholder="Ej: 90">
        @error('height')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- Principal --}}
    <div class="col-md-2 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="principal" value="1" id="principal"
                {{ old('principal', $zone->principal ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="principal" data-bs-toggle="tooltip"
                title="Marcar si esta zona es la principal del sitio.">
                <i class="fas fa-star text-warning me-1"></i> Principal
            </label>
        </div>
    </div>

    {{-- Descripción --}}
    <div class="col-12">
        <label class="form-label">Descripción</label>
        <input type="text" name="description" class="form-control" value="{{ $val('description') }}"
            placeholder="Descripción corta de la zona (opcional)">
        @error('description')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <hr class="my-3">


    <div class="col-12 mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Guardar
        </button>
        <a href="{{ route('zones.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
        });
    </script>
@endpush
