@extends('layouts.app')

@section('content')

<div class="container-fluid pb-4">

    {{-- ============================================================
    HEADER
    ============================================================ --}}
    <div class="row align-items-center mb-3">
        <div class="col">
            <h3 class="fw-bold mb-0 text-slate-800">Klasifikasi Tingkat Keparahan Bencana</h3>
            <small class="text-muted">Jalankan prediksi keparahan bencana secara instan (Single) atau massal (Batch dari Excel)</small>
        </div>
    </div>



    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check fa-lg me-2 text-success"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-xmark fa-lg me-2 text-danger"></i>
                <div>
                    <strong>Gagal!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ============================================================
    TAB NAVIGATION
    ============================================================ --}}
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="pills-manual-tab" data-bs-toggle="pill" data-bs-target="#pills-manual" type="button" role="tab" aria-controls="pills-manual" aria-selected="true" style="border-radius: 8px;">
                <i class="fa fa-keyboard me-2"></i>Klasifikasi Manual
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="pills-batch-tab" data-bs-toggle="pill" data-bs-target="#pills-batch" type="button" role="tab" aria-controls="pills-batch" aria-selected="false" style="border-radius: 8px;">
                <i class="fa fa-file-excel me-2"></i>Klasifikasi Batch (Excel)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        {{-- ============================================================
        TAB 1: KLASIFIKASI MANUAL
        ============================================================ --}}
        <div class="tab-pane fade show active" id="pills-manual" role="tabpanel" aria-labelledby="pills-manual-tab">
            <div class="row g-4">
                
                {{-- Form Input --}}
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                            <div class="icon-box bg-primary-subtle text-primary">
                                <i class="fa fa-brain"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 15px;">Form Input Atribut Bencana</h5>
                                <small class="text-muted" style="font-size: 11px;">Atribut lengkap disesuaikan dengan data historis</small>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            {{-- Section 1: Informasi Dasar --}}
                            <p class="fw-bold text-primary mb-3" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fa fa-circle-info me-1"></i> Informasi Dasar
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">ID Log BPBD</label>
                                    <input type="number" id="bpbd_log_id" class="form-control form-control-sm border" placeholder="Contoh: 14022">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Tanggal Kejadian</label>
                                    <input type="date" id="event_date" class="form-control form-control-sm border" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Jenis Bencana</label>
                                    <select id="disaster_type_select" class="form-select form-select-sm border">
                                        @foreach($disasterTypesList as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Lokasi (Kabupaten/Kota)</label>
                                    <select id="regency_select" class="form-select form-select-sm border">
                                        @foreach($regenciesList as $regency)
                                            <option value="{{ $regency }}">{{ $regency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Area Spesifik (Kec/Desa)</label>
                                    <input type="text" id="area" class="form-control form-control-sm border" placeholder="Contoh: Kec. Mojoagung">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Kondisi Cuaca</label>
                                    <input type="text" id="weather" class="form-control form-control-sm border" placeholder="Contoh: Hujan Lebat">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Status Penanganan</label>
                                    <input type="text" id="status" class="form-control form-control-sm border" value="Selesai">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Sumber Data</label>
                                    <input type="text" id="source" class="form-control form-control-sm border" value="BPBD Jatim">
                                </div>
                            </div>

                            {{-- Section 2: Koordinat & Taksiran Kerugian --}}
                            <p class="fw-bold text-primary mb-3" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fa fa-location-crosshairs me-1"></i> Geografis & Finansial
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Latitude</label>
                                    <input type="number" id="latitude" class="form-control form-control-sm border" step="any" placeholder="-7.8239">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Longitude</label>
                                    <input type="number" id="longitude" class="form-control form-control-sm border" step="any" placeholder="112.0123">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Taksiran Kerugian (Rp)</label>
                                    <input type="number" id="losses" class="form-control form-control-sm border" placeholder="Contoh: 15000000">
                                </div>
                            </div>

                            {{-- Section 3: Korban Jiwa (ML Input) --}}
                            <p class="fw-bold text-primary mb-3" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fa fa-users-viewfinder me-1"></i> Dampak Korban (Parameter Prediksi)
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Meninggal</label>
                                    <input type="number" id="dead" class="form-control form-control-sm border" value="0" min="0">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Hilang</label>
                                    <input type="number" id="missing" class="form-control form-control-sm border" value="0" min="0">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Luka Berat</label>
                                    <input type="number" id="serious_wound" class="form-control form-control-sm border" value="0" min="0">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Luka Ringan</label>
                                    <input type="number" id="minor_injuries" class="form-control form-control-sm border" value="0" min="0">
                                </div>
                            </div>

                            {{-- Section 4: Deskripsi & Penanganan --}}
                            <p class="fw-bold text-primary mb-3" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fa fa-building-circle-exclamation me-1"></i> Kerusakan (Parameter Prediksi)
                            </p>

                            {{-- Hidden field untuk teks damage yang di-generate --}}
                            <input type="hidden" id="damage">

                            <div class="row g-2 align-items-end mb-3">
                                <div class="col-md-5">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 11px; font-weight: 600;">Entitas Kerusakan</label>
                                    <select id="dmg-entity-select" class="form-select form-select-sm border" style="font-size: 12px;">
                                        <option value="rumah">🏠 Rumah</option>
                                        <option value="sekolah">🏫 Sekolah</option>
                                        <option value="puskesmas">🏥 Fasilitas Kesehatan</option>
                                        <option value="masjid">🕌 Tempat Ibadah</option>
                                        <option value="kantor">🏢 Kantor/Pemerintahan</option>
                                        <option value="pasar">🏪 Pasar</option>
                                        <option value="toko">🏬 Usaha/UMKM</option>
                                        <option value="jembatan">🌉 Jembatan</option>
                                        <option value="lahan">🌾 Lahan/Sawah</option>
                                        <option value="jalan">🛤️ Jalan</option>
                                        <option value="tanggul">🏞️ Tanggul/Talud</option>
                                        <option value="saluran">💧 Drainase/Saluran</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 11px; font-weight: 600;">Kondisi Kerusakan</label>
                                    <select id="dmg-cond-select" class="form-select form-select-sm border" style="font-size: 12px;">
                                        <!-- Dinamis via JS -->
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label id="dmg-val-label" class="form-label text-slate-600 mb-1" style="font-size: 11px; font-weight: 600;">Jumlah (unit)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" id="dmg-val-input" class="form-control border" min="0.01" step="any" placeholder="0">
                                        <button type="button" id="btn-add-damage" class="btn btn-primary fw-bold">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Container List Badges Kerusakan --}}
                            <div id="damage-list-container" class="d-flex flex-wrap gap-2 p-2 rounded-3 border mb-3" style="background: #f8fafc; min-height: 42px; align-content: center;">
                                <span class="text-muted small w-100 text-center" id="empty-dmg-msg" style="font-size: 11px;">Belum ada data kerusakan yang ditambahkan.</span>
                            </div>

                            {{-- Narasi & Penanganan --}}
                            <p class="fw-bold text-primary mb-3" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fa fa-file-text me-1"></i> Narasi &amp; Penanganan
                            </p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Kronologi Kejadian</label>
                                    <textarea id="chronology" class="form-control form-control-sm border" rows="2" placeholder="Jelaskan kronologi kejadian secara singkat..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Upaya Respon / Penanganan</label>
                                    <textarea id="response" class="form-control form-control-sm border" rows="2" placeholder="Contoh: BPBD mendirikan tenda darurat..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-slate-600 mb-1" style="font-size: 12px;">Tautan Foto Kejadian</label>
                                    <input type="text" id="photos" class="form-control form-control-sm border" placeholder="Contoh: https://link-foto.com/bencana.jpg">
                                </div>
                            </div>

                            <button id="btnPredict" class="btn btn-primary w-100 fw-semibold">
                                <i class="fa fa-brain me-2"></i> Prediksi & Simpan ke Database
                            </button>

                        </div>
                    </div>
                </div>

                {{-- Hasil Prediksi --}}
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 rounded-3 position-sticky" style="top: 24px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 15px;">
                                <i class="fa fa-square-poll-vertical text-primary me-2"></i>Panel Hasil Prediksi
                            </h5>
                        </div>
                        <div class="card-body">
                            
                            {{-- Idle --}}
                            <div id="idleState" class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fa fa-brain text-muted" style="font-size: 48px; opacity: 0.25;"></i>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 13px;">
                                    Isi data bencana secara lengkap,<br>
                                    kemudian jalankan klasifikasi.
                                </p>
                            </div>

                            {{-- Result --}}
                            <div id="predictionResult" class="d-none">
                                <div class="result-badge-wrapper text-center py-4 rounded-3 mb-4" id="resultBadgeWrapper">
                                    <div class="mb-2 text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Hasil Prediksi</div>
                                    <h1 id="prediction" class="fw-black mb-1" style="font-size: 48px; letter-spacing: 1px;">-</h1>
                                    <div id="resultSubtitle" class="small text-muted px-2"></div>
                                </div>

                                <p class="fw-semibold text-slate-700 mb-3" style="font-size: 13px;">Probabilitas Kelas</p>
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-semibold text-success small">Rendah</span>
                                            <span id="low_prob" class="fw-bold text-success small">0%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div id="low_progress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-semibold text-warning-dark small">Sedang</span>
                                            <span id="medium_prob" class="fw-bold text-warning-dark small">0%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div id="medium_progress" class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="fw-semibold text-danger small">Tinggi</span>
                                            <span id="high_prob" class="fw-bold text-danger small">0%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div id="high_progress" class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
                                        </div>
                                    </div>
                                </div>

                                <button id="btnReset" class="btn btn-outline-secondary btn-sm w-100 mb-3">
                                    <i class="fa fa-redo me-1"></i> Reset Form
                                </button>


                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ============================================================
        TAB 2: KLASIFIKASI BATCH (EXCEL)
        ============================================================ --}}
        <div class="tab-pane fade" id="pills-batch" role="tabpanel" aria-labelledby="pills-batch-tab">
            <div class="row g-4">
                
                {{-- Form Import --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="fa fa-file-import me-2"></i>Unggah Spreadsheet Excel
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('classification.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4 text-center p-5 border border-2 border-dashed rounded-3 bg-light" style="cursor: pointer;" onclick="document.getElementById('file').click()">
                                    <i class="fa-solid fa-file-excel fa-4x text-success mb-3"></i>
                                    <h5>Pilih File Excel Kejadian Bencana</h5>
                                    <p class="text-muted small mb-0">Mendukung format .xlsx atau .xls (Maks. 20MB)</p>
                                    <input type="file" name="file" id="file" class="form-control d-none" required accept=".xlsx, .xls">
                                    <div id="file-name-display" class="mt-3 text-success fw-semibold small"></div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm" id="btn-submit">
                                    <i class="fa fa-play me-2"></i>Jalankan Batch Klasifikasi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Ketentuan Format --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 15px;">
                                <i class="fa fa-circle-info me-2 text-primary"></i>Ketentuan & Format Batch
                            </h5>
                        </div>
                        <div class="card-body" style="font-size: 13px;">
                            <p class="text-muted">Sistem akan membaca setiap baris data bencana, memanggil model machine learning untuk memprediksi tingkat keparahannya secara otomatis, dan menyimpannya langsung ke database.</p>
                            <ul class="list-group list-group-flush mb-0">
                                <li class="list-group-item px-0 bg-transparent">
                                    <strong>Struktur Sheet:</strong> Nama sheet harus berupa tahun seperti: <code>2020, 2021, 2022, 2023, 2024, 2025</code>.
                                </li>
                                <li class="list-group-item px-0 bg-transparent">
                                    <strong>Header Kolom:</strong> Baris pertama harus berisi kolom:
                                    <div class="text-secondary mt-1">
                                        <code>ID Logs</code>, <code>Disaster Type</code>, <code>Event Date</code>, <code>Regency</code>, <code>Area</code>, <code>Latitude</code>, <code>Longitude</code>, <code>Weather</code>, <code>Chronology</code>, <code>Dead</code>, <code>Missing</code>, <code>Serious Wound</code>, <code>Minor Injuries</code>, <code>Damage</code>, <code>Losses</code>, <code>Response</code>, <code>Photos</code>, <code>Source</code>, <code>Status</code>
                                    </div>
                                </li>
                                <li class="list-group-item px-0 bg-transparent">
                                    <strong>Tanpa Kolom Level:</strong> Kolom <code>Level</code> tidak wajib diisi karena tingkat keparahan akan dihasilkan langsung dari prediksi model Random Forest kami.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@push('scripts')
<style>
.text-slate-800 { color: #1e293b; }
.text-slate-700 { color: #334155; }
.text-slate-600 { color: #475569; }
.text-muted     { color: #64748b !important; }
.text-warning-dark { color: #d97706; }
.bg-warning-subtle  { background-color: #fef3c7; }
.icon-box {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.fw-black { font-weight: 900; }
.result-badge-wrapper {
    transition: background 0.4s ease;
}
.result-rendah { background: #f0fdf4; }
.result-sedang { background: #fffbeb; }
.result-tinggi { background: #fef2f2; }
</style>

<script>
// ============================================================
// DYNAMIC DAMAGE LIST BUILDER
// ============================================================
const dmgConfig = {
    rumah: {
        name: "🏠 Rumah",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" },
            { val: "tergenang", label: "Tergenang" },
            { val: "terendam", label: "Terendam" },
            { val: "terdampak", label: "Terdampak" }
        ]
    },
    sekolah: {
        name: "🏫 Sekolah",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    puskesmas: {
        name: "🏥 Fasilitas Kesehatan",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    masjid: {
        name: "🕌 Tempat Ibadah",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    kantor: {
        name: "🏢 Kantor/Pemerintahan",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    pasar: {
        name: "🏪 Pasar",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    toko: {
        name: "🏬 Usaha/UMKM",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    jembatan: {
        name: "🌉 Jembatan",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "roboh", label: "Roboh" },
            { val: "ambruk", label: "Ambruk" },
            { val: "hancur", label: "Hancur" },
            { val: "rusak sedang", label: "Rusak Sedang" },
            { val: "rusak ringan", label: "Rusak Ringan" },
            { val: "retak", label: "Retak-retak" }
        ]
    },
    lahan: {
        name: "🌾 Lahan/Sawah",
        unit: "ha",
        conditions: [
            { val: "tergenang", label: "Tergenang" },
            { val: "terendam", label: "Terendam" },
            { val: "terbakar", label: "Terbakar" },
            { val: "kebakaran", label: "Kebakaran" }
        ]
    },
    jalan: {
        name: "🛤️ Jalan",
        unit: "meter-panjang",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "longsor", label: "Longsor" },
            { val: "putus", label: "Putus/Terputus" },
            { val: "ambrol", label: "Ambrol" },
            { val: "tergenang", label: "Tergenang" },
            { val: "terendam", label: "Terendam" }
        ]
    },
    tanggul: {
        name: "🏞️ Tanggul/Talud",
        unit: "custom",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat (unit)", unit: "unit" },
            { val: "rusak sedang", label: "Rusak Sedang (unit)", unit: "unit" },
            { val: "rusak ringan", label: "Rusak Ringan (unit)", unit: "unit" },
            { val: "jebol", label: "Jebol (meter)", unit: "meter-panjang" },
            { val: "ambrol", label: "Ambrol (meter)", unit: "meter-panjang" },
            { val: "longsor", label: "Longsor (meter)", unit: "meter-panjang" }
        ]
    },
    saluran: {
        name: "💧 Drainase/Saluran",
        unit: "unit",
        conditions: [
            { val: "rusak berat", label: "Rusak Berat" },
            { val: "jebol", label: "Jebol" },
            { val: "ambrol", label: "Ambrol" }
        ]
    }
};

let addedDamages = [];

const entitySelect = document.getElementById("dmg-entity-select");
const condSelect   = document.getElementById("dmg-cond-select");
const valInput     = document.getElementById("dmg-val-input");
const valLabel     = document.getElementById("dmg-val-label");
const addBtn       = document.getElementById("btn-add-damage");
const listContainer = document.getElementById("damage-list-container");
const hiddenDmg    = document.getElementById("damage");

function updateCondOptions() {
    const entity = entitySelect.value;
    const cfg = dmgConfig[entity];
    condSelect.innerHTML = "";
    
    cfg.conditions.forEach(c => {
        const opt = document.createElement("option");
        opt.value = c.val;
        opt.textContent = c.label;
        if (c.unit) opt.dataset.unit = c.unit;
        condSelect.appendChild(opt);
    });
    
    updateUnitLabel();
}

function updateUnitLabel() {
    const entity = entitySelect.value;
    const cfg = dmgConfig[entity];
    let unit = cfg.unit;
    
    // Khusus tanggul, unit bisa dinamis tergantung kondisi terpilih
    if (entity === 'tanggul') {
        const selectedOpt = condSelect.selectedOptions[0];
        if (selectedOpt && selectedOpt.dataset.unit) {
            unit = selectedOpt.dataset.unit;
        }
    }
    
    let text = "Jumlah (unit)";
    if (unit === 'ha') text = "Luas (Ha)";
    if (unit === 'meter-panjang') text = "Panjang (meter)";
    
    valLabel.textContent = text;
}

function renderDamageList() {
    listContainer.innerHTML = "";
    if (addedDamages.length === 0) {
        listContainer.innerHTML = '<span class="text-muted small w-100 text-center" id="empty-dmg-msg" style="font-size: 11px;">Belum ada data kerusakan yang ditambahkan.</span>';
        hiddenDmg.value = "";
        return;
    }
    
    const textParts = [];
    
    addedDamages.forEach((item, idx) => {
        const cfg = dmgConfig[item.entity];
        let unitText = item.unit;
        if (unitText === 'meter-panjang') unitText = 'meter';
        
        let displayLabel = `${item.value} ${unitText} ${cfg.name} ${item.cond}`;
        if (item.entity === 'lahan') {
            displayLabel = `${item.value} ${item.unit} ${item.entity} ${item.cond}`;
        }
        
        // Buat element badge
        const badge = document.createElement("span");
        badge.className = "badge d-inline-flex align-items-center gap-1 py-1.5 px-2 bg-light border text-slate-700 rounded-3";
        badge.style.fontSize = "11px";
        badge.innerHTML = `
            <span>${item.value} ${unitText} ${cfg.name} ${item.cond}</span>
            <i class="fa fa-times text-danger ms-1 cursor-pointer delete-dmg-item" data-index="${idx}" style="cursor: pointer;"></i>
        `;
        listContainer.appendChild(badge);
        
        // Bangun string untuk model
        let segment = '';
        if (item.unit === 'ha') {
            segment = `${item.value} hektar ${item.entity} ${item.cond}`;
        } else if (item.unit === 'meter-panjang') {
            segment = `panjang ${item.value} meter ${item.entity} ${item.cond}`;
        } else {
            segment = `${item.value} ${item.entity} ${item.cond}`;
        }
        textParts.push(segment);
    });
    
    hiddenDmg.value = textParts.join(', ');
}

entitySelect.addEventListener("change", updateCondOptions);
condSelect.addEventListener("change", updateUnitLabel);

addBtn.addEventListener("click", function() {
    const entity = entitySelect.value;
    const cond   = condSelect.value;
    const val    = parseFloat(valInput.value);
    
    if (isNaN(val) || val <= 0) {
        alert("Masukkan jumlah/nilai kerusakan yang valid!");
        return;
    }
    
    const cfg = dmgConfig[entity];
    let unit = cfg.unit;
    if (entity === 'tanggul') {
        const selectedOpt = condSelect.selectedOptions[0];
        if (selectedOpt && selectedOpt.dataset.unit) {
            unit = selectedOpt.dataset.unit;
        }
    }
    
    // Periksa apakah sudah ada entitas + kondisi yang sama
    const existingIdx = addedDamages.findIndex(item => item.entity === entity && item.cond === cond);
    if (existingIdx !== -1) {
        addedDamages[existingIdx].value = val; // update nilai jika sudah ada
    } else {
        addedDamages.push({ entity, cond, value: val, unit });
    }
    
    valInput.value = "";
    renderDamageList();
});

listContainer.addEventListener("click", function(e) {
    if (e.target.classList.contains("delete-dmg-item")) {
        const idx = parseInt(e.target.dataset.index);
        addedDamages.splice(idx, 1);
        renderDamageList();
    }
});

// Jalankan load awal option
updateCondOptions();

// File display helper
document.getElementById('file').addEventListener('change', function(e) {
    const fileName = e.target.files[0] ? e.target.files[0].name : '';
    const displayDiv = document.getElementById('file-name-display');
    if (fileName) {
        displayDiv.innerHTML = '<i class="fa fa-check-circle me-1"></i> File terpilih: <strong>' + fileName + '</strong>';
    } else {
        displayDiv.innerHTML = '';
    }
});

// Loading state batch submission
document.getElementById('btn-submit').addEventListener('click', function(e) {
    const fileInput = document.getElementById('file');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Silakan pilih file Excel terlebih dahulu!');
        e.preventDefault();
        return false;
    }
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengklasifikasikan & Mengimpor data...';
    this.disabled = true;
    this.closest('form').submit();
});

const btnPredict = document.getElementById("btnPredict");
const btnReset   = document.getElementById("btnReset");

btnPredict.addEventListener("click", function () {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Memproses Klasifikasi...';

    const payload = {
        bpbd_log_id:     document.getElementById("bpbd_log_id").value || null,
        disaster_type:   document.getElementById("disaster_type_select").value,
        event_date:      document.getElementById("event_date").value,
        regency:         document.getElementById("regency_select").value,
        area:            document.getElementById("area").value || null,
        latitude:        document.getElementById("latitude").value || null,
        longitude:       document.getElementById("longitude").value || null,
        weather:         document.getElementById("weather").value || null,
        chronology:      document.getElementById("chronology").value || null,
        dead:            Number(document.getElementById("dead").value),
        missing:         Number(document.getElementById("missing").value),
        serious_wound:   Number(document.getElementById("serious_wound").value),
        minor_injuries:  Number(document.getElementById("minor_injuries").value),
        damage:          document.getElementById("damage").value || null,
        losses:          document.getElementById("losses").value || null,
        response:        document.getElementById("response").value || null,
        photos:          document.getElementById("photos").value || null,
        source:          document.getElementById("source").value || null,
        status:          document.getElementById("status").value || null,
    };

    fetch("{{ route('classification.predict') }}", {
        method: "POST",
        headers: { 
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(result => {
        if(result.status === 'error') {
            alert(result.message);
            return;
        }

        const pred    = result.data.prediction;
        const badgeEl = document.getElementById("prediction");
        const wrapper = document.getElementById("resultBadgeWrapper");
        const subtitle = document.getElementById("resultSubtitle");

        badgeEl.textContent = pred;

        // Styling badge hasil
        wrapper.className = 'result-badge-wrapper text-center py-4 rounded-3 mb-4';
        if (pred === 'TINGGI') {
            badgeEl.className = 'fw-black mb-1 text-danger';
            wrapper.classList.add('result-tinggi');
        } else if (pred === 'SEDANG') {
            badgeEl.className = 'fw-black mb-1 text-warning-dark';
            wrapper.classList.add('result-sedang');
        } else {
            badgeEl.className = 'fw-black mb-1 text-success';
            wrapper.classList.add('result-rendah');
        }

        subtitle.innerHTML = 'Bencana berhasil disimpan ke Database.<br><a href="/history/' + result.data.history_id + '" class="fw-bold text-primary text-decoration-underline"><i class="fa fa-eye me-1"></i>Lihat Detail Riwayat &rarr;</a>';

        // Probabilitas
        const lowP    = result.data.probability.RENDAH * 100;
        const mediumP = result.data.probability.SEDANG * 100;
        const highP   = result.data.probability.TINGGI * 100;

        document.getElementById("low_prob").textContent    = lowP.toFixed(2) + "%";
        document.getElementById("medium_prob").textContent = mediumP.toFixed(2) + "%";
        document.getElementById("high_prob").textContent   = highP.toFixed(2) + "%";

        setTimeout(() => {
            document.getElementById("low_progress").style.width    = lowP + "%";
            document.getElementById("medium_progress").style.width = mediumP + "%";
            document.getElementById("high_progress").style.width   = highP + "%";
        }, 50);

        // Tampilkan panel hasil
        document.getElementById("idleState").classList.add("d-none");
        document.getElementById("predictionResult").classList.remove("d-none");
    })
    .catch(err => {
        alert('Terjadi kesalahan koneksi ke server.');
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-brain me-2"></i> Prediksi & Simpan ke Database';
    });
});

btnReset.addEventListener("click", function () {
    // Reset form fields
    document.getElementById("bpbd_log_id").value    = "";
    document.getElementById("area").value           = "";
    document.getElementById("weather").value        = "";
    document.getElementById("latitude").value       = "";
    document.getElementById("longitude").value      = "";
    document.getElementById("losses").value         = "";
    document.getElementById("chronology").value     = "";
    document.getElementById("response").value       = "";
    document.getElementById("photos").value         = "";
    document.getElementById("dead").value           = 0;
    document.getElementById("missing").value        = 0;
    document.getElementById("serious_wound").value  = 0;
    document.getElementById("minor_injuries").value = 0;

    // Reset dynamic list builder
    addedDamages = [];
    valInput.value = "";
    renderDamageList();

    // Reset progress bars
    ["low_progress","medium_progress","high_progress"].forEach(id => {
        document.getElementById(id).style.width = "0%";
    });

    // Kembali ke idle
    document.getElementById("predictionResult").classList.add("d-none");
    document.getElementById("idleState").classList.remove("d-none");
});
</script>
@endpush

@endsection
