@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
    <h1>Prestasi Mandiri</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('prestasi-mandiri.index') }}">Prestasi Mandiri</a></li>
            <li class="breadcrumb-item active">Form</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="card-title fw-bold text-dark mb-4 p-0">View Data</h5>

            <div class="row g-3">
                <!-- Level & Kategori -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Level</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->level ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Kategori</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->kategori ?? '-' }}
                    </div>
                </div>

                <!-- Nama Kompetisi / Lomba -->
                <div class="col-12">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Kompetisi / Lomba</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->nama_kompetisi ?? '-' }}
                    </div>
                </div>

                <!-- Nama Cabang & Peringkat -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Cabang</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->nama_cabang ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Peringkat</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->peringkat ?? '-' }}
                    </div>
                </div>

                <!-- Nama Penyelenggara -->
                <div class="col-12">
                    <label class="form-label small fw-bold text-dark mb-1">Nama Penyelenggara</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->nama_penyelenggara ?? '-' }}
                    </div>
                </div>

                <!-- Jumlah PT / Negara Peserta, Kepesertaan, Bentuk -->
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Jumlah PT / Negara Peserta</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->jumlah_pt_peserta ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Kepesertaan</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->kepesertaan ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-dark mb-1">Bentuk</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->bentuk ?? '-' }}
                    </div>
                </div>

                <!-- URL Kompetisi / Lomba -->
                <div class="col-12">
                    <label class="form-label small fw-bold text-dark mb-1">URL Kompetisi / Lomba</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small text-break">
                        @if($prestasiMandiri->url_kompetisi)
                            <a href="{{ $prestasiMandiri->url_kompetisi }}" target="_blank" class="text-primary text-decoration-none">{{ $prestasiMandiri->url_kompetisi }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>

                <!-- Dokumen Sertifikat & Tanggal Sertifikat -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Dokumen Sertifikat</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @php
                            $certFile = $prestasiMandiri->file_dokumen_sertifikat;
                            $certLink = $prestasiMandiri->link_dokumen_sertifikat;
                            $certUrl = $certFile ? (str_starts_with($certFile, 'http') ? $certFile : asset('storage/' . $certFile)) : $certLink;
                            $isDrive = $certUrl && str_contains($certUrl, 'drive.google.com');
                        @endphp
                        @if($certUrl)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'sertifikat']) }}" class="btn btn-sm btn-outline-primary py-1 px-3 d-inline-flex align-items-center" target="_blank">
                                    <i class="bi {{ $isDrive ? 'bi-google' : 'bi-file-earmark-check' }} me-1"></i> {{ $isDrive ? 'Buka di Google Drive' : 'Unduh Berkas Sertifikat' }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $certUrl }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL Sertifikat (SIMKATMAWA):</span>
                                <a href="{{ $certUrl }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $certUrl }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Tanggal Sertifikat</label>
                    <div class="p-2 rounded bg-light border text-dark small">
                        {{ $prestasiMandiri->tanggal_sertifikat ? $prestasiMandiri->tanggal_sertifikat->format('Y-m-d') : '-' }}
                    </div>
                </div>

                <!-- Foto Upacara Penyerahan Penghargaan & Link Dokumen Undangan -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Foto Upacara Penyerahan Penghargaan</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @php 
                            $uppFile = $prestasiMandiri->file_foto_upp ?: $prestasiMandiri->link_foto_upp;
                            $uppUrl = $uppFile ? (str_starts_with($uppFile, 'http') ? $uppFile : asset('storage/' . $uppFile)) : null;
                            $isDrive = $uppUrl && str_contains($uppUrl, 'drive.google.com');
                        @endphp
                        @if($uppUrl)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'foto-upp']) }}" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center" target="_blank">
                                    <i class="bi {{ $isDrive ? 'bi-google' : 'bi-image' }} me-1"></i> {{ $isDrive ? 'Buka di Google Drive' : 'Lihat Foto Upacara Penyerahan Penghargaan' }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $uppUrl }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL Foto Upacara Penyerahan Penghargaan (SIMKATMAWA):</span>
                                <a href="{{ $uppUrl }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $uppUrl }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Link Dokumen Undangan</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @if($prestasiMandiri->link_dokumen_undangan)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ $prestasiMandiri->link_dokumen_undangan }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3 d-inline-flex align-items-center">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Undangan
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $prestasiMandiri->link_dokumen_undangan }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL Undangan (SIMKATMAWA):</span>
                                <a href="{{ $prestasiMandiri->link_dokumen_undangan }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $prestasiMandiri->link_dokumen_undangan }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <!-- File Dokumen LPJ -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Berkas File LPJ</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @php
                            $lpjFile = $prestasiMandiri->file_dokumen_lpj;
                            $lpjLink = $prestasiMandiri->link_dokumen_lpj;
                            $lpjUrl = $lpjFile ? (str_starts_with($lpjFile, 'http') ? $lpjFile : asset('storage/' . $lpjFile)) : $lpjLink;
                            $isDrive = $lpjUrl && str_contains($lpjUrl, 'drive.google.com');
                        @endphp
                        @if($lpjUrl)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ route('prestasi-mandiri.download-lpj', $prestasiMandiri->id) }}" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center" target="_blank">
                                    <i class="bi {{ $isDrive ? 'bi-google' : 'bi-download' }} me-1"></i> {{ $isDrive ? 'Buka di Google Drive' : 'Unduh Berkas LPJ' }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $lpjUrl }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL LPJ:</span>
                                <a href="{{ $lpjUrl }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $lpjUrl }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <!-- Dokumen Tambahan & Kontak -->
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">No. HP / WhatsApp</label>
                    <div class="p-2 rounded bg-light border text-dark small">
                        {{ $prestasiMandiri->no_hp ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">KTM (Kartu Tanda Mahasiswa)</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @php
                            $ktmFile = $prestasiMandiri->file_ktm;
                            $ktmLink = $prestasiMandiri->link_ktm;
                            $ktmUrl = $ktmFile ? (str_starts_with($ktmFile, 'http') ? $ktmFile : asset('storage/' . $ktmFile)) : $ktmLink;
                            $isDrive = $ktmUrl && str_contains($ktmUrl, 'drive.google.com');
                        @endphp
                        @if($ktmUrl)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'ktm']) }}" class="btn btn-sm btn-outline-primary py-1 px-3 d-inline-flex align-items-center" target="_blank">
                                    <i class="bi {{ $isDrive ? 'bi-google' : 'bi-person-badge' }} me-1"></i> {{ $isDrive ? 'Buka di Google Drive' : 'Unduh KTM' }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $ktmUrl }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL KTM:</span>
                                <a href="{{ $ktmUrl }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $ktmUrl }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Surat Pengajuan Reward Prestasi</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @php
                            $rewFile = $prestasiMandiri->file_pengajuan_reward;
                            $rewLink = $prestasiMandiri->link_pengajuan_reward;
                            $rewUrl = $rewFile ? (str_starts_with($rewFile, 'http') ? $rewFile : asset('storage/' . $rewFile)) : $rewLink;
                            $isDrive = $rewUrl && str_contains($rewUrl, 'drive.google.com');
                        @endphp
                        @if($rewUrl)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'reward']) }}" class="btn btn-sm btn-outline-success py-1 px-3 d-inline-flex align-items-center" target="_blank">
                                    <i class="bi {{ $isDrive ? 'bi-google' : 'bi-file-earmark-arrow-up' }} me-1"></i> {{ $isDrive ? 'Buka di Google Drive' : 'Unduh Surat Reward' }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $rewUrl }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL Surat Reward:</span>
                                <a href="{{ $rewUrl }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $rewUrl }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark mb-1">Surat Tugas WR III / WD I</label>
                    <div class="p-2 rounded bg-light border text-dark small text-break">
                        @php
                            $tgsFile = $prestasiMandiri->file_surat_tugas;
                            $tgsLink = $prestasiMandiri->link_surat_tugas;
                            $tgsUrl = $tgsFile ? (str_starts_with($tgsFile, 'http') ? $tgsFile : asset('storage/' . $tgsFile)) : $tgsLink;
                            $isDrive = $tgsUrl && str_contains($tgsUrl, 'drive.google.com');
                        @endphp
                        @if($tgsUrl)
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'surat-tugas']) }}" class="btn btn-sm btn-outline-info py-1 px-3 d-inline-flex align-items-center" target="_blank">
                                    <i class="bi {{ $isDrive ? 'bi-google' : 'bi-file-earmark-check' }} me-1"></i> {{ $isDrive ? 'Buka di Google Drive' : 'Unduh Surat Tugas' }}
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center copy-url-btn" data-url="{{ $tgsUrl }}" title="Salin Link URL">
                                    <i class="bi bi-clipboard me-1"></i> <span>Salin URL</span>
                                </button>
                            </div>
                            <div class="mt-1 pt-1 border-top">
                                <span class="text-muted d-block small" style="font-size: 0.72rem;"><i class="bi bi-link-45deg"></i> Link URL Surat Tugas:</span>
                                <a href="{{ $tgsUrl }}" target="_blank" class="text-primary text-break fw-medium" style="font-size: 0.8rem;">{{ $tgsUrl }}</a>
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="col-12">
                    <label class="form-label small fw-bold text-dark mb-1">Keterangan</label>
                    <div class="p-2 rounded bg-light border-0 text-dark small">
                        {{ $prestasiMandiri->keterangan ?? '-' }}
                    </div>
                </div>

                <!-- Data Mahasiswa Table -->
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-dark mb-2">Data Mahasiswa</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th style="width: 250px;">NIM</th>
                                    <th>Nama Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($prestasiMandiri->data_mahasiswa) && count($prestasiMandiri->data_mahasiswa) > 0)
                                    @foreach($prestasiMandiri->data_mahasiswa as $idx => $mhs)
                                        <tr>
                                            <td class="fw-bold">{{ $idx + 1 }}</td>
                                            <td>{{ $mhs['nim'] ?? '-' }}</td>
                                            <td>{{ $mhs['nama'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center text-muted small py-3">Belum ada data mahasiswa</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Data Dosen Table -->
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-dark mb-2">Data Dosen</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th style="width: 250px;">NUPTK/NIDN</th>
                                    <th>Nama Dosen</th>
                                    <th>Surat Tugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($prestasiMandiri->data_dosen) && count($prestasiMandiri->data_dosen) > 0)
                                    @foreach($prestasiMandiri->data_dosen as $idx => $dsn)
                                        <tr>
                                            <td class="fw-bold">{{ $idx + 1 }}</td>
                                            <td>{{ $dsn['nidn'] ?? '-' }}</td>
                                            <td>{{ $dsn['nama'] ?? '-' }}</td>
                                            <td>
                                                @if(!empty($dsn['url_surat']))
                                                    <a href="{{ $dsn['url_surat'] }}" target="_blank" class="text-primary text-decoration-none">{{ $dsn['url_surat'] }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted small py-3">Belum ada data dosen</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bottom Button -->
                <div class="col-12 mt-4">
                    <a href="{{ route('prestasi-mandiri.index') }}" class="btn text-white fw-medium px-4 shadow-sm" style="background-color: #f97316; border-color: #f97316;">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.copy-url-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            if (url) {
                navigator.clipboard.writeText(url).then(() => {
                    const icon = this.querySelector('i');
                    const span = this.querySelector('span');
                    const originalText = span ? span.innerText : 'Salin URL';

                    this.classList.remove('btn-outline-secondary');
                    this.classList.add('btn-success');
                    if (icon) icon.className = 'bi bi-check2 me-1';
                    if (span) span.innerText = 'Tersalin!';

                    setTimeout(() => {
                        this.classList.remove('btn-success');
                        this.classList.add('btn-outline-secondary');
                        if (icon) icon.className = 'bi bi-clipboard me-1';
                        if (span) span.innerText = originalText;
                    }, 2000);
                }).catch(() => {
                    // Fallback for older browsers
                    const tempInput = document.createElement('input');
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    alert('URL berhasil disalin: ' + url);
                });
            }
        });
    });
});
</script>
@endpush
@endsection
