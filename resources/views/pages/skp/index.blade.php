@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Satuan Kredit Poin (SKP) Mahasiswa</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">SKP Mahasiswa</li>
    </ol>
  </nav>
  <p class="text-muted small">Penilaian & pelaporan kegiatan ekstrakurikuler mahasiswa UIS berdasarkan pedoman SKP 2025.</p>
</div><!-- End Page Title -->

<section class="section">
  <!-- Ringkasan Statistik -->
  <div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-secondary small fw-medium d-block">Total Poin Disetujui</span>
            <h2 class="fw-bold text-primary mb-0">{{ number_format($stats['total_skp']) }} <span class="fs-6 fw-normal text-muted">SKP</span></h2>
            <div class="mt-1">
              <span class="badge {{ $gradeInfo['badge'] }} px-2 py-1">Nilai: {{ $gradeInfo['grade'] }} ({{ $gradeInfo['predikat'] }})</span>
            </div>
          </div>
          <div class="p-3 bg-white rounded-circle shadow-sm text-primary">
            <i class="bi bi-trophy fs-3"></i>
          </div>
        </div>
        @if(!empty($badgeInfo))
          <div class="mt-2 pt-2 border-top border-primary-subtle d-flex align-items-center gap-1 small">
            <i class="bi {{ $badgeInfo['icon'] }} fs-5" style="color: {{ $badgeInfo['warna'] }}"></i>
            <span class="fw-bold" style="color: {{ $badgeInfo['warna'] }}">{{ $badgeInfo['nama'] }}</span>
          </div>
        @endif
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-secondary small fw-medium d-block">Menunggu Verifikasi</span>
            <h2 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h2>
            <small class="text-muted">Butuh verifikasi WD1 / Kabid</small>
          </div>
          <div class="p-3 bg-warning-subtle rounded-circle text-warning">
            <i class="bi bi-hourglass-split fs-3"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-secondary small fw-medium d-block">Telah Disetujui</span>
            <h2 class="fw-bold text-success mb-0">{{ $stats['approved'] }}</h2>
            <small class="text-muted">Poin resmi terakumulasi</small>
          </div>
          <div class="p-3 bg-success-subtle rounded-circle text-success">
            <i class="bi bi-check-circle fs-3"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm rounded-3 h-100 p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <span class="text-secondary small fw-medium d-block">Ditolak / Perlu Revisi</span>
            <h2 class="fw-bold text-danger mb-0">{{ $stats['rejected'] }}</h2>
            <small class="text-muted">Dapat diajukan ulang</small>
          </div>
          <div class="p-3 bg-danger-subtle rounded-circle text-danger">
            <i class="bi bi-x-circle fs-3"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filter & Kontrol Tabel -->
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
          <h5 class="fw-bold text-dark mb-1">
            @if(auth()->user()->isWadek1())
              <i class="bi bi-mortarboard text-primary me-2"></i>Verifikasi SKP Akademik (Wakil Dekan 1 - {{ auth()->user()->fakultas ?? 'Fakultas' }})
            @elseif(auth()->user()->isKabid())
              <i class="bi bi-award text-success me-2"></i>Verifikasi SKP Non-Akademik (Kabid Kemahasiswaan)
            @else
              Daftar Kegiatan SKP Mahasiswa
            @endif
          </h5>
          <small class="text-muted">Kelola pengajuan, verifikasi keabsahan dokumen, dan pemantauan perolehan poin SKP.</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          @if(auth()->user()->isMahasiswa() || auth()->user()->isAdmin())
            <a href="{{ route('skp.create') }}" class="btn btn-primary px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center">
              <i class="bi bi-plus-lg me-2"></i> Ajukan Kegiatan SKP
            </a>
          @endif
          <a href="{{ route('skp.transkrip') }}" class="btn btn-outline-primary px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center">
            <i class="bi bi-file-earmark-text me-2"></i> {{ auth()->user()->isMahasiswa() ? 'Lihat Transkrip SKP Saya' : 'Buku Transkrip SKP' }}
          </a>
        </div>
      </div>

      <!-- Filter Form -->
      <form action="{{ route('skp.index') }}" method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kegiatan, nama, NIM..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Semua Status --</option>
            <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
          </select>
        </div>
        <div class="col-md-2">
          <select name="sifat" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Sifat Kegiatan --</option>
            <option value="Akademik" {{ request('sifat') === 'Akademik' ? 'selected' : '' }}>Akademik (WD 1)</option>
            <option value="Non-Akademik" {{ request('sifat') === 'Non-Akademik' ? 'selected' : '' }}>Non-Akademik (Kabid)</option>
          </select>
        </div>
        <div class="col-md-3">
          <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="">-- Kategori Kegiatan --</option>
            @foreach($kategoris as $kKey => $kData)
              <option value="{{ $kKey }}" {{ request('kategori') === $kKey ? 'selected' : '' }}>{{ $kData['nama'] }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button type="submit" class="btn btn-sm btn-dark w-100"><i class="bi bi-search me-1"></i> Filter</button>
          @if(request()->anyFilled(['search', 'status', 'sifat', 'kategori']))
            <a href="{{ route('skp.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
          @endif
        </div>
      </form>

      <!-- Tabel Daftar Pengajuan SKP -->
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
          <thead class="table-light">
            <tr>
              <th scope="col" style="width: 5%;">No</th>
              <th scope="col">Mahasiswa</th>
              <th scope="col">Nama Kegiatan & Kategori</th>
              <th scope="col">Tingkat / Capaian</th>
              <th scope="col">Ranah Verifikasi</th>
              <th scope="col" class="text-center">Poin</th>
              <th scope="col" class="text-center">Status</th>
              <th scope="col" class="text-end" style="width: 15%;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($kegiatans as $index => $item)
              <tr>
                <td>{{ $kegiatans->firstItem() + $index }}</td>
                <td>
                  <span class="fw-bold text-dark d-block">{{ $item->nama_mahasiswa }}</span>
                  <small class="text-muted">{{ $item->nim }} &bull; {{ $item->program_studi }}</small>
                </td>
                <td>
                  <span class="fw-semibold text-dark d-block">{{ $item->nama_kegiatan }}</span>
                  <small class="text-secondary">
                    {{ $kategoris[$item->kategori_id]['nama'] ?? $item->kategori_id }}
                    @if($item->jenis_kegiatan) &bull; <span class="text-primary">{{ $item->jenis_kegiatan }}</span> @endif
                  </small>
                </td>
                <td>
                  @if($item->lingkup_tingkat)
                    <span class="badge bg-light text-dark border me-1">{{ $item->lingkup_tingkat }}</span>
                  @endif
                  @if($item->peringkat_juara)
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">{{ $item->peringkat_juara }}</span>
                  @endif
                  @if($item->jabatan)
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">{{ $item->jabatan }}</span>
                  @endif
                  @if(!$item->lingkup_tingkat && !$item->peringkat_juara && !$item->jabatan)
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @if($item->sifat_kegiatan === 'Akademik')
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1">
                      <i class="bi bi-mortarboard"></i> Akademik (WD 1)
                    </span>
                  @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                      <i class="bi bi-people"></i> Non-Akademik (Kabid)
                    </span>
                  @endif
                  @if($item->verifikator)
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                      Oleh: {{ $item->verifikator->name }}
                    </small>
                  @endif
                </td>
                <td class="text-center">
                  <span class="fw-bold fs-6 text-primary">+{{ $item->poin_skp }}</span>
                  <small class="text-muted d-block" style="font-size: 0.7rem;">SKP</small>
                </td>
                <td class="text-center">
                  @if($item->status === 'Disetujui')
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
                  @elseif($item->status === 'Ditolak')
                    <span class="badge bg-danger" title="{{ $item->catatan_verifikator }}"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                  @else
                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Menunggu</span>
                  @endif
                </td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="{{ route('skp.show', $item->id) }}" class="btn btn-outline-info" title="Lihat Detail">
                      <i class="bi bi-eye"></i>
                    </a>

                    <!-- Tombol Verifikasi Cepat (WD1 / Kabid / Admin) -->
                    @php
                      $canVerify = false;
                      if ($item->status === 'Pending') {
                        if (auth()->user()->isAdmin()) $canVerify = true;
                        elseif (auth()->user()->isWadek1() && $item->sifat_kegiatan === 'Akademik') $canVerify = true;
                        elseif (auth()->user()->isKabid() && $item->sifat_kegiatan === 'Non-Akademik') $canVerify = true;
                      }
                    @endphp

                    @if($canVerify)
                      <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalVerify{{ $item->id }}" title="Verifikasi Pengajuan">
                        <i class="bi bi-check-lg"></i>
                      </button>
                    @endif

                    @if($item->status !== 'Disetujui' || auth()->user()->isAdmin())
                      @if(auth()->user()->id === $item->user_id || auth()->user()->isAdmin())
                        <a href="{{ route('skp.edit', $item->id) }}" class="btn btn-outline-warning" title="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('skp.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-outline-danger" title="Hapus">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      @endif
                    @endif
                  </div>

                  <!-- Modal Verifikasi Cepat -->
                  @if($canVerify)
                    <div class="modal fade" id="modalVerify{{ $item->id }}" tabindex="-1" aria-labelledby="modalVerifyLabel{{ $item->id }}" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered text-start">
                        <div class="modal-content border-0 shadow">
                          <form action="{{ route('skp.verify', $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-light">
                              <h6 class="modal-title fw-bold" id="modalVerifyLabel{{ $item->id }}">
                                <i class="bi bi-shield-check text-primary me-2"></i>Verifikasi Kegiatan SKP
                              </h6>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                              <p class="small text-secondary mb-2">Periksa kegiatan berikut sebelum memberikan persetujuan poin SKP:</p>
                              <div class="p-3 bg-light rounded-3 mb-3 border">
                                <span class="fw-bold text-dark d-block">{{ $item->nama_kegiatan }}</span>
                                <small class="text-muted d-block">Mahasiswa: <b>{{ $item->nama_mahasiswa }}</b> ({{ $item->nim }})</small>
                                <small class="text-muted d-block">Kategori: {{ $kategoris[$item->kategori_id]['nama'] ?? $item->kategori_id }} &bull; Sifat: <b>{{ $item->sifat_kegiatan }}</b></small>
                                @if($item->bukti_url)
                                  <a href="{{ $item->bukti_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2 py-0 px-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-file-earmark-arrow-down me-1"></i> Buka Berkas Bukti
                                  </a>
                                @endif
                              </div>

                              <div class="mb-3">
                                <label class="form-label fw-semibold small">Poin SKP yang Diberikan</label>
                                <div class="input-group input-group-sm">
                                  <input type="number" name="poin_skp" class="form-control" value="{{ $item->poin_skp }}" min="0" required>
                                  <span class="input-group-text">Poin</span>
                                </div>
                                <small class="text-muted" style="font-size: 0.72rem;">Standar pedoman UIS 2025: {{ $item->poin_skp }} Poin.</small>
                              </div>

                              <div class="mb-3">
                                <label class="form-label fw-semibold small">Keputusan Verifikasi <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusApprove{{ $item->id }}" value="Disetujui" checked>
                                    <label class="form-check-label text-success fw-medium" for="statusApprove{{ $item->id }}">
                                      <i class="bi bi-check-circle me-1"></i> Setujui Pengajuan
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusReject{{ $item->id }}" value="Ditolak">
                                    <label class="form-check-label text-danger fw-medium" for="statusReject{{ $item->id }}">
                                      <i class="bi bi-x-circle me-1"></i> Tolak Pengajuan
                                    </label>
                                  </div>
                                </div>
                              </div>

                              <div class="mb-0">
                                <label class="form-label fw-semibold small">Catatan / Alasan Penolakan (Opsional)</label>
                                <textarea name="catatan_verifikator" class="form-control form-control-sm" rows="2" placeholder="Tuliskan catatan perbaikan atau alasan jika ditolak..."></textarea>
                              </div>
                            </div>
                            <div class="modal-footer bg-light py-2">
                              <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                              <button type="submit" class="btn btn-sm btn-primary px-3">Simpan Keputusan</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endif

                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                  <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                  Belum ada pengajuan kegiatan SKP yang sesuai dengan kriteria filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Menampilkan {{ $kegiatans->firstItem() ?? 0 }} - {{ $kegiatans->lastItem() ?? 0 }} dari total {{ $kegiatans->total() }} data</small>
        {{ $kegiatans->links() }}
      </div>
    </div>
  </div>
</section>
@endsection
