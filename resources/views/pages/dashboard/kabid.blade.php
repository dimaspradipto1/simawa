@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
    <h1>Dashboard Kemahasiswaan</h1>
    <p class="text-muted small mb-0">Panel Pengawasan, Pembinaan Organisasi Mahasiswa &amp; Verifikasi SKP Non-Akademik Universitas Ibnu Sina.</p>
    <nav class="mt-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard Kabid Kemahasiswaan</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <!-- Welcome Banner Eksekutif Kabid -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #065f46 0%, #059669 50%, #0d9488 100%); color: white; border-radius: 12px !important; box-shadow: 0 8px 24px rgba(5, 150, 105, 0.25) !important;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <span class="badge bg-white text-success px-3 py-1 mb-2 fw-bold" style="font-size: 0.78rem;">
                        <i class="bi bi-shield-check me-1"></i> KABID KEMAHASISWAAN &bull; TINGKAT UNIVERSITAS
                    </span>
                    <h3 class="fw-bold text-white mb-2">Selamat Datang, {{ $user->name }}!</h3>
                    <p class="mb-3 text-white-50" style="font-size: 0.95rem; line-height: 1.6;">
                        Anda bertindak sebagai pengawas dan verifikator utama untuk seluruh kegiatan <strong>Non-Akademik</strong> se-Universitas Ibnu Sina 
                        (Pengurus Ormawa BEM/DPM/HIMA/UKM, Kejuaraan Seni/Olahraga/Debat, Kepanitiaan, Relawan Bencana, serta Kegiatan Wajib Kampus), 
                        serta mengesahkan portofolio Transkrip SKPI mahasiswa sebelum wisuda.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('skp.index', ['sifat' => 'Non-Akademik']) }}" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="bi bi-hourglass-split fs-5"></i> Antrean Verifikasi ({{ $pendingCount }})
                        </a>
                        <a href="{{ route('rekapitulasi.index') }}" class="btn btn-light px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="bi bi-file-earmark-bar-graph fs-5"></i> Rekapitulasi &amp; Laporan
                        </a>
                        <a href="{{ route('skp.transkrip') }}" class="btn btn-outline-light px-3 py-2 fw-medium d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="bi bi-file-earmark-person fs-5"></i> Transkrip SKP Mahasiswa
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end d-none d-lg-block">
                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 d-inline-block text-start" style="min-width: 250px;">
                        <div class="small text-white-50 mb-1"><i class="bi bi-building me-1"></i> Wilayah Kerja:</div>
                        <div class="fw-bold text-white mb-2 fs-6">Universitas Ibnu Sina (UIS)</div>
                        <div class="small text-white-50 mb-1"><i class="bi bi-people-fill me-1"></i> Total Mahasiswa Ber-KTA:</div>
                        <div class="fw-semibold text-white fs-6">{{ $totalMahasiswaBerKta }} Mahasiswa Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Metrics Card -->
    <div class="row g-3 mb-4">
        <!-- 1. Menunggu Verifikasi Non-Akademik -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">Antrean Verifikasi</span>
                        <div class="rounded-circle p-2 bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $pendingCount }}</h3>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-muted">Berkas Non-Akademik</span>
                        @if($pendingCount > 0)
                            <a href="{{ route('skp.index', ['sifat' => 'Non-Akademik']) }}" class="small fw-bold text-warning text-decoration-none">
                                Review &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Disetujui -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">Telah Disetujui</span>
                        <div class="rounded-circle p-2 bg-success-subtle text-success">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $approvedCount }}</h3>
                    <span class="small text-muted">Kegiatan Valid Se-UIS</span>
                </div>
            </div>
        </div>

        <!-- 3. Total Poin SKP Non-Akademik -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #0284c7 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">Total Poin Non-Akademik</span>
                        <div class="rounded-circle p-2 bg-info-subtle text-info">
                            <i class="bi bi-award-fill fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($totalPoinNonAkademik, 0, ',', '.') }}</h3>
                    <span class="small text-muted">Akumulasi Poin Mahasiswa</span>
                </div>
            </div>
        </div>

        <!-- 4. Mahasiswa Ber-KTA Ormawa -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #6366f1 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">KTA Ormawa Aktif</span>
                        <div class="rounded-circle p-2 bg-primary-subtle text-primary">
                            <i class="bi bi-person-vcard-fill fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $totalMahasiswaBerKta }}</h3>
                    <span class="small text-muted">Pengurus Terdata Sistem</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Antrean Cepat Verifikasi Non-Akademik (Col 8) -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-warning fs-5"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Antrean Verifikasi Non-Akademik Terbaru</h6>
                            <small class="text-muted">Pengajuan kegiatan Ormawa, Lomba, Panitia, Relawan yang menunggu persetujuan Kabid.</small>
                        </div>
                    </div>
                    <a href="{{ route('skp.index', ['sifat' => 'Non-Akademik']) }}" class="btn btn-sm btn-outline-success px-3 rounded-2">
                        Lihat Semua ({{ $pendingCount }})
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pendingList->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check2-all text-success" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark mt-2">Tidak Ada Antrean Verifikasi</h6>
                            <p class="text-muted small mb-0">Seluruh pengajuan kegiatan Non-Akademik telah selesai diverifikasi.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Kegiatan &amp; Kategori</th>
                                        <th class="text-center">Poin</th>
                                        <th>Bukti</th>
                                        <th class="text-end">Aksi Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingList as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->nama_mahasiswa }}</div>
                                            <div class="text-muted small">NIM: {{ $item->nim }} &bull; {{ $item->program_studi }}</div>
                                            <div class="small text-secondary">{{ $item->fakultas }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 250px;" title="{{ $item->nama_kegiatan }}">
                                                {{ $item->nama_kegiatan }}
                                            </div>
                                            <div class="text-muted small">
                                                <span class="badge bg-light text-dark border">{{ $item->jenis_kegiatan ?: $item->kategori_id }}</span>
                                                @if($item->jabatan)
                                                    <span class="badge bg-primary-subtle text-primary">{{ $item->jabatan }}</span>
                                                @elseif($item->peringkat_juara)
                                                    <span class="badge bg-warning-subtle text-dark">{{ $item->peringkat_juara }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success px-2 py-1 fw-bold">+{{ $item->poin_skp }} Poin</span>
                                        </td>
                                        <td>
                                            @if($item->bukti_url)
                                                <a href="{{ $item->bukti_url }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Lihat Berkas Bukti">
                                                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Berkas
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModalKabid{{ $item->id }}">
                                                    <i class="bi bi-check-lg me-1"></i> Proses
                                                </button>
                                                <a href="{{ route('skp.show', $item->id) }}" class="btn btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>

                                            <!-- Modal Verifikasi Kabid -->
                                            <div class="modal fade text-start" id="verifyModalKabid{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <form action="{{ route('skp.verify', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title fs-6 fw-bold">
                                                                    <i class="bi bi-shield-check me-1"></i> Verifikasi SKP Non-Akademik (Kabid)
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                                                    <div class="small text-muted">Mahasiswa:</div>
                                                                    <div class="fw-bold text-dark">{{ $item->nama_mahasiswa }} ({{ $item->nim }})</div>
                                                                    <div class="small text-secondary">{{ $item->program_studi }} &bull; {{ $item->fakultas }}</div>
                                                                    <hr class="my-2">
                                                                    <div class="small text-muted">Kegiatan:</div>
                                                                    <div class="fw-semibold text-dark">{{ $item->nama_kegiatan }}</div>
                                                                    <div class="small text-success fw-bold mt-1">Bobot Poin SKP: {{ $item->poin_skp }} Poin</div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">Keputusan Verifikasi:</label>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="status" id="kabidSetuju{{ $item->id }}" value="Disetujui" checked>
                                                                            <label class="form-check-label text-success fw-bold" for="kabidSetuju{{ $item->id }}">
                                                                                <i class="bi bi-check-circle me-1"></i> Setujui
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="status" id="kabidTolak{{ $item->id }}" value="Ditolak">
                                                                            <label class="form-check-label text-danger fw-bold" for="kabidTolak{{ $item->id }}">
                                                                                <i class="bi bi-x-circle me-1"></i> Tolak / Perlu Revisi
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-0">
                                                                    <label class="form-label small fw-semibold">Catatan dari Kabid Kemahasiswaan (Opsional):</label>
                                                                    <textarea name="catatan_verifikator" class="form-control" rows="3" placeholder="Contoh: SK Pengurus Ormawa terverifikasi sah."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-success btn-sm px-3">
                                                                    <i class="bi bi-save me-1"></i> Simpan Keputusan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div><!-- End Modal -->
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sebaran Ormawa & Top Mahasiswa Non-Akademik (Col 4) -->
        <div class="col-lg-4">
            <!-- Card 1: Top Mahasiswa Non-Akademik -->
            <div class="card mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Top Prestasi Non-Akademik</h6>
                    </div>
                    <a href="{{ route('skp.transkrip') }}" class="small text-success text-decoration-none fw-semibold">
                        Semua &rarr;
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($topMahasiswa->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            Belum ada perolehan poin non-akademik disetujui.
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($topMahasiswa as $idx => $mhs)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge rounded-circle {{ $idx === 0 ? 'bg-warning text-dark' : ($idx === 1 ? 'bg-secondary text-white' : 'bg-light text-dark border') }}" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                                            {{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <a href="{{ route('skp.transkrip', $mhs->user_id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 170px;">
                                                {{ $mhs->nama_mahasiswa }}
                                            </a>
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">{{ $mhs->program_studi }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success fw-bold" style="font-size: 0.78rem;">{{ $mhs->total_poin }} Poin</span>
                                        <div class="small fw-semibold text-secondary" style="font-size: 0.7rem;">Grade {{ $mhs->grade }}</div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Card 2: Keaktifan Anggota Ormawa Terbanyak -->
            <div class="card mb-0">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-people-fill text-primary me-2"></i>Keanggotaan Ormawa Terbanyak
                    </h6>
                </div>
                <div class="card-body p-3">
                    @if(empty($topOrmawa))
                        <div class="text-center py-3 text-muted small">Belum ada data keanggotaan ormawa terdata.</div>
                    @else
                        @foreach($topOrmawa as $orgName => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="small fw-semibold text-dark text-truncate" style="max-width: 200px;">{{ $orgName }}</span>
                            <span class="badge bg-light text-primary border fw-bold">{{ $count }} Anggota</span>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Sebaran Capaian Non-Akademik per Fakultas se-UIS -->
    @if(count($fakultasLabels) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bar-chart-fill text-success me-2"></i>Perbandingan Capaian Kegiatan Non-Akademik Antar-Fakultas UIS
                    </h6>
                    <small class="text-muted">Total poin kegiatan non-akademik dan keaktifan mahasiswa di setiap fakultas.</small>
                </div>
                <div class="card-body p-4">
                    <div id="kabidFakultasChart" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const fLabels = @json($fakultasLabels);
    const fPoints = @json($fakultasPoints);
    const fKegiatan = @json($fakultasKegiatan);

    if (fLabels && fLabels.length > 0 && document.querySelector("#kabidFakultasChart")) {
        new ApexCharts(document.querySelector("#kabidFakultasChart"), {
            series: [{
                name: 'Total Poin SKP Disetujui',
                data: fPoints
            }, {
                name: 'Jumlah Kegiatan Valid',
                data: fKegiatan
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    endingShape: 'rounded',
                    borderRadius: 4
                },
            },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            xaxis: {
                categories: fLabels,
                labels: { style: { fontSize: '11px', fontWeight: 600 } }
            },
            yaxis: {
                title: { text: 'Poin / Jumlah' }
            },
            fill: { opacity: 1 },
            colors: ['#059669', '#0284c7'],
            tooltip: {
                y: {
                    formatter: function (val, opts) {
                        return opts.seriesIndex === 0 ? val + " Poin" : val + " Kegiatan";
                    }
                }
            }
        }).render();
    }
});
</script>
@endpush
@endsection
