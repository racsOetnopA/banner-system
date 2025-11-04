@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4"><i class="fas fa-chart-line me-2"></i> Dashboard de Estadísticas</h1>

  {{-- KPIs principales --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card text-center shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <i class="fas fa-eye fa-2x text-primary mb-2"></i>
          <h5 class="card-title mb-1">Vistas Totales</h5>
          <h3 class="fw-bold">{{ number_format($totalViews) }}</h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center shadow-sm border-start border-success border-4">
        <div class="card-body">
          <i class="fas fa-mouse-pointer fa-2x text-success mb-2"></i>
          <h5 class="card-title mb-1">Clics Totales</h5>
          <h3 class="fw-bold">{{ number_format($totalClicks) }}</h3>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card text-center shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <i class="fas fa-percentage fa-2x text-warning mb-2"></i>
          <h5 class="card-title mb-1">CTR (Click Through Rate)</h5>
          <h3 class="fw-bold">{{ $ctr }}%</h3>
        </div>
      </div>
    </div>
  </div>

  {{-- Gráficas --}}
  <div class="row g-3">
    {{-- Gráfico 1: Vistas vs Clics por día --}}
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <i class="fas fa-chart-bar me-2 text-primary"></i> Vistas y Clics (últimos 7 días)
        </div>
        <div class="card-body">
          <canvas id="chartViewsClicks" height="120"></canvas>
        </div>
      </div>
    </div>

    {{-- Gráfico 2: Vistas por zona --}}
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <i class="fas fa-layer-group me-2 text-secondary"></i> Distribución por Zona
        </div>
        <div class="card-body">
          <canvas id="chartZones" height="180"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // === Datos desde el backend ===
  const viewsByDay = @json($viewsByDay);
  const clicksByDay = @json($clicksByDay);
  const viewsByZone = @json($viewsByZone);

  // === Dataset: Vistas vs Clics ===
  const ctx1 = document.getElementById('chartViewsClicks');
  new Chart(ctx1, {
    type: 'line',
    data: {
      labels: viewsByDay.map(v => v.date),
      datasets: [
        {
          label: 'Vistas',
          data: viewsByDay.map(v => v.total),
          borderColor: '#007bff',
          backgroundColor: 'rgba(0, 123, 255, .2)',
          tension: .4
        },
        {
          label: 'Clics',
          data: clicksByDay.map(v => v.total),
          borderColor: '#28a745',
          backgroundColor: 'rgba(40, 167, 69, .2)',
          tension: .4
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: true, position: 'bottom' } },
      scales: {
        y: { beginAtZero: true },
        x: { ticks: { autoSkip: false } }
      }
    }
  });

  // === Dataset: Vistas por Zona ===
  const ctx2 = document.getElementById('chartZones');
  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: viewsByZone.map(z => z.name),
      datasets: [{
        data: viewsByZone.map(z => z.total),
        backgroundColor: ['#0d6efd','#6f42c1','#20c997','#ffc107','#dc3545','#198754'],
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        tooltip: { enabled: true }
      }
    }
  });
});
</script>
@endpush
