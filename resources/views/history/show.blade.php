@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold">Detail Riwayat Prediksi</h2>
            <p class="text-muted">Informasi lengkap hasil prediksi serta parameter input data kejadian bencana</p>
        </div>
        <div class="col-md-6 text-md-end text-start">
            <a href="{{ route('history.index') }}" class="btn btn-light shadow-sm">
                <i class="fa fa-arrow-left me-2"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    @php
        $disaster = $history->historicalDisaster;
    @endphp

    <div class="row g-4">
        {{-- ============================================================
        LEFT COLUMN: HASIL PREDIKSI & MODEL INFO
        ============================================================ --}}
        <div class="col-lg-5">
            {{-- Hasil Prediksi Card --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-primary">
                        <i class="fa fa-square-poll-vertical me-2"></i> Hasil Prediksi Model
                    </h5>
                </div>
                <div class="card-body">
                    
                    @php
                        $badgeClass = '';
                        $wrapperClass = '';
                        if ($history->predicted_level === 'TINGGI') {
                            $badgeClass = 'text-danger';
                            $wrapperClass = 'bg-danger-subtle text-danger';
                        } elseif ($history->predicted_level === 'SEDANG') {
                            $badgeClass = 'text-warning-dark';
                            $wrapperClass = 'bg-warning-subtle text-warning-dark';
                        } else {
                            $badgeClass = 'text-success';
                            $wrapperClass = 'bg-success-subtle text-success';
                        }
                    @endphp

                    <div class="text-center py-4 rounded-4 mb-4 {{ $wrapperClass }}">
                        <div class="mb-1 uppercase-label small text-muted" style="letter-spacing: 1px;">Tingkat Keparahan</div>
                        <h1 class="fw-black mb-1 {{ $badgeClass }}" style="font-size: 44px; letter-spacing: 1px;">
                            {{ $history->predicted_level }}
                        </h1>
                        <span class="small text-muted">Diprediksi pada {{ $history->prediction_date ? $history->prediction_date->format('d M Y H:i') : '-' }}</span>
                    </div>

                    {{-- Probabilities --}}
                    <p class="fw-bold text-slate-700 mb-3" style="font-size: 13px;">Distribusi Probabilitas Model</p>
                    <div class="d-flex flex-column gap-3 mb-4">
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold text-success small">Rendah</span>
                                <span class="fw-bold text-success small">{{ round($history->probability_low, 2) }}%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $history->probability_low }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold text-warning-dark small">Sedang</span>
                                <span class="fw-bold text-warning-dark small">{{ round($history->probability_medium, 2) }}%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $history->probability_medium }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-semibold text-danger small">Tinggi</span>
                                <span class="fw-bold text-danger small">{{ round($history->probability_high, 2) }}%</span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $history->probability_high }}%"></div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-borderless small mb-0 bg-light rounded-3 p-2">
                        <tr>
                            <td class="text-muted py-1" style="width: 140px;">Algoritma Model</td>
                            <td class="py-1 fw-semibold">: {{ $history->algorithm }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Tipe Input</td>
                            <td class="py-1 fw-semibold">: {{ $disaster->bpbd_log_id ? 'Pencatatan Resmi (BPBD)' : 'Klasifikasi Input Manual' }}</td>
                        </tr>
                    </table>

                </div>
            </div>

            {{-- Map Location --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-primary">
                        <i class="fa fa-map-location-dot me-2"></i> Peta Lokasi Kejadian
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($disaster && $disaster->latitude && $disaster->longitude)
                        <div id="map" style="height: 280px; border-radius: 0 0 18px 18px; z-index: 1;"></div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-map-location fa-3x mb-3" style="opacity: 0.3;"></i>
                            <p class="mb-0 small">Titik koordinat GPS tidak tersedia.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================================
        RIGHT COLUMN: DISASTER ATTRIBUTE DETAILS
        ============================================================ --}}
        <div class="col-lg-7">
            {{-- Basic Info Card --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $disaster->disaster_type ?? 'Lain-Lain' }}</span>
                        <span class="badge bg-secondary px-3 py-2 fs-6">{{ $disaster->status ?? 'Selesai' }}</span>
                    </div>

                    <h3 class="fw-bold mb-3">{{ $disaster->regency ?? '-' }}</h3>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless small">
                                <tr>
                                    <td class="text-muted py-1" style="width: 140px;">ID Log BPBD</td>
                                    <td class="py-1 fw-semibold">: {{ $disaster->bpbd_log_id ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Tanggal Kejadian</td>
                                    <td class="py-1 fw-semibold">: {{ $disaster->event_date ? $disaster->event_date->format('d F Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Kondisi Cuaca</td>
                                    <td class="py-1 fw-semibold">: {{ $disaster->weather ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless small">
                                <tr>
                                    <td class="text-muted py-1" style="width: 140px;">Area Spesifik</td>
                                    <td class="py-1 fw-semibold">: {{ $disaster->area ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Sumber Data</td>
                                    <td class="py-1 fw-semibold">: {{ $disaster->source ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Taksiran Kerugian</td>
                                    <td class="py-1 fw-semibold text-danger">: {{ $disaster->losses ? 'Rp ' . number_format($disaster->losses, 0, ',', '.') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Impact Statistics --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-sm-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-3 bg-danger-subtle text-danger">
                        <small class="fw-bold">Meninggal</small>
                        <h2 class="fw-bold my-1 text-danger">{{ $disaster->dead }}</h2>
                        <span class="small text-muted">Jiwa</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-3 bg-warning-subtle text-warning-dark">
                        <small class="fw-bold">Hilang</small>
                        <h2 class="fw-bold my-1 text-warning-dark">{{ $disaster->missing }}</h2>
                        <span class="small text-muted">Jiwa</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-3 bg-info-subtle text-info-dark">
                        <small class="fw-bold">Luka Berat</small>
                        <h2 class="fw-bold my-1 text-info-dark">{{ $disaster->serious_wound }}</h2>
                        <span class="small text-muted">Jiwa</span>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-3 bg-light text-dark">
                        <small class="fw-bold">Luka Ringan</small>
                        <h2 class="fw-bold my-1 text-dark">{{ $disaster->minor_injuries }}</h2>
                        <span class="small text-muted">Jiwa</span>
                    </div>
                </div>
            </div>

            {{-- Narratives (Chronology, Damage, Response) --}}
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-primary">
                        <i class="fa fa-house-damage me-2"></i> Rincian Kerusakan Fisik
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lh-base mb-0" style="text-align: justify; white-space: pre-line;">
                        {{ $disaster->damage ?? 'Rincian kerusakan tidak dicatatkan.' }}
                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-primary">
                        <i class="fa fa-align-left me-2"></i> Kronologi Kejadian
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lh-base mb-0" style="text-align: justify; white-space: pre-line;">
                        {{ $disaster->chronology ?? 'Kronologi kejadian tidak dicatatkan.' }}
                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold text-primary">
                        <i class="fa fa-kit-medical me-2"></i> Respon & Penanganan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="lh-base mb-0" style="text-align: justify; white-space: pre-line;">
                        {{ $disaster->response ?? 'Upaya penanganan tidak dicatatkan.' }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

@if($disaster && $disaster->latitude && $disaster->longitude)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var lat = {{ $disaster->latitude }};
        var lng = {{ $disaster->longitude }};
        var regency = "{{ $disaster->regency }}";
        var type = "{{ $disaster->disaster_type }}";

        // Initialize Leaflet Map
        var map = L.map('map').setView([lat, lng], 12);

        // OSM Tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker with popup
        L.marker([lat, lng]).addTo(map)
            .bindPopup('<strong>' + regency + '</strong><br>' + type)
            .openPopup();
    });
</script>
@endpush
@endif
@endsection
