@extends('layouts.admin')
@section('title', 'Código')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-code me-2"></i> Código</h1>
    <!-- no crear nueva asignación desde aquí -->
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Seleccionar zona</label>
                <select id="zone_select" class="form-select">
                    <option value="">-- Selecciona zona --</option>
                    @foreach($zones as $z)
                        <option value="{{ $z->id }}" data-site="{{ $z->web->site_domain ?? '' }}">{{ $z->name }} @if($z->web) - {{ $z->web->site_domain }}@endif</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">Código para insertar</label>
                <div class="input-group">
                    <textarea id="embed-code" class="form-control bg-light border" rows="10" readonly
                              style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', monospace; font-size: .9rem;"></textarea>
                    <button type="button" id="copy-code" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Copiar código">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <small class="text-muted d-block mt-1">Copia este snippet y pégalo en el sitio correspondiente.</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('zone_select');
    const embed = document.getElementById('embed-code');
    const copyBtn = document.getElementById('copy-code');
    const base = "{{ rtrim(config('app.url'), '/') }}";

    function buildSnippet(zoneName, siteDomain) {
        if (!zoneName || !siteDomain) return '';
        return `<div id="zone-${zoneName}"></div>\n<script>\n(function(){\n  var s=document.createElement('script');\n  s.src='${base}/js/banner.js?zone=${zoneName}&site=${siteDomain}';\n  document.currentScript.parentNode.appendChild(s);\n})();\n<\/script>`;
    }

    function update() {
        const opt = select.options[select.selectedIndex];
        const zoneText = opt ? opt.text.split(' - ')[0].trim() : '';
        const site = opt ? opt.dataset.site || '' : '';
        embed.value = zoneText && site ? buildSnippet(zoneText, site) : '';
    }

    copyBtn.addEventListener('click', () => {
        if (!embed.value) return;
        embed.focus(); embed.select(); document.execCommand('copy');
        copyBtn.innerHTML = '<i class="fas fa-check text-success"></i>';
        copyBtn.classList.replace('btn-outline-primary','btn-success');
        setTimeout(()=>{ copyBtn.innerHTML = '<i class="fas fa-copy"></i>'; copyBtn.classList.replace('btn-success','btn-outline-primary'); }, 1500);
    });

    select.addEventListener('change', update);
    update();
});
</script>
@endpush
