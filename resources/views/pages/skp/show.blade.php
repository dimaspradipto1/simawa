@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Detail Kegiatan SKP</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('skp.index') }}">SKP Mahasiswa</a></li>
      <li class="breadcrumb-item active">Detail Kegiatan</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <div class="row g-4">
    <!-- Kolom Kiri: Rincian Kegiatan -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <span class="badge {{ $skp->sifat_kegiatan === 'Akademik' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} px-3 py-1 mb-2">
                <i class="bi {{ $skp->sifat_kegiatan === 'Akademik' ? 'bi-mortarboard' : 'bi-people' }} me-1"></i> {{ $skp->sifat_kegiatan }}
              </span>
              <h4 class="fw-bold text-dark mb-1">{{ $skp->nama_kegiatan }}</h4>
              <p class="text-secondary small mb-0">{{ $skp->jenis_kegiatan ?? 'Kegiatan Ekstrakurikuler' }}</p>
            </div>
            <div>
              @if($skp->status === 'Disetujui')
                <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle me-1"></i> Disetujui</span>
              @elseif($skp->status === 'Ditolak')
                <span class="badge bg-danger fs-6 px-3 py-2"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
              @else
                <span class="badge bg-warning text-dark fs-6 px-3 py-2"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
              @endif
            </div>
          </div>

          <div class="row g-3 py-3 border-top border-bottom my-3">
            <div class="col-sm-6">
              <span class="text-secondary small d-block">Tingkat / Lingkup Kegiatan</span>
              <strong class="text-dark">{{ $skp->lingkup_tingkat ?? '-' }}</strong>
            </div>
            <div class="col-sm-6">
              <span class="text-secondary small d-block">Capaian Juara / Jabatan</span>
              <strong class="text-dark">{{ $skp->peringkat_juara ?: ($skp->jabatan ?: '-') }}</strong>
            </div>
            <div class="col-sm-6">
              <span class="text-secondary small d-block">Tanggal Pelaksanaan</span>
              <strong class="text-dark">{{ $skp->tanggal_kegiatan ? $skp->tanggal_kegiatan->translatedFormat('d F Y') : '-' }}</strong>
            </div>
            <div class="col-sm-6">
              <span class="text-secondary small d-block">Poin SKP yang Diperoleh</span>
              <span class="badge bg-primary fs-6">+{{ $skp->poin_skp }} Poin SKP</span>
            </div>
          </div>

          @if($skp->keterangan)
            <div class="mb-4">
              <span class="text-secondary small fw-medium d-block mb-1">Keterangan Tambahan:</span>
              <p class="p-3 bg-light rounded-3 text-dark small mb-0">{{ $skp->keterangan }}</p>
            </div>
          @endif

          <!-- Dokumen Bukti -->
          <div class="mb-3">
            <span class="text-secondary small fw-medium d-block mb-2">Berkas Bukti / Dokumen:</span>
            @if($skp->bukti_url)
              <div class="d-flex align-items-center gap-2 p-3 bg-light rounded-3 border">
                <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                <div class="flex-grow-1">
                  <span class="fw-semibold text-dark d-block">Sertifikat / Bukti Kegiatan</span>
                  <small class="text-muted text-break">{{ $skp->bukti_url }}</small>
                </div>
                <a href="{{ $skp->bukti_url }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                  <i class="bi bi-box-arrow-up-right me-1"></i> Buka Bukti
                </a>
              </div>
            @else
              <span class="text-muted small">Tidak ada dokumen yang dilampirkan.</span>
            @endif
          </div>

          <!-- Riwayat Verifikasi -->
          @if($skp->verified_by)
            <div class="alert alert-light border rounded-3 mt-4 mb-0">
              <h6 class="fw-bold mb-1 small text-dark"><i class="bi bi-shield-check text-success me-1"></i> Informasi Verifikasi</h6>
              <p class="small text-secondary mb-1">
                Diverifikasi oleh: <strong>{{ $skp->verifikator?->name ?? 'Pejabat Verifikator' }}</strong> pada {{ $skp->verified_at ? $skp->verified_at->translatedFormat('d F Y H:i') : '-' }} WIB.
              </p>
              @if($skp->catatan_verifikator)
                <div class="p-2 bg-white rounded border small mt-2">
                  <strong>Catatan Verifikator:</strong> {{ $skp->catatan_verifikator }}
                </div>
              @endif
            </div>
          @endif
        </div>
      </div>

      <!-- Tombol Aksi Verifikasi jika berhak -->
      @if($canVerify)
        <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-4 border-primary">
          <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-2">
              <i class="bi bi-pencil-square text-primary me-2"></i>Tindakan Verifikasi
            </h5>
            <p class="small text-secondary mb-3">
              Sebagai <strong>{{ auth()->user()->isWadek1() ? 'Wakil Dekan 1' : (auth()->user()->isKabid() ? 'Kabid Kemahasiswaan' : 'Administrator') }}</strong>, Anda berwenang memvalidasi pengajuan ini.
            </p>

            <form action="{{ route('skp.verify', $skp->id) }}" method="POST">
              @csrf
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Poin SKP yang Disetujui</label>
                  <div class="input-group input-group-sm">
                    <input type="number" name="poin_skp" class="form-control" value="{{ $skp->poin_skp }}" min="0" required>
                    <span class="input-group-text">Poin</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Keputusan</label>
                  <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="status" id="app1" value="Disetujui" checked>
                      <label class="form-check-label text-success fw-medium" for="app1">Setujui</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="status" id="rej1" value="Ditolak">
                      <label class="form-check-label text-danger fw-medium" for="rej1">Tolak</label>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold small">Catatan Verifikator (Wajib jika ditolak)</label>
                  <textarea name="catatan_verifikator" class="form-control form-control-sm" rows="2" placeholder="Tuliskan catatan persetujuan atau alasan jika pengajuan ditolak..."></textarea>
                </div>
              </div>
              <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Simpan Keputusan Verifikasi
              </button>
            </form>
          </div>
        </div>
      @endif

      <a href="{{ route('skp.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill mb-5">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
      </a>
    </div>

    <!-- Kolom Kanan: Kartu Mahasiswa Pemohon -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-3 p-4 sticky-top" style="top: 20px;">
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-circle text-primary me-2"></i>Data Mahasiswa</h6>
        <div class="text-center py-3 border-bottom mb-3">
          <img src="{{ $skp->mahasiswa?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($skp->nama_mahasiswa) }}" class="rounded-circle shadow-sm mb-2" style="width: 70px; height: 70px; object-fit: cover;">
          <h6 class="fw-bold text-dark mb-0">{{ $skp->nama_mahasiswa }}</h6>
          <small class="text-muted">{{ $skp->nim }}</small>
        </div>
        <div class="small text-secondary">
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span>Program Studi:</span><strong class="text-dark">{{ $skp->program_studi }}</strong>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom">
            <span>Fakultas:</span><strong class="text-dark">{{ $skp->fakultas }}</strong>
          </div>
          <div class="d-flex justify-content-between py-1">
            <span>Tahun Akademik:</span><strong class="text-dark">{{ $skp->tahun_akademik }}</strong>
          </div>
        </div>

        @if($skp->user_id)
          <div class="mt-4 pt-3 border-top">
            <a href="{{ route('skp.transkrip', $skp->user_id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
              <i class="bi bi-file-earmark-person me-1"></i> Lihat Transkrip Lengkap
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection
