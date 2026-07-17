@extends('layouts.app')

@section('content')

{{-- Leaflet MarkerCluster CSS --}}
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
@endpush

<div class="container-fluid pb-5">

    {{-- ============================================================
    HEADER & FILTER BAR
    ============================================================ --}}
    <div class="row align-items-center mb-3 g-2">
        <div class="col-md-7">
            <h4 class="fw-bold mb-0 text-slate-800">Dashboard Klasifikasi Tingkat Keparahan Bencana</h4>
            <small class="text-muted">Provinsi Jawa Timur</small>
        </div>
        <div class="col-md-5 text-md-end">
            <form method="GET" action="{{ route('dashboard') }}" class="d-inline-flex align-items-center gap-2 bg-white px-3 py-2 rounded-3 shadow-sm border">
                <i class="fa fa-calendar text-muted" style="font-size: 13px;"></i>
                <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-transparent text-slate-700" value="{{ $startDate }}" style="width: 120px; font-size: 12px;">
                <span class="text-muted">—</span>
                <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-transparent text-slate-700" value="{{ $endDate }}" style="width: 120px; font-size: 12px;">
                <button type="submit" class="btn btn-sm btn-primary px-3" style="font-size: 12px; border-radius: 6px;">
                    Filter
                </button>
            </form>
        </div>
    </div>



    {{-- ============================================================
    ROW 1: KPI CARDS
    ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                    <div class="kpi-icon-wrapper bg-primary-subtle text-primary">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div>
                        <div class="text-muted uppercase-label">Total Kejadian</div>
                        <h3 class="fw-bold mb-0 text-slate-800">{{ number_format($totalDisaster) }}</h3>
                        <span class="text-muted" style="font-size: 11px;">Kejadian bencana</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                    <div class="kpi-icon-wrapper bg-success-subtle text-success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <div class="text-muted uppercase-label">Tingkat Rendah</div>
                        <h3 class="fw-bold mb-0 text-success">{{ number_format($totalLow) }}</h3>
                        <span class="text-success fw-semibold" style="font-size: 11px;">
                            {{ $totalDisaster > 0 ? number_format(($totalLow / $totalDisaster) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                    <div class="kpi-icon-wrapper bg-warning-subtle text-warning">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <div>
                        <div class="text-muted uppercase-label">Tingkat Sedang</div>
                        <h3 class="fw-bold mb-0 text-warning-dark">{{ number_format($totalMedium) }}</h3>
                        <span class="text-warning-dark fw-semibold" style="font-size: 11px;">
                            {{ $totalDisaster > 0 ? number_format(($totalMedium / $totalDisaster) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3 px-3">
                    <div class="kpi-icon-wrapper bg-danger-subtle text-danger">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <div class="text-muted uppercase-label">Tingkat Tinggi</div>
                        <h3 class="fw-bold mb-0 text-danger">{{ number_format($totalHigh) }}</h3>
                        <span class="text-danger fw-semibold" style="font-size: 11px;">
                            {{ $totalDisaster > 0 ? number_format(($totalHigh / $totalDisaster) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
    ROW 2: PETA FULL WIDTH
    ============================================================ --}}
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 15px;">
                        <i class="fa fa-map text-primary me-2"></i>Peta Sebaran Kejadian Bencana
                    </h5>
                    <div class="d-flex gap-3 align-items-center bg-light border rounded-3 px-3 py-1" style="font-size: 11px;">
                        <span class="fw-semibold text-slate-600">Keparahan:</span>
                        <span><span class="badge-dot bg-success"></span> Rendah</span>
                        <span><span class="badge-dot bg-warning"></span> Sedang</span>
                        <span><span class="badge-dot bg-danger"></span> Tinggi</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 420px; border-radius: 0 0 12px 12px; z-index: 1;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
    ROW 3: DONUT + TREND CHART + PERFORMA MODEL
    ============================================================ --}}
    <div class="row g-3 mb-3">

        {{-- Distribusi Donut --}}
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 14px;">Distribusi Keparahan</h5>
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-3">
                    <div class="position-relative d-flex justify-content-center" style="width: 160px; height: 160px;">
                        <canvas id="levelChart"></canvas>
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <h5 class="fw-bold mb-0 text-slate-800" style="font-size: 18px;">{{ number_format($totalDisaster) }}</h5>
                            <small class="text-muted" style="font-size: 10px;">Total</small>
                        </div>
                    </div>
                    <ul class="list-unstyled mt-3 mb-0 w-100" style="font-size: 12px;">
                        <li class="d-flex align-items-center justify-content-between py-1 border-bottom">
                            <span class="d-flex align-items-center gap-2">
                                <span style="width:10px;height:10px;border-radius:3px;background:#22C55E;display:inline-block;"></span>
                                <span class="text-slate-700">Rendah</span>
                            </span>
                            <span class="fw-bold text-success">{{ number_format($totalLow) }}</span>
                        </li>
                        <li class="d-flex align-items-center justify-content-between py-1 border-bottom">
                            <span class="d-flex align-items-center gap-2">
                                <span style="width:10px;height:10px;border-radius:3px;background:#FACC15;display:inline-block;"></span>
                                <span class="text-slate-700">Sedang</span>
                            </span>
                            <span class="fw-bold text-warning-dark">{{ number_format($totalMedium) }}</span>
                        </li>
                        <li class="d-flex align-items-center justify-content-between pt-1">
                            <span class="d-flex align-items-center gap-2">
                                <span style="width:10px;height:10px;border-radius:3px;background:#EF4444;display:inline-block;"></span>
                                <span class="text-slate-700">Tinggi</span>
                            </span>
                            <span class="fw-bold text-danger">{{ number_format($totalHigh) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Trend Chart --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 14px;">Tren Kejadian Bencana per Tahun</h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <div style="width:100%; height: 200px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Performa Model --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 14px;">Performa Model</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2">
                                <div class="bg-primary-subtle text-primary rounded px-2 py-1" style="font-size: 12px;"><i class="fa fa-chart-line"></i></div>
                                <div>
                                    <div class="text-muted" style="font-size: 10px;">Akurasi</div>
                                    <div class="fw-bold" style="font-size: 13px;">94.16%</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2">
                                <div class="bg-success-subtle text-success rounded px-2 py-1" style="font-size: 12px;"><i class="fa fa-bullseye"></i></div>
                                <div>
                                    <div class="text-muted" style="font-size: 10px;">Precision</div>
                                    <div class="fw-bold" style="font-size: 13px;">94.02%</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2">
                                <div class="bg-warning-subtle text-warning-dark rounded px-2 py-1" style="font-size: 12px;"><i class="fa fa-redo"></i></div>
                                <div>
                                    <div class="text-muted" style="font-size: 10px;">Recall</div>
                                    <div class="fw-bold" style="font-size: 13px;">94.16%</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2">
                                <div class="bg-purple-subtle text-purple rounded px-2 py-1" style="font-size: 12px;"><i class="fa fa-chart-bar"></i></div>
                                <div>
                                    <div class="text-muted" style="font-size: 10px;">F1-Score</div>
                                    <div class="fw-bold" style="font-size: 13px;">94.02%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================
    ROW 4: DATA HISTORIS TERBARU + PERBANDINGAN MODEL
    ============================================================ --}}
    <div class="row g-3">

        {{-- Data Historis Terbaru --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 14px;">
                        <i class="fa fa-clock-rotate-left text-primary me-2"></i>Data Historis Terbaru
                    </h5>
                    <a href="{{ route('history.index') }}" class="btn btn-outline-primary btn-sm px-3" style="font-size: 11px; border-radius: 6px;">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 align-middle" style="font-size: 12px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Tanggal</th>
                                    <th>Bencana</th>
                                    <th>Lokasi</th>
                                    <th class="text-center">Korban</th>
                                    <th>Kerusakan</th>
                                    <th class="pe-3">Tingkat</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($latestDisaster as $item)
                            <tr>
                                <td class="ps-3 py-2 text-slate-600 text-nowrap">{{ \Carbon\Carbon::parse($item->event_date)->format('d/m/Y') }}</td>
                                <td class="fw-semibold text-slate-800">{{ $item->disaster_type }}</td>
                                <td class="text-truncate" style="max-width: 120px;">{{ $item->regency }}</td>
                                <td class="text-center">{{ $item->dead + $item->missing + $item->serious_wound + $item->minor_injuries }}</td>
                                <td class="text-truncate text-slate-500" style="max-width: 140px;" title="{{ $item->damage }}">{{ $item->damage ?: '-' }}</td>
                                <td class="pe-3">
                                    @if($item->level_bpbd === 'TINGGI')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 10px;">Tinggi</span>
                                    @elseif($item->level_bpbd === 'SEDANG')
                                        <span class="badge bg-warning-subtle text-warning-dark border border-warning-subtle px-2 py-1" style="font-size: 10px;">Sedang</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 10px;">Rendah</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Perbandingan Model --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 14px;">
                        <i class="fa fa-scale-balanced text-primary me-2"></i>Perbandingan Model
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0 align-middle" style="font-size: 11px;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-2">Model</th>
                                <th>Akurasi</th>
                                <th>Precision</th>
                                <th>Recall</th>
                                <th class="pe-3">F1</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 py-2 fw-semibold text-slate-700">Decision Tree</td>
                                <td>93.82%</td>
                                <td>93.67%</td>
                                <td>93.82%</td>
                                <td class="pe-3">93.48%</td>
                            </tr>
                            <tr class="table-primary-subtle">
                                <td class="ps-3 py-2 fw-bold text-primary">Random Forest</td>
                                <td class="text-primary fw-semibold">94.16%</td>
                                <td class="text-primary fw-semibold">94.02%</td>
                                <td class="text-primary fw-semibold">94.16%</td>
                                <td class="pe-3 text-primary fw-semibold">94.02%</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="px-3 py-3">
                        <div class="alert alert-primary border-0 d-flex align-items-center gap-2 py-2 px-3 mb-0 rounded-3" style="font-size: 12px;">
                            <i class="fa fa-brain"></i>
                            <div>
                                Ingin membuat klasifikasi baru?
                                <a href="{{ route('classification.index') }}" class="fw-bold ms-1">Buka Klasifikasi →</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')

{{-- Leaflet MarkerCluster --}}
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<style>
/* Dashboard Styles */
.text-slate-800 { color: #1e293b; }
.text-slate-700 { color: #334155; }
.text-slate-600 { color: #475569; }
.text-slate-500 { color: #64748b; }
.text-warning-dark { color: #d97706; }
.bg-warning-subtle { background-color: #fef3c7; }
.bg-purple-subtle { background-color: #f3e8ff; }
.text-purple { color: #a855f7; }
.table-primary-subtle { background-color: #eff6ff; }

/* Custom Badge Dot for Map Legend */
.badge-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 4px;
}

/* KPI Customization */
.kpi-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.uppercase-label {
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: .5px;
    font-weight: 600;
}

/* Custom Tooltip */
.leaflet-tooltip-region {
    background: rgba(15, 23, 42, 0.88);
    color: #f1f5f9;
    border: none;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.leaflet-tooltip-region::before {
    border-top-color: rgba(15, 23, 42, 0.88);
}
</style>

<script>
const levelData    = @json($levelDistribution);
const trendRaw     = @json($yearTrend);
const mapData      = @json($mapData);
const regencyStats = @json($regencyStats);

/* ============================================================
   DONUT CHART
   ============================================================ */
const levelOrder  = ['RENDAH','SEDANG','TINGGI'];
const levelSorted = levelOrder.map(l => levelData.find(x => x.level_bpbd === l) || { level_bpbd: l, total: 0 });

new Chart(document.getElementById("levelChart"), {
    type: "doughnut",
    data: {
        labels: levelSorted.map(x => x.level_bpbd),
        datasets: [{
            data: levelSorted.map(x => x.total),
            backgroundColor: ["#22C55E", "#FACC15", "#EF4444"],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '72%',
        plugins: {
            legend: { display: false }
        }
    }
});

/* ============================================================
   LINE CHART (Multi-line Trend per Severity Level)
   ============================================================ */
const years = [...new Set(trendRaw.map(x => x.year))].sort();

const rendahData = [];
const sedangData = [];
const tinggiData = [];

years.forEach(yr => {
    const r = trendRaw.find(x => x.year === yr && x.level_bpbd === 'RENDAH');
    const s = trendRaw.find(x => x.year === yr && x.level_bpbd === 'SEDANG');
    const t = trendRaw.find(x => x.year === yr && x.level_bpbd === 'TINGGI');
    
    rendahData.push(r ? r.total : 0);
    sedangData.push(s ? s.total : 0);
    tinggiData.push(t ? t.total : 0);
});

new Chart(document.getElementById("trendChart"), {
    type: "line",
    data: {
        labels: years,
        datasets: [
            {
                label: "Rendah",
                data: rendahData,
                borderColor: "#22C55E",
                backgroundColor: "transparent",
                tension: 0.3,
                pointRadius: 3
            },
            {
                label: "Sedang",
                data: sedangData,
                borderColor: "#FACC15",
                backgroundColor: "transparent",
                tension: 0.3,
                pointRadius: 3
            },
            {
                label: "Tinggi",
                data: tinggiData,
                borderColor: "#EF4444",
                backgroundColor: "transparent",
                tension: 0.3,
                pointRadius: 3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom",
                labels: { font: { size: 10 }, boxWidth: 10, padding: 8 }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { font: { size: 9 } } },
            x: { ticks: { font: { size: 9 } } }
        }
    }
});

/* ============================================================
   LEAFLET MAP
   ============================================================ */
const map = L.map("map", { zoomControl: true }).setView([-7.7, 112.5], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 19
}).addTo(map);

function getColor(level) {
    if (level === 'TINGGI') return '#EF4444';
    if (level === 'SEDANG') return '#FACC15';
    return '#22C55E';
}

function makeCircleIcon(color) {
    return L.divIcon({
        html: `<div style="width:12px;height:12px;border-radius:50%;background:${color};border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.4);"></div>`,
        className: '',
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });
}

const clusterGroup = L.markerClusterGroup({
    maxClusterRadius: 50,
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: false,
    iconCreateFunction: function(cluster) {
        const count = cluster.getChildCount();
        let size = count < 50 ? 36 : count < 200 ? 46 : 56;
        let bg   = count < 50 ? '#22C55E' : count < 200 ? '#FACC15' : '#EF4444';
        return L.divIcon({
            html: `<div style="background:${bg};color:#fff;width:${size}px;height:${size}px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:13px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);">${count}</div>`,
            className: '',
            iconSize: [size, size],
            iconAnchor: [size/2, size/2]
        });
    }
});

mapData.forEach(item => {
    if (!item.latitude || !item.longitude) return;
    const lat = parseFloat(item.latitude);
    const lng = parseFloat(item.longitude);
    if (isNaN(lat) || isNaN(lng)) return;
    const color  = getColor(item.level_bpbd);
    const marker = L.marker([lat, lng], { icon: makeCircleIcon(color) });
    marker.bindPopup(
        `<div style="min-width:160px;">` +
        `<b style="color:${color};">${item.level_bpbd}</b><br>` +
        `<b>${item.disaster_type}</b><br>` +
        `<span class="text-muted">${item.regency}</span>` +
        `</div>`
    );
    clusterGroup.addLayer(marker);
});
map.addLayer(clusterGroup);

function normalizeName(name) {
    if (!name) return "";
    return name.toLowerCase()
        .replace(/kabupaten|kota|kab\.|kab|kot/g, "")
        .replace(/\s+/g, "")
        .trim();
}

function findRegencyStats(geoName) {
    const cleanGeo = normalizeName(geoName);
    return regencyStats.find(r => normalizeName(r.regency) === cleanGeo) || null;
}

function getChoroplethColor(total) {
    if (total >= 2000) return '#991B1B';
    if (total >= 1000) return '#DC2626';
    if (total >= 750)  return '#EA580C';
    if (total >= 500)  return '#F97316';
    if (total >= 250)  return '#F59E0B';
    if (total >= 100)  return '#EAB308';
    if (total > 0)     return '#84CC16';
    return '#CBD5E1';
}

const regencyLayer = L.layerGroup();
let geojsonLayer;

fetch('/east-java-regencies.json')
    .then(res => res.json())
    .then(geojson => {
        geojsonLayer = L.geoJSON(geojson, {
            style: function(feature) {
                const name  = feature.properties.NAME_2 || '';
                const stats = findRegencyStats(name);
                const total = stats ? parseInt(stats.total) : 0;
                return {
                    fillColor: getChoroplethColor(total),
                    weight: 1.5,
                    opacity: 0.8,
                    color: '#475569',
                    fillOpacity: 0.55
                };
            },
            onEachFeature: function(feature, layer) {
                const name  = feature.properties.NAME_2 || '';
                const type  = feature.properties.TYPE_2 || '';
                const stats = findRegencyStats(name);
                const total = stats ? parseInt(stats.total) : 0;
                const center = layer.getBounds().getCenter();

                if (total > 0) {
                    const color = getChoroplethColor(total);
                    const badgeIcon = L.divIcon({
                        html: `<div style="background:${color};color:#fff;font-weight:700;font-size:11px;height:28px;width:28px;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;">${total}</div>`,
                        className: '',
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    });
                    const badgeMarker = L.marker(center, { icon: badgeIcon });
                    badgeMarker.bindPopup(
                        `<div style="min-width:180px;">` +
                        `<b>${name} ${type}</b><br><hr style="margin:5px 0;">` +
                        `<span style="color:#22C55E;">&#9679;</span> Rendah: <b>${parseInt(stats.total_rendah).toLocaleString()}</b><br>` +
                        `<span style="color:#FACC15;">&#9679;</span> Sedang: <b>${parseInt(stats.total_sedang).toLocaleString()}</b><br>` +
                        `<span style="color:#EF4444;">&#9679;</span> Tinggi: <b>${parseInt(stats.total_tinggi).toLocaleString()}</b><br>` +
                        `<hr style="margin:5px 0;">Total: <b>${total.toLocaleString()}</b>` +
                        `</div>`
                    );
                    regencyLayer.addLayer(badgeMarker);
                }

                let tooltipContent = `<strong>${name} ${type}</strong>`;
                tooltipContent += stats
                    ? `<br>Total Bencana: <b>${total.toLocaleString()}</b>`
                    : `<br>Tidak ada data`;

                layer.bindTooltip(tooltipContent, {
                    sticky: true,
                    opacity: 0.9,
                    className: 'leaflet-tooltip-region'
                });

                layer.on({
                    mouseover: function(e) {
                        const l = e.target;
                        l.setStyle({ weight: 3, color: '#0F172A', fillOpacity: 0.75 });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) l.bringToFront();
                    },
                    mouseout: function(e) { geojsonLayer.resetStyle(e.target); },
                    click:    function(e) { map.fitBounds(e.target.getBounds()); }
                });
            }
        }).addTo(map);
        updateLayers();
    })
    .catch(err => console.warn('Gagal memuat GeoJSON:', err));

function updateLayers() {
    const zoom = map.getZoom();
    if (zoom >= 10) {
        if (!map.hasLayer(clusterGroup)) map.addLayer(clusterGroup);
        if (map.hasLayer(regencyLayer))  map.removeLayer(regencyLayer);
        if (geojsonLayer) geojsonLayer.setStyle({ fillOpacity: 0.15, opacity: 0.4 });
    } else {
        if (map.hasLayer(clusterGroup))  map.removeLayer(clusterGroup);
        if (!map.hasLayer(regencyLayer)) map.addLayer(regencyLayer);
        if (geojsonLayer) geojsonLayer.setStyle({ fillOpacity: 0.55, opacity: 0.8 });
    }
}
map.on('zoomend', updateLayers);
updateLayers();
</script>

@endpush

@endsection
