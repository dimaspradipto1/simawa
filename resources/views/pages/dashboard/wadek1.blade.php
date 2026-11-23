@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
    <h1>Dashboard Wakil Dekan 1</h1>
    <p class="text-muted small mb-0">Panel Pengawasan &amp; Verifikasi Satuan Kredit Poin (SKP) Akademik {{ $fakultas }} Universitas Ibnu Sina.</p>
    <nav class="mt-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard Wakil Dekan 1</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <!-- Welcome Banner Dekanat -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%); color: white; border-radius: 12px !important; box-shadow: 0 8px 24px rgba(2, 132, 199, 0.25) !important;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <span class="badge bg-white text-primary px-3 py-1 mb-2 fw-bold" style="font-size: 0.78rem;">
                        <i class="bi bi-mortarboard-fill me-1"></i> WAKIL DEKAN 1 &bull; BIDANG AKADEMIK
                    </span>
                    <h3 class="fw-bold text-white mb-2">Selamat Datang, {{ $user->name }}!</h3>
                    <p class="mb-3 text-white-50" style="font-size: 0.95rem; line-height: 1.6;">
                        Anda bertindak sebagai verifikator utama untuk seluruh kegiatan <strong>Akademik</strong> mahasiswa 
                        <strong>{{ $fakultas }}</strong> (Karya Ilmiah SINTA/Scopus, Buku ber-ISBN, Paten/HKI, Seminar/Konferensi, dan Rekognisi Akademik), 
                        serta mengesahkan portofolio Transkrip SKPI mahasiswa sebelum yudisium.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('skp.index', ['sifat' => 'Akademik']) }}" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="bi bi-check2-circle fs-5"></i> Antrean Verifikasi ({{ $pendingCount }})
                        </a>
                        <a href="{{ route('skp.transkrip') }}" class="btn btn-outline-light px-3 py-2 fw-medium d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                            <i class="bi bi-file-earmark-person fs-5"></i> Transkrip SKP Mahasiswa
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end d-none d-lg-block">
                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 d-inline-block text-start" style="min-width: 240px;">
                        <div class="small text-white-50 mb-1"><i class="bi bi-buildings me-1"></i> Cakupan Dekanat:</div>
                        <div class="fw-bold text-white mb-2 fs-6">{{ $fakultas }}</div>
                        <div class="small text-white-50 mb-1"><i class="bi bi-calendar3 me-1"></i> Tanggal Sistem:</div>
                        <div class="fw-semibold text-white">{{ date('d F Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Metrics Card -->
    <div class="row g-3 mb-4">
        <!-- 1. Menunggu Verifikasi -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">Butuh Verifikasi</span>
                        <div class="rounded-circle p-2 bg-warning-subtle text-warning">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $pendingCount }}</h3>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="small text-muted">Pengajuan Akademik</span>
                        @if($pendingCount > 0)
                            <a href="{{ route('skp.index', ['sifat' => 'Akademik']) }}" class="small fw-bold text-warning text-decoration-none">
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
                            <i class="bi bi-patch-check-fill fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $approvedCount }}</h3>
                    <span class="small text-muted">Karya Akademik Valid</span>
                </div>
            </div>
        </div>

        <!-- 3. Total Poin SKP -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #2563eb !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">Total Poin SKP</span>
                        <div class="rounded-circle p-2 bg-primary-subtle text-primary">
                            <i class="bi bi-trophy-fill fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ number_format($totalPoin, 0, ',', '.') }}</h3>
                    <span class="small text-muted">Poin Terakumulasi Fakultas</span>
                </div>
            </div>
        </div>

        <!-- 4. Publikasi & HKI -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 mb-0" style="border-left: 4px solid #8b5cf6 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-bold text-uppercase">Publikasi / HKI</span>
                        <div class="rounded-circle p-2 bg-purple-subtle text-purple" style="background-color: #ede9fe; color: #7c3aed;">
                            <i class="bi bi-journal-bookmark-fill fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-1">{{ $publikasiCount }}</h3>
                    <span class="small text-muted">Jurnal, Buku &amp; Paten</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Antrean Cepat Verifikasi Karya Akademik Terbaru (Col 8) -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-warning fs-5"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Antrean Verifikasi Akademik Terbaru</h6>
                            <small class="text-muted">Pengajuan dari mahasiswa {{ $fakultas }} yang membutuhkan persetujuan WD 1.</small>
                        </div>
                    </div>
                    <a href="{{ route('skp.index', ['sifat' => 'Akademik']) }}" class="btn btn-sm btn-outline-primary px-3 rounded-2">
                        Lihat Semua ({{ $pendingCount }})
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($pendingList->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-check2-all text-success" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold text-dark mt-2">Tidak Ada Antrean Verifikasi</h6>
                            <p class="text-muted small mb-0">Semua pengajuan SKP Akademik mahasiswa {{ $fakultas }} telah diverifikasi.</p>
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
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 250px;" title="{{ $item->nama_kegiatan }}">
                                                {{ $item->nama_kegiatan }}
                                            </div>
                                            <div class="text-muted small">
                                                <span class="badge bg-light text-dark border">{{ $item->jenis_kegiatan ?: $item->kategori_id }}</span>
                                                @if($item->lingkup_tingkat)
                                                    <span class="badge bg-secondary-subtle text-secondary">{{ $item->lingkup_tingkat }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary px-2 py-1 fw-bold">+{{ $item->poin_skp }} Poin</span>
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
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#verifyModal{{ $item->id }}">
                                                    <i class="bi bi-check-lg me-1"></i> Proses
                                                </button>
                                                <a href="{{ route('skp.show', $item->id) }}" class="btn btn-outline-primary" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>

                                            <!-- Modal Verifikasi WD 1 -->
                                            <div class="modal fade text-start" id="verifyModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <form action="{{ route('skp.verify', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-primary text-white">
                                                                <h5 class="modal-title fs-6 fw-bold">
                                                                    <i class="bi bi-shield-check me-1"></i> Verifikasi SKP Akademik (WD 1)
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
                                                                    <div class="small text-primary fw-bold mt-1">Estimasi Poin SKP: {{ $item->poin_skp }} Poin</div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">Keputusan Verifikasi:</label>
                                                                    <div class="d-flex gap-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="status" id="statusSetuju{{ $item->id }}" value="Disetujui" checked>
                                                                            <label class="form-check-label text-success fw-bold" for="statusSetuju{{ $item->id }}">
                                                                                <i class="bi bi-check-circle me-1"></i> Setujui
                                                                            </label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="radio" name="status" id="statusTolak{{ $item->id }}" value="Ditolak">
                                                                            <label class="form-check-label text-danger fw-bold" for="statusTolak{{ $item->id }}">
                                                                                <i class="bi bi-x-circle me-1"></i> Tolak / Perlu Revisi
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-0">
                                                                    <label class="form-label small fw-semibold">Catatan dari Wakil Dekan 1 (Opsional):</label>
                                                                    <textarea name="catatan_verifikator" class="form-control" rows="3" placeholder="Contoh: Berkas jurnal SINTA 2 valid dan terkonfirmasi."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary btn-sm px-3">
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

        <!-- Top Mahasiswa Berprestasi Akademik (Col 4) -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-award-fill text-primary fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Top Prestasi Akademik</h6>
                    </div>
                    <a href="{{ route('skp.transkrip') }}" class="small text-primary text-decoration-none fw-semibold">
                        Semua &rarr;
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($topMahasiswa->isEmpty())
                        <div class="text-center py-5 text-muted small">
                            <i class="bi bi-mortarboard fs-2 d-block mb-1 text-secondary"></i>
                            Belum ada capaian akademik terverifikasi di fakultas ini.
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
                                            <a href="{{ route('skp.transkrip', $mhs->user_id) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="max-width: 180px;">
                                                {{ $mhs->nama_mahasiswa }}
                                            </a>
                                            <small class="text-muted d-block" style="font-size: 0.74rem;">{{ $mhs->program_studi }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-primary fw-bold" style="font-size: 0.8rem;">{{ $mhs->total_poin }} Poin</span>
                                        <div class="small fw-semibold text-success" style="font-size: 0.72rem;">Grade {{ $mhs->grade }}</div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Distribusi Capaian SKP Akademik per Program Studi -->
    @if(count($prodiChartLabels) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Distribusi Capaian Poin SKP per Program Studi di {{ $fakultas }}
                    </h6>
                    <small class="text-muted">Perbandingan akumulasi poin kegiatan akademik yang disetujui di setiap program studi.</small>
                </div>
                <div class="card-body p-4">
                    <div id="wadekProdiChart" style="min-height: 280px;"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
</section>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", () => {
    const prodiLabels = @json($prodiChartLabels);
    const prodiPoints = @json($prodiChartPoints);
    const prodiKegiatan = @json($prodiChartKegiatan);

    if (prodiLabels && prodiLabels.length > 0 && document.querySelector("#wadekProdiChart")) {
        new ApexCharts(document.querySelector("#wadekProdiChart"), {
            series: [{
                name: 'Total Poin SKP Disetujui',
                data: prodiPoints
            }, {
                name: 'Jumlah Kegiatan',
                data: prodiKegiatan
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded',
                    borderRadius: 4
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: prodiLabels,
                labels: {
                    rotate: -15,
                    style: { fontSize: '11px' }
                }
            },
            yaxis: {
                title: { text: 'Poin / Jumlah' }
            },
            fill: {
                opacity: 1
            },
            colors: ['#0284c7', '#10b981'],
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
