@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Transkrip Satuan Kredit Poin (SKP)</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('skp.index') }}">SKP Mahasiswa</a></li>
      <li class="breadcrumb-item active">Transkrip SKP</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <!-- Kartu Profil Mahasiswa & Rekap Poin -->
  <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
    <div class="card-body p-4 p-md-5">
      <div class="row align-items-center g-4">
        <div class="col-md-7">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img src="{{ $targetUser->avatar_url }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 75px; height: 75px; object-fit: cover;">
            <div>
              <span class="badge bg-primary px-3 py-1 mb-1">UNIVERSITAS IBNU SINA</span>
              <h3 class="fw-bold mb-0 text-white">{{ $targetUser->name }}</h3>
              <p class="mb-0 text-light opacity-75 small">NIM: <strong>{{ $targetUser->nim ?? '-' }}</strong> &bull; {{ $targetUser->program_studi ?? '-' }} ({{ $targetUser->fakultas ?? '-' }})</p>
            </div>
          </div>
          <p class="small text-light opacity-75 mb-0">
            Transkrip resmi penilaian kegiatan ekstrakurikuler mahasiswa berdasarkan pedoman Satuan Kredit Poin (SKP) UIS 2025 sebagai kelengkapan Surat Keterangan Pendamping Ijazah (SKPI).
          </p>
        </div>

        <div class="col-md-5">
          <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-25 text-center">
            <span class="text-uppercase small tracking-wider opacity-75 d-block">Akumulasi Kredit Poin</span>
            <div class="display-4 fw-bold text-warning my-1">{{ number_format($totalPoin) }} <span class="fs-6 fw-normal text-white">SKP</span></div>
            <div class="d-flex justify-content-center gap-2 mt-2">
              <span class="badge {{ $gradeInfo['badge'] }} px-3 py-1 fs-6">Nilai: {{ $gradeInfo['grade'] }} (Bobot: {{ number_format($gradeInfo['bobot'], 2) }})</span>
              <span class="badge bg-light text-dark px-3 py-1 fs-6">{{ $gradeInfo['predikat'] }}</span>
            </div>
            @if(!empty($badgeInfo))
              <div class="mt-3 pt-2 border-top border-white border-opacity-25 d-flex align-items-center justify-content-center gap-2">
                <i class="bi {{ $badgeInfo['icon'] }} fs-4 text-warning"></i>
                <span class="fw-bold text-warning">{{ $badgeInfo['nama'] }}</span>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Breakdown Poin & Aksi Cetak -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3">
        <span class="text-secondary small fw-medium">Poin Ranah Akademik</span>
        <h3 class="fw-bold text-primary mb-0">{{ number_format($akademikPoin) }} <span class="fs-6 fw-normal text-muted">SKP</span></h3>
        <small class="text-muted">Diverifikasi oleh Wakil Dekan 1</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3">
        <span class="text-secondary small fw-medium">Poin Ranah Non-Akademik</span>
        <h3 class="fw-bold text-success mb-0">{{ number_format($nonAkademikPoin) }} <span class="fs-6 fw-normal text-muted">SKP</span></h3>
        <small class="text-muted">Diverifikasi oleh Kabid Kemahasiswaan</small>
      </div>
    </div>
    <div class="col-md-4 d-flex align-items-center justify-content-md-end">
      <a href="{{ route('skp.cetak-pdf', $targetUser->id) }}" target="_blank" class="btn btn-dark px-4 py-3 rounded-pill shadow-sm w-100 d-inline-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-printer-fill fs-5"></i>
        <span class="fw-bold">Cetak Transkrip SKP (PDF)</span>
      </a>
    </div>
  </div>

  <!-- Tabel Rincian Kegiatan yang Disetujui -->
  <div class="card border-0 shadow-sm rounded-3 mb-5">
    <div class="card-body p-4">
      <h5 class="fw-bold text-dark mb-3"><i class="bi bi-journal-check text-primary me-2"></i>Daftar Kegiatan yang Diakui & Disetujui</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
          <thead class="table-light">
            <tr>
              <th style="width: 5%;">No</th>
              <th>Nama Kegiatan / Prestasi</th>
              <th>Kategori</th>
              <th>Tingkat / Capaian</th>
              <th>Ranah</th>
              <th class="text-center">Poin SKP</th>
              <th>Verifikator</th>
              <th class="text-end">Bukti</th>
            </tr>
          </thead>
          <tbody>
            @forelse($approvedKegiatans as $index => $row)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                  <span class="fw-semibold text-dark d-block">{{ $row->nama_kegiatan }}</span>
                  <small class="text-muted">{{ $row->tanggal_kegiatan ? $row->tanggal_kegiatan->translatedFormat('d M Y') : '-' }}</small>
                </td>
                <td>{{ $row->kategori_id }}</td>
                <td>
                  @if($row->lingkup_tingkat)<span class="badge bg-light text-dark border me-1">{{ $row->lingkup_tingkat }}</span>@endif
                  @if($row->peringkat_juara)<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">{{ $row->peringkat_juara }}</span>@endif
                  @if($row->jabatan)<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">{{ $row->jabatan }}</span>@endif
                </td>
                <td>
                  <span class="badge {{ $row->sifat_kegiatan === 'Akademik' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-success-subtle text-success border border-success-subtle' }}">
                    {{ $row->sifat_kegiatan }}
                  </span>
                </td>
                <td class="text-center">
                  <span class="fw-bold text-primary fs-6">+{{ $row->poin_skp }}</span>
                </td>
                <td>
                  <small class="text-muted d-block">{{ $row->verifikator?->name ?? 'Verifikator' }}</small>
                  <small class="text-secondary" style="font-size: 0.72rem;">{{ $row->verified_at ? $row->verified_at->translatedFormat('d/m/Y') : '-' }}</small>
                </td>
                <td class="text-end">
                  @if($row->bukti_url)
                    <a href="{{ $row->bukti_url }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                      <i class="bi bi-eye"></i> Buka
                    </a>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                  <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                  Belum ada kegiatan yang disetujui dalam transkrip SKP mahasiswa ini.
                </td>
              </tr>
            @endforelse
          </tbody>
          <tfoot class="table-light fw-bold">
            <tr>
              <td colspan="5" class="text-end">TOTAL SATUAN KREDIT POIN (SKP):</td>
              <td class="text-center fs-5 text-primary">{{ number_format($totalPoin) }}</td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection
