@props([
    'action' => '#',
    'method' => 'POST',
    'web' => null,
])

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if(strtoupper($method) !== 'POST')
                @method($method)
            @endif

            <div class="mb-3">
                <label for="site_domain" class="form-label">Dominio</label>
                <input type="text" name="site_domain" id="site_domain" class="form-control" value="{{ old('site_domain', optional($web)->site_domain) }}" required>
                @error('site_domain') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-success">{{ strtoupper($method) === 'POST' ? 'Guardar' : 'Actualizar' }}</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Regresar</a>
            </div>
        </form>
    </div>
</div>
