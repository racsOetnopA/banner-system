@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4"><i class="fas fa-chart-line me-2"></i> Dashboard de Estadísticas</h1>

        {{-- KPIs principales --}}
        <div class="row">
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="card custom-card total-students-card overflow-hidden">
                    <div class="card-body">
                        <h4><span class="d-block mb-2">Vistas Totales</span></h4>
                        <h5 class="fw-medium mb-2">{{ number_format($totalViews) }}</h5>
                        <span class="courses-main-cards-icon svg-white text-fixed-white">
                            <i class="fas fa-eye fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="card custom-card total-courses-card overflow-hidden">
                    <div class="card-body">
                        <h4><span class="d-block mb-2">Clics Totales</span></h4>
                        <h5 class="fw-medium mb-2">{{ number_format($totalClicks) }}</h5>
                        <span class="courses-main-cards-icon svg-white text-fixed-white">
                            <i class="fas fa-mouse-pointer fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                <div class="card custom-card total-revenue-card overflow-hidden">
                    <div class="card-body">
                        <h4><span class="d-block mb-2">CTR (Click Through Rate)</span></h4>
                        <h5 class="fw-medium mb-2">{{ $ctr }}%</h5>
                        <span class="courses-main-cards-icon svg-white text-fixed-white">
                            <i class="fas fa-percentage fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
            <hr>
        </div>


        {{-- Gráficas --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Rango de fechas</label>
                <div class="d-flex gap-2">
                    <input type="date" id="filter_start" class="form-control form-control-sm">
                    <input type="date" id="filter_end" class="form-control form-control-sm">
                    <button id="filter_apply" class="btn btn-sm btn-primary">Aplicar</button>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Sitio</label>
                <select id="filter_web" class="form-select form-select-sm">
                    <option value="">-- Todos los sitios --</option>
                    @foreach ($webs as $w)
                        <option value="{{ $w->id }}">{{ $w->site_domain }}</option>
                    @endforeach
                </select>
            </div>
        </div>
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

        {{-- Sección: Estadísticas por Zona por Sitio --}}
        <div class="row g-3 mt-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <i class="fas fa-globe me-2 text-muted"></i> Estadísticas por Zona (por Sitio)
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 mb-3">
                                <canvas id="chartZoneByWeb" height="120"></canvas>
                            </div>
                            <div class="col-12">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Zona</th>
                                            <th class="text-end">Vistas</th>
                                            <th class="text-end">Clics</th>
                                        </tr>
                                    </thead>
                                    <tbody id="zone_stats_table">
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Seleccione un sitio y aplique
                                                el rango.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
            // Guardar el chart en una variable para poder actualizarlo con filtros
            let viewsClicksChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: viewsByDay.map(v => v.date),
                    datasets: [{
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
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        },
                        x: {
                            ticks: {
                                autoSkip: false
                            }
                        }
                    }
                }
            });

            // Función para actualizar el chart de Vistas/Clics con series nuevas
            function updateViewsClicksChart(viewsSeries, clicksSeries) {
                // Crear un set de fechas unificado entre views y clicks
                const datesSet = new Set();
                viewsSeries.forEach(v => datesSet.add(v.date));
                clicksSeries.forEach(c => datesSet.add(c.date));
                const labels = Array.from(datesSet).sort();

                const viewsMap = new Map(viewsSeries.map(v => [v.date, v.total]));
                const clicksMap = new Map(clicksSeries.map(c => [c.date, c.total]));

                const viewsData = labels.map(d => viewsMap.has(d) ? viewsMap.get(d) : 0);
                const clicksData = labels.map(d => clicksMap.has(d) ? clicksMap.get(d) : 0);

                viewsClicksChart.data.labels = labels;
                viewsClicksChart.data.datasets[0].data = viewsData;
                viewsClicksChart.data.datasets[1].data = clicksData;
                viewsClicksChart.update();
            }

            // === Dataset: Vistas por Zona (doughnut) ===
            const ctx2 = document.getElementById('chartZones');
            // Guardamos el chart en una variable para poder actualizarlo al filtrar por sitio
            let zonesChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: viewsByZone.map(z => z.name),
                    datasets: [{
                        data: viewsByZone.map(z => z.total),
                        backgroundColor: ['#0d6efd', '#6f42c1', '#20c997', '#ffc107', '#dc3545', '#198754'],
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            enabled: true
                        }
                    }
                }
            });

            // === Zona por Sitio (AJAX) ===
            const webSelect = document.getElementById('filter_web');
            const startInput = document.getElementById('filter_start');
            const endInput = document.getElementById('filter_end');
            const applyBtn = document.getElementById('filter_apply');
            let zoneWebChart = null;

            function fetchZoneStats() {
                const webId = webSelect.value;
                const start = startInput.value;
                const end = endInput.value;
                if (!webId) {
                    // Cuando no hay sitio seleccionado, mostrar todas las zonas en el donut
                    document.getElementById('zone_stats_table').innerHTML =
                        '<tr><td colspan="3" class="text-center text-muted">Seleccione un sitio primero.</td></tr>';
                    if (zoneWebChart) zoneWebChart.destroy();

                    // Restaurar donut a la vista global (todas las zonas)
                    zonesChart.data.labels = viewsByZone.map(z => z.name);
                    zonesChart.data.datasets[0].data = viewsByZone.map(z => z.total);
                    zonesChart.update();

                    return;
                }
                fetch(`{{ route('estadisticas.zone_stats') }}?web_id=${webId}&start=${start}&end=${end}`)
                    .then(r => r.json())
                    .then(json => {
                        const data = json.data || [];
                        const tbody = document.getElementById('zone_stats_table');
                        if (!data.length) {
                            tbody.innerHTML =
                                '<tr><td colspan="3" class="text-center text-muted">No hay datos para este periodo.</td></tr>';
                            if (zoneWebChart) zoneWebChart.destroy();
                            // Si no hay datos para el sitio, limpiar el donut
                            zonesChart.data.labels = [];
                            zonesChart.data.datasets[0].data = [];
                            zonesChart.update();
                            return;
                        }
                        tbody.innerHTML = data.map(row =>
                            `<tr><td>${row.name}</td><td class="text-end">${row.views}</td><td class="text-end">${row.clicks}</td></tr>`
                            ).join('');

                        // Chart
                        const labels = data.map(d => d.name);
                        const values = data.map(d => d.views);
                        // Actualizar donut para mostrar sólo las zonas del sitio seleccionado
                        zonesChart.data.labels = labels;
                        zonesChart.data.datasets[0].data = values;
                        zonesChart.update();

                        const ctx = document.getElementById('chartZoneByWeb');
                        if (zoneWebChart) zoneWebChart.destroy();
                        zoneWebChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Vistas',
                                    data: values,
                                    backgroundColor: 'rgba(13,110,253,.7)'
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    })
                    .catch(err => console.error(err));
            }

            // Nueva función: obtener series de Vistas/Clics filtradas
            function fetchSeries() {
                const webId = webSelect.value;
                const start = startInput.value;
                const end = endInput.value;

                fetch(`{{ route('estadisticas.series') }}?web_id=${webId}&start=${start}&end=${end}`)
                    .then(r => r.json())
                    .then(json => {
                        const views = json.views || [];
                        const clicks = json.clicks || [];
                        updateViewsClicksChart(views, clicks);
                    })
                    .catch(err => console.error(err));
            }

            // Al aplicar filtros, actualizar tanto la tabla/gráfico por zona como las series
            applyBtn.addEventListener('click', () => { fetchZoneStats(); fetchSeries(); });
            // también disparar al cambiar de sitio
            webSelect.addEventListener('change', () => { fetchZoneStats(); fetchSeries(); });
        });
    </script>
@endpush
