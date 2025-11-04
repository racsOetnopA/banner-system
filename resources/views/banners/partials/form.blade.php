<div class="row g-3">

  {{-- Nombre --}}
  <div class="col-md-6">
    <label class="form-label">
      Nombre
      <i class="fas fa-info-circle text-muted ms-1"
         data-bs-toggle="tooltip"
         title="Nombre descriptivo del banner, visible solo en el panel de administración."></i>
    </label>
    <input type="text" name="name" class="form-control" required
           value="{{ old('name', $banner->name ?? '') }}">
    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Tipo --}}
  <div class="col-md-3">
    <label class="form-label">
      Tipo
      <i class="fas fa-question-circle text-muted ms-1"
         data-bs-toggle="tooltip"
         title="Selecciona el tipo de contenido: imagen, video o código HTML/script."></i>
    </label>
    @php $t = old('type', $banner->type ?? 'image'); @endphp
    <select name="type" id="type" class="form-select" required>
      <option value="image" {{ $t=='image'?'selected':'' }}>Imagen</option>
      <option value="video" {{ $t=='video'?'selected':'' }}>Video</option>
      <option value="html"  {{ $t=='html'?'selected':'' }}>HTML / Script</option>
    </select>
    @error('type')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Activo --}}
  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check">
      <input class="form-check-input" type="checkbox" name="active" value="1" id="active"
             {{ old('active', $banner->active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="active">
        <i class="fas fa-toggle-on text-success me-1"></i> Activo
      </label>
    </div>
  </div>

  {{-- Campo: Imagen --}}
  <div class="col-md-6 type-image">
    <label class="form-label">
      Imagen
      <i class="fas fa-image text-muted ms-1" data-bs-toggle="tooltip"
         title="Selecciona una imagen para este banner (formatos JPG, PNG, WEBP)."></i>
    </label>
    <input type="file" name="image" class="form-control" accept="image/*">
    @if(isset($banner) && $banner->image_path)
      <div class="mt-2">
        <img src="{{ Storage::url($banner->image_path) }}" style="height:80px;" class="rounded shadow-sm">
      </div>
    @endif
    @error('image')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Campo: Video --}}
  <div class="col-md-6 type-video" style="display:none;">
    <label class="form-label">
      Video
      <i class="fas fa-video text-muted ms-1" data-bs-toggle="tooltip"
         title="Sube un archivo de video (MP4, WEBM, OGG). Se reproducirá automáticamente en el banner."></i>
    </label>
    <input type="file" name="video" class="form-control" accept="video/*">
    @if(isset($banner) && $banner->video_path)
      <div class="mt-2">
        <video src="{{ Storage::url($banner->video_path) }}" width="150" controls class="rounded shadow-sm"></video>
      </div>
    @endif
    @error('video')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Campo: HTML/Script --}}
  <div class="col-md-6 type-html" style="display:none;">
    <label class="form-label">
      Código HTML / Script
      <i class="fas fa-code text-muted ms-1" data-bs-toggle="tooltip"
         title="Pega aquí el código HTML o script externo (como Google AdSense, iframes, etc.)."></i>
    </label>
    <textarea name="html_code" rows="6" class="form-control"
              placeholder="<script> ... </script>">{{ old('html_code', $banner->html_code ?? '') }}</textarea>
    @error('html_code')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Enlace destino --}}
  <div class="col-md-6">
    <label class="form-label">
      Enlace destino (opcional)
      <i class="fas fa-link text-muted ms-1" data-bs-toggle="tooltip"
         title="Dirección a la que se redirige cuando el usuario hace clic en el banner."></i>
    </label>
    <input type="url" name="link_url" class="form-control"
           value="{{ old('link_url', $banner->link_url ?? '') }}"
           placeholder="https://ejemplo.com">
    @error('link_url')<small class="text-danger">{{ $message }}</small>@enderror
  </div>

  {{-- Fechas --}}
  <div class="col-md-3">
    <label class="form-label">Inicio</label>
    <input type="datetime-local" name="start_date" class="form-control"
           value="{{ old('start_date', isset($banner->start_date)?$banner->start_date->format('Y-m-d\TH:i'):'') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Fin</label>
    <input type="datetime-local" name="end_date" class="form-control"
           value="{{ old('end_date', isset($banner->end_date)?$banner->end_date->format('Y-m-d\TH:i'):'') }}">
  </div>

  {{-- Botones --}}
  <div class="col-12 mt-4">
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save me-1"></i> Guardar
    </button>
    <a href="{{ route('banners.index') }}" class="btn btn-secondary">
      <i class="fas fa-times me-1"></i> Cancelar
    </a>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const typeSelect = document.getElementById('type');
  const imageField = document.querySelector('input[name="image"]');
  const videoField = document.querySelector('input[name="video"]');
  const htmlField  = document.querySelector('textarea[name="html_code"]');

  function toggleFields() {
    const t = typeSelect.value;

    // Mostrar/ocultar secciones
    document.querySelector('.type-image').style.display = (t === 'image') ? '' : 'none';
    document.querySelector('.type-video').style.display = (t === 'video') ? '' : 'none';
    document.querySelector('.type-html').style.display  = (t === 'html')  ? '' : 'none';

    // Activar/desactivar 'required' según tipo
    if (imageField) imageField.required = (t === 'image');
    if (videoField) videoField.required = (t === 'video');
    if (htmlField)  htmlField.required  = (t === 'html');
  }

  // Inicializar tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  typeSelect.addEventListener('change', toggleFields);
  toggleFields();
});
</script>
@endpush
