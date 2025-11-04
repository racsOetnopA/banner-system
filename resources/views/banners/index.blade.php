@extends('layouts.admin')
@section('title', 'Banners')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><i class="fas fa-image me-2"></i> Banners</h1>
    <a href="{{ route('banners.create') }}" class="btn btn-primary">
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
                    <th>Fechas</th>
                    <th class="text-center">Vista previa</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($banners as $b)
                <tr>
                    <td>{{ $b->id }}</td>
                    <td>{{ $b->name }}</td>
                    <td>
                        @if($b->type === 'image')
                            <span class="badge bg-info"><i class="fas fa-image me-1"></i> Imagen</span>
                        @elseif($b->type === 'video')
                            <span class="badge bg-primary"><i class="fas fa-video me-1"></i> Video</span>
                        @elseif($b->type === 'html')
                            <span class="badge bg-warning text-dark"><i class="fas fa-code me-1"></i> HTML</span>
                        @endif
                    </td>
                    <td>
                        {!! $b->active
                            ? '<span class="badge bg-success">Sí</span>'
                            : '<span class="badge bg-danger">No</span>' !!}
                    </td>
                    <td>
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
                        @if($b->type === 'image' && $b->image_path)
                            <img src="{{ Storage::url($b->image_path) }}"
                                 class="rounded shadow-sm"
                                 style="height:50px;max-width:100px;object-fit:cover;"
                                 alt="Imagen del banner">
                        @elseif($b->type === 'video' && $b->video_path)
                            <video src="{{ Storage::url($b->video_path) }}"
                                   class="rounded shadow-sm"
                                   style="height:50px;max-width:100px;object-fit:cover;"
                                   muted
                                   onmouseover="this.play()" onmouseout="this.pause()"></video>
                        @elseif($b->type === 'html')
                            <span class="text-muted small">[HTML / Script]</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>

                    {{-- Acciones --}}
                    <td class="text-end">
                        <a href="{{ route('banners.edit', $b) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar banner">
                            <i class="fas fa-edit"></i>
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
