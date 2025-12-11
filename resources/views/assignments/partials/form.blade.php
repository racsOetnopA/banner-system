@php
  $val = fn($k, $d='') => old($k, isset($assignment)?$assignment->$k:$d);
@endphp

<div class="row g-3">

  {{-- Banner --}}
  <div class="col-md-4">
    <label class="form-label">Banner</label>
    <select name="banner_id" class="form-select" required data-bs-toggle="tooltip" title="Selecciona el banner que se mostrará">
      <option value="">-- Selecciona --</option>
      @foreach($banners as $b)
        <option value="{{ $b->id }}" {{ (int)$val('banner_id')===$b->id ? 'selected':'' }}>{{ $b->name }}</option>
      @endforeach
    </select>
    @error('banner_id')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Zona --}}
  <div class="col-md-4">
    <label class="form-label">Zona</label>
    <select name="zone_id" class="form-select" required data-bs-toggle="tooltip" title="Selecciona la zona donde aparecerá el banner">
      <option value="">-- Selecciona --</option>
      @foreach($zones as $z)
        <option value="{{ $z->id }}" data-zone-name="{{ $z->name }}" data-site="{{ $z->web->site_domain ?? '' }}" {{ (int)$val('zone_id')===$z->id ? 'selected':'' }}>
          {{ $z->name }}@if($z->width || $z->height) - {{ $z->width }}x{{ $z->height }}@endif @if($z->web) - {{ $z->web->site_domain }}@endif
        </option>
      @endforeach
    </select>
    @error('zone_id')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Dominio --}}
  <div class="col-md-4">
    <label class="form-label">Dominio autorizado</label>
    <input type="text" name="site_domain" class="form-control"
           placeholder="ej: blog.com"
           value="{{ $val('site_domain') }}" data-bs-toggle="tooltip"
           title="Dominio donde se permitirá mostrar este banner (sin http)">
    @error('site_domain')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Rotación --}}
  <div class="col-md-4">
    <label class="form-label">Modo de rotación</label>
    <select name="rotation_mode" class="form-select" required data-bs-toggle="tooltip" title="Aleatoria o secuencial">
      @php $rm = $val('rotation_mode','random'); @endphp
      <option value="random" {{ $rm==='random'?'selected':'' }}>Aleatoria</option>
      <option value="sequential" {{ $rm==='sequential'?'selected':'' }}>Secuencial</option>
    </select>
  </div>

  {{-- Peso --}}
  <div class="col-md-2">
    <label class="form-label">Peso</label>
    <input type="number" name="weight" class="form-control" min="1" max="100" required
           value="{{ $val('weight',1) }}" data-bs-toggle="tooltip"
           title="Define la prioridad o frecuencia de aparición (1-100)">
    @error('weight')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  <hr class="my-3">

{{-- CÓDIGO PARA INSERTAR --}}
<div class="col-12">
  <label class="form-label">
    Código para insertar en la web
    <i class="fas fa-code text-muted ms-1"
       data-bs-toggle="tooltip"
       title="Copia y pega este snippet en la página donde se mostrará el banner asignado."></i>
  </label>

  <div class="input-group">
    <textarea id="embed-code" class="form-control bg-light border" rows="8" readonly
              style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', monospace; font-size: .9rem;"></textarea>
    <button type="button" id="copy-code" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Copiar código">
      <i class="fas fa-copy"></i>
    </button>
  </div>

  <small class="text-muted d-block mt-1">
    Este script mostrará el banner correspondiente a esta asignación, usando los datos de zona y dominio configurados. Cópielo y péguelo en su entorno web
  </small>
</div>


  {{-- Botones --}}
  <div class="col-12 mt-4">
    <div class="d-flex justify-content-end gap-2">
      <button type="submit" class="btn btn-success">
        <i class="fas fa-save me-1"></i> Guardar
      </button>
      <a href="{{ route('assignments.index') }}" class="btn btn-secondary">Cancelar</a>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  const zoneSelect = document.querySelector('select[name="zone_id"]');
  const siteInput = document.querySelector('input[name="site_domain"]');
  const embed = document.getElementById('embed-code');
  const copyBtn = document.getElementById('copy-code');
  const base = "{{ rtrim(config('app.url'), '/') }}"; // APP_URL base del servidor de banners

  function buildSnippet(zoneId) {
    if (!zoneId) return '';
    return `<div id="zone-${zoneId}"></div>
<script>
  (function(){
    var s=document.createElement('script');
    s.src='${base}/js/banner.js?zone_id=${zoneId}';
    document.currentScript.parentNode.appendChild(s);
  })();
<\/script>`;
  }

  function updateCode() {
    const opt = zoneSelect ? zoneSelect.options[zoneSelect.selectedIndex] : null;
    const zoneId = opt ? opt.value : '';
    embed.value = zoneId ? buildSnippet(zoneId) : '';
  }

  // Copiar
  copyBtn.addEventListener('click', () => {
    if (!embed.value) return;
    embed.focus();
    embed.select();
    document.execCommand('copy');
    copyBtn.innerHTML = '<i class="fas fa-check text-success"></i>';
    copyBtn.classList.replace('btn-outline-primary','btn-success');
    setTimeout(() => {
      copyBtn.innerHTML = '<i class="fas fa-copy"></i>';
      copyBtn.classList.replace('btn-success','btn-outline-primary');
    }, 1500);
  });

  if (zoneSelect) zoneSelect.addEventListener('change', updateCode);
  if (siteInput) siteInput.addEventListener('input', updateCode);
  updateCode(); // inicializa si hay valores existentes
});
</script>
@endpush
