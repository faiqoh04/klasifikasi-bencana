@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold">Riwayat Prediksi</h2>
            <p class="text-muted">Daftar log semua hasil prediksi klasifikasi tingkat keparahan bencana</p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check fa-2x me-3 text-success"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-xmark fa-2x me-3 text-danger"></i>
                <div>
                    <strong>Gagal!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-semibold text-primary">
                <i class="fa fa-filter me-2"></i> Filter Riwayat
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('history.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="year" class="form-label small text-muted">Tahun Kejadian</label>
                        <select name="year" id="year" class="form-select form-control">
                            <option value="">Semua Tahun</option>
                            @for($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="regency" class="form-label small text-muted">Kabupaten / Kota</label>
                        <input type="text" name="regency" id="regency" class="form-control" placeholder="Cari Kabupaten/Kota..." value="{{ request('regency') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="type" class="form-label small text-muted">Jenis Bencana</label>
                        <input type="text" name="type" id="type" class="form-control" placeholder="Cari Jenis Bencana..." value="{{ request('type') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="level" class="form-label small text-muted">Tingkat Keparahan</label>
                        <select name="level" id="level" class="form-select form-control">
                            <option value="">Semua Tingkat</option>
                            <option value="RENDAH" {{ request('level') == 'RENDAH' ? 'selected' : '' }}>RENDAH</option>
                            <option value="SEDANG" {{ request('level') == 'SEDANG' ? 'selected' : '' }}>SEDANG</option>
                            <option value="TINGGI" {{ request('level') == 'TINGGI' ? 'selected' : '' }}>TINGGI</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <a href="{{ route('history.index') }}" class="btn btn-light me-2">
                            <i class="fa fa-sync me-2"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa fa-search me-2"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Tanggal Prediksi</th>
                            <th class="py-3">ID Logs</th>
                            <th class="py-3">Jenis Bencana</th>
                            <th class="py-3">Kabupaten / Kota</th>
                            <th class="py-3">Hasil Prediksi</th>
                            <th class="py-3 text-center">Probabilitas Model</th>
                            <th class="py-3 text-center">Algoritma</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            @php
                                $disaster = $history->historicalDisaster;
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold text-slate-800">{{ $history->prediction_date ? $history->prediction_date->format('d M Y H:i') : '-' }}</span>
                                </td>
                                <td>{{ $disaster->bpbd_log_id ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $disaster->disaster_type ?? 'Lain-lain' }}</span>
                                </td>
                                <td>{{ $disaster->regency ?? '-' }}</td>
                                <td>
                                    @if($history->predicted_level === 'TINGGI')
                                        <span class="badge-tinggi">Tinggi</span>
                                    @elseif($history->predicted_level === 'SEDANG')
                                        <span class="badge-sedang">Sedang</span>
                                    @else
                                        <span class="badge-rendah">Rendah</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2 small">
                                        <span class="text-success fw-bold" title="Rendah">{{ round($history->probability_low) }}%</span>
                                        <span class="text-muted">|</span>
                                        <span class="text-warning-dark fw-bold" title="Sedang">{{ round($history->probability_medium) }}%</span>
                                        <span class="text-muted">|</span>
                                        <span class="text-danger fw-bold" title="Tinggi">{{ round($history->probability_high) }}%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 10px;">{{ $history->algorithm }}</span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('history.show', $history->id) }}" class="btn btn-sm btn-outline-primary rounded-3">
                                        <i class="fa fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Belum ada riwayat prediksi yang sesuai dengan filter.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($histories->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Menampilkan {{ $histories->firstItem() }} sampai {{ $histories->lastItem() }} dari {{ $histories->total() }} data</span>
                    </div>
                    <div>
                        {{ $histories->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
