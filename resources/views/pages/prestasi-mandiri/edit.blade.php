@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Prestasi Mandiri</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('prestasi-mandiri.index') }}">Prestasi Mandiri</a></li>
      <li class="breadcrumb-item active">Edit Prestasi</li>
    </ol>
  </nav>
  <p class="text-muted small">Perbarui data prestasi, mahasiswa, dan dosen pendamping.</p>
</div><!-- End Page Title -->

<section class="section">
  <!-- SIMKATMAWA Soft Blue Header Banner -->
  <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
    <div class="card-body p-4">
      <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
          Edit Prestasi
        </span>
        @if(!empty($isDriveActive) && $isDriveActive)
          <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
            <i class="bi bi-google me-1"></i> Penyimpanan Google Drive Aktif
          </span>
          @if(auth()->check() && in_array(auth()->user()->role, ['superadmin', 'adminbkak']))
            <a href="{{ route('google-drive.auth') }}" class="btn btn-sm btn-outline-primary py-0 px-2 fw-medium" style="font-size: 0.75rem;">
              <i class="bi bi-arrow-repeat me-1"></i> Otorisasi Ulang
            </a>
            <form action="{{ route('google-drive.sync-local') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-sm btn-success py-0 px-2 fw-medium" style="font-size: 0.75rem;" title="Unggah berkas lokal yang belum ada di Google Drive">
                <i class="bi bi-cloud-arrow-up me-1"></i> Sinkronkan Berkas Lokal ke Drive
              </button>
            </form>
          @endif
        @else
          <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
            <i class="bi bi-exclamation-triangle me-1"></i> Google Drive Belum Terhubung / Token Kedaluwarsa (Penyimpanan Lokal Aktif)
          </span>
          @if(auth()->check() && in_array(auth()->user()->role, ['superadmin', 'adminbkak']))
            <a href="{{ route('google-drive.auth') }}" class="btn btn-sm btn-danger py-0 px-2 fw-medium" style="font-size: 0.75rem;">
              <i class="bi bi-google me-1"></i> Hubungkan Ulang Google Drive
            </a>
          @endif
        @endif
      </div>
      <h5 class="fw-bold text-dark mb-1">Form prestasi mandiri terpadu</h5>
      <p class="text-secondary small mb-3">Data mahasiswa dan dosen kini diisi langsung di form ini, sehingga tidak perlu berpindah halaman setelah data prestasi tersimpan.</p>

      @php
        $templatesLpj = ($activeTemplatesLpj ?? collect())->isNotEmpty()
          ? $activeTemplatesLpj
          : \App\Models\TemplateLpj::getActiveTemplates('Prestasi Mandiri');

        if ($templatesLpj->isEmpty()) {
          $fallback = \App\Models\TemplateLpj::where('kategori', 'Prestasi Mandiri')->get();
          if ($fallback->isNotEmpty()) {
            $templatesLpj = $fallback;
          }
        }
      @endphp

      <div class="card border border-primary-subtle bg-white rounded-3 shadow-sm p-3 mb-0">
        <div class="d-flex align-items-center mb-2">
          <i class="bi bi-file-earmark-arrow-down text-primary fs-5 me-2"></i>
          <div>
            <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">Daftar Template Dokumen (Prestasi Mandiri)</span>
            <small class="text-muted" style="font-size: 0.75rem;">Unduh dokumen template atau buka tautan di bawah ini sebagai acuan pelaporan prestasi Anda:</small>
          </div>
        </div>
        <div class="list-group list-group-flush rounded border mt-1">
          @forelse($templatesLpj as $tpl)
            <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-2 px-3 gap-2">
              <div class="d-flex align-items-center">
                <i class="bi {{ $tpl->tipe === 'link' ? 'bi-link-45deg text-info' : 'bi-file-earmark-word text-primary' }} fs-5 me-2 flex-shrink-0"></i>
                <div>
                  <span class="fw-semibold text-dark d-block" style="font-size: 0.85rem;">{{ $tpl->nama }}</span>
                  @if($tpl->keterangan)
                    <span class="text-muted d-block small" style="font-size: 0.75rem;">{{ $tpl->keterangan }}</span>
                  @endif
                </div>
              </div>
              <div class="flex-shrink-0">
                <a href="{{ $tpl->download_url }}" target="_blank" rel="noopener noreferrer" 
                  class="btn btn-sm {{ $tpl->tipe === 'link' ? 'btn-outline-info' : 'btn-outline-primary' }} rounded-pill px-3 py-1 fw-medium"
                  style="font-size: 0.78rem;">
                  <i class="bi {{ $tpl->tipe === 'link' ? 'bi-box-arrow-up-right' : 'bi-download' }} me-1"></i>
                  {{ $tpl->tipe === 'link' ? 'Buka Tautan' : 'Unduh Template' }}
                </a>
              </div>
            </div>
          @empty
            <div class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center py-2 px-3 gap-2">
              <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark-word text-primary fs-5 me-2 flex-shrink-0"></i>
                <div>
                  <span class="fw-semibold text-dark d-block" style="font-size: 0.85rem;">Template Dokumen LPJ Prestasi Mandiri UIS</span>
                  <span class="text-muted d-block small" style="font-size: 0.75rem;">Format resmi dokumen LPJ (.docx)</span>
                </div>
              </div>
              <div class="flex-shrink-0">
                <a href="{{ route('prestasi-mandiri.template-lpj') }}" target="_blank" rel="noopener noreferrer" 
                  class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-medium" style="font-size: 0.78rem;">
                  <i class="bi bi-download me-1"></i> Unduh Template
                </a>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>



  <form action="{{ route('prestasi-mandiri.update', $prestasiMandiri->id) }}" method="POST" enctype="multipart/form-data" id="form-prestasi-mandiri" novalidate>
    @csrf
    @method('PUT')

    <!-- Card 1: Data Prestasi -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
          <div class="p-2 rounded-circle bg-primary-subtle text-primary me-3">
            <i class="bi bi-trophy fs-4"></i>
          </div>
          <div>
            <h5 class="fw-bold text-dark mb-0">Data Prestasi</h5>
            <small class="text-muted">Informasi kompetisi, prestasi, dan dokumen bukti utama.</small>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="level" class="form-label fw-medium">Level<span class="text-danger">*</span></label>
            <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
              <option value="" disabled {{ old('level', $prestasiMandiri->level) ? '' : 'selected' }}>Pilih Level</option>
              @foreach($options['levels'] as $key => $label)
                <option value="{{ $key }}" {{ old('level', $prestasiMandiri->level) == $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('level')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="kategori" class="form-label fw-medium">Kategori<span class="text-danger">*</span></label>
            <select class="form-select @error('kategori') is-invalid @enderror" id="kategori" name="kategori" required>
              <option value="" disabled {{ old('kategori', $prestasiMandiri->kategori) ? '' : 'selected' }}>Pilih Kategori</option>
              @foreach($options['kategoris'] as $key => $label)
                <option value="{{ $key }}" {{ old('kategori', $prestasiMandiri->kategori) == $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('kategori')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="nama_kompetisi" class="form-label fw-medium">Nama Kompetisi / Lomba<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-award"></i></span>
              <input type="text" class="form-control @error('nama_kompetisi') is-invalid @enderror" id="nama_kompetisi" name="nama_kompetisi" value="{{ old('nama_kompetisi', $prestasiMandiri->nama_kompetisi) }}" placeholder="Nama Kompetisi / Lomba" required>
            </div>
            @error('nama_kompetisi')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="nama_cabang" class="form-label fw-medium">Nama Cabang<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-diagram-3"></i></span>
              <input type="text" class="form-control @error('nama_cabang') is-invalid @enderror" id="nama_cabang" name="nama_cabang" value="{{ old('nama_cabang', $prestasiMandiri->nama_cabang) }}" placeholder="Nama Cabang" required>
            </div>
            @error('nama_cabang')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="peringkat" class="form-label fw-medium">Peringkat<span class="text-danger">*</span></label>
            <select class="form-select @error('peringkat') is-invalid @enderror" id="peringkat" name="peringkat" required>
              <option value="" disabled {{ old('peringkat', $prestasiMandiri->peringkat) ? '' : 'selected' }}>Pilih Peringkat</option>
              @foreach($options['peringkats'] as $key => $label)
                <option value="{{ $key }}" {{ old('peringkat', $prestasiMandiri->peringkat) == $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('peringkat')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="nama_penyelenggara" class="form-label fw-medium">Nama Penyelenggara<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-building"></i></span>
              <input type="text" class="form-control @error('nama_penyelenggara') is-invalid @enderror" id="nama_penyelenggara" name="nama_penyelenggara" value="{{ old('nama_penyelenggara', $prestasiMandiri->nama_penyelenggara) }}" placeholder="Penyelenggara" required>
            </div>
            @error('nama_penyelenggara')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4">
            <label for="jumlah_pt_peserta" class="form-label fw-medium">Jumlah PT Peserta<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-people"></i></span>
              <input type="number" class="form-control @error('jumlah_pt_peserta') is-invalid @enderror" id="jumlah_pt_peserta" name="jumlah_pt_peserta" value="{{ old('jumlah_pt_peserta', $prestasiMandiri->jumlah_pt_peserta) }}" placeholder="Jumlah" required min="1">
            </div>
            @error('jumlah_pt_peserta')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4">
            <label for="kepesertaan" class="form-label fw-medium">Kepesertaan<span class="text-danger">*</span></label>
            <select class="form-select @error('kepesertaan') is-invalid @enderror" id="kepesertaan" name="kepesertaan" required>
              <option value="" disabled {{ old('kepesertaan', $prestasiMandiri->kepesertaan) ? '' : 'selected' }}>Pilih Kepesertaan</option>
              @foreach($options['kepesertaans'] as $key => $label)
                <option value="{{ $key }}" {{ old('kepesertaan', $prestasiMandiri->kepesertaan) == $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('kepesertaan')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-4">
            <label for="bentuk" class="form-label fw-medium">Bentuk<span class="text-danger">*</span></label>
            <select class="form-select @error('bentuk') is-invalid @enderror" id="bentuk" name="bentuk" required>
              <option value="" disabled {{ old('bentuk', $prestasiMandiri->bentuk) ? '' : 'selected' }}>Pilih Bentuk</option>
              @foreach($options['bentuks'] as $key => $label)
                <option value="{{ $key }}" {{ old('bentuk', $prestasiMandiri->bentuk) == $key ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
            @error('bentuk')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label for="url_kompetisi" class="form-label fw-medium">URL Kompetisi / Lomba<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('url_kompetisi') is-invalid @enderror" id="url_kompetisi" name="url_kompetisi" value="{{ old('url_kompetisi', $prestasiMandiri->url_kompetisi) }}" placeholder="URL" required>
            </div>
            @error('url_kompetisi')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="file_dokumen_sertifikat" class="form-label fw-medium">Upload File Sertifikat</label>
            @if($prestasiMandiri->file_dokumen_sertifikat)
              @php $isDrive = str_contains($prestasiMandiri->file_dokumen_sertifikat, 'drive.google.com'); @endphp
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi {{ $isDrive ? 'bi-google text-success' : 'bi-file-earmark-check text-success' }} me-1"></i>
                  {{ $isDrive ? 'Berkas Google Drive' : basename($prestasiMandiri->file_dokumen_sertifikat) }}
                </span>
                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'sertifikat']) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi {{ $isDrive ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i> {{ $isDrive ? 'Buka Drive' : 'Unduh' }}
                </a>
              </div>
            @elseif($prestasiMandiri->link_dokumen_sertifikat)
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi bi-link-45deg text-info me-1"></i> {{ $prestasiMandiri->link_dokumen_sertifikat }}
                </span>
                <a href="{{ $prestasiMandiri->link_dokumen_sertifikat }}" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi bi-box-arrow-up-right"></i> Buka
                </a>
              </div>
            @endif
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-upload"></i></span>
              <input type="file" class="form-control @error('file_dokumen_sertifikat') is-invalid @enderror" id="file_dokumen_sertifikat" name="file_dokumen_sertifikat" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: .pdf, .jpg, .jpeg, .png (Maks. 20 MB). Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="col-md-6">
            <label for="link_dokumen_sertifikat" class="form-label fw-medium">Link Dokumen Sertifikat <span class="text-muted fw-normal">(URL)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_dokumen_sertifikat') is-invalid @enderror" id="link_dokumen_sertifikat" name="link_dokumen_sertifikat" value="{{ old('link_dokumen_sertifikat', $prestasiMandiri->link_dokumen_sertifikat) }}" placeholder="https://...">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan Google Drive / URL dokumen untuk SIMKATMAWA.</small>
          </div>

          <div class="col-md-6">
            <label for="tanggal_sertifikat" class="form-label fw-medium">Tanggal Sertifikat<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-calendar-event"></i></span>
              <input type="date" class="form-control @error('tanggal_sertifikat') is-invalid @enderror" id="tanggal_sertifikat" name="tanggal_sertifikat" value="{{ old('tanggal_sertifikat', $prestasiMandiri->tanggal_sertifikat ? $prestasiMandiri->tanggal_sertifikat->format('Y-m-d') : '') }}" required>
            </div>
            @error('tanggal_sertifikat')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="file_foto_upp" class="form-label fw-medium">Upload Foto Upacara Penyerahan Penghargaan</label>
            @php 
              $existingFotoUpp = $prestasiMandiri->file_foto_upp ?? $prestasiMandiri->link_foto_upp;
            @endphp
            @if($existingFotoUpp)
              @php $isDrive = str_contains($existingFotoUpp, 'drive.google.com') || filter_var($existingFotoUpp, FILTER_VALIDATE_URL); @endphp
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi {{ $isDrive ? 'bi-link-45deg text-success' : 'bi-image text-success' }} me-1"></i>
                  {{ $isDrive ? 'Berkas URL / Drive' : basename($existingFotoUpp) }}
                </span>
                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'foto-upp']) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi {{ $isDrive ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i> {{ $isDrive ? 'Buka Link' : 'Lihat Berkas' }}
                </a>
              </div>
            @endif
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-image"></i></span>
              <input type="file" class="form-control @error('file_foto_upp') is-invalid @enderror" id="file_foto_upp" name="file_foto_upp" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: .png, .jpg, .jpeg (Maks. 10 MB). Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="col-md-6">
            <label for="link_foto_upp" class="form-label fw-medium">Link Foto Upacara Penyerahan Penghargaan <span class="text-muted fw-normal">(URL)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_foto_upp') is-invalid @enderror" id="link_foto_upp" name="link_foto_upp" value="{{ old('link_foto_upp', $prestasiMandiri->link_foto_upp) }}" placeholder="https://...">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan foto upacara penyerahan penghargaan untuk SIMKATMAWA.</small>
          </div>

          <div class="col-md-6">
            <label for="link_dokumen_undangan" class="form-label fw-medium">Link Dokumen Undangan</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_dokumen_undangan') is-invalid @enderror" id="link_dokumen_undangan" name="link_dokumen_undangan" value="{{ old('link_dokumen_undangan', $prestasiMandiri->link_dokumen_undangan) }}" placeholder="URL">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan dokumen undangan kegiatan / lomba.</small>
          </div>

          <div class="col-md-6">
            <label for="file_dokumen_lpj" class="form-label fw-medium">Upload File LPJ</label>
            @php 
              $existingLpj = $prestasiMandiri->file_dokumen_lpj ?: $prestasiMandiri->link_dokumen_lpj;
            @endphp
            @if($existingLpj)
              @php $isDrive = str_contains($existingLpj, 'drive.google.com') || filter_var($existingLpj, FILTER_VALIDATE_URL); @endphp
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi {{ $isDrive ? 'bi-link-45deg text-success' : 'bi-file-earmark-check text-success' }} me-1"></i>
                  {{ $isDrive ? 'Berkas URL / Drive' : basename($existingLpj) }}
                </span>
                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'lpj']) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi {{ $isDrive ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i> {{ $isDrive ? 'Buka Link' : 'Unduh' }}
                </a>
              </div>
            @endif
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-upload"></i></span>
              <input type="file" class="form-control @error('file_dokumen_lpj') is-invalid @enderror" id="file_dokumen_lpj" name="file_dokumen_lpj" accept=".pdf,.doc,.docx,.zip">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: .pdf, .docx, .doc, .zip (Maks. 20 MB). Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="col-md-6">
            <label for="link_dokumen_lpj" class="form-label fw-medium">Link Dokumen LPJ <span class="text-muted fw-normal">(URL)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_dokumen_lpj') is-invalid @enderror" id="link_dokumen_lpj" name="link_dokumen_lpj" value="{{ old('link_dokumen_lpj', $prestasiMandiri->link_dokumen_lpj) }}" placeholder="https://...">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan dokumen LPJ kegiatan untuk SIMKATMAWA.</small>
          </div>

          <div class="col-md-6">
            <label for="no_hp" class="form-label fw-medium">No. HP / WhatsApp<span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-telephone"></i></span>
              <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $prestasiMandiri->no_hp ?? '') }}" placeholder="Contoh: 081234567890" required>
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Nomor WhatsApp aktif mahasiswa.</small>
            @error('no_hp')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="file_ktm" class="form-label fw-medium">Upload KTM (Kartu Tanda Mahasiswa)</label>
            @php 
              $existingKtm = $prestasiMandiri->file_ktm ?: $prestasiMandiri->link_ktm;
            @endphp
            @if($existingKtm)
              @php $isDrive = str_contains($existingKtm, 'drive.google.com') || filter_var($existingKtm, FILTER_VALIDATE_URL); @endphp
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi {{ $isDrive ? 'bi-link-45deg text-success' : 'bi-file-earmark-check text-success' }} me-1"></i>
                  {{ $isDrive ? 'Berkas URL / Drive' : basename($existingKtm) }}
                </span>
                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'ktm']) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi {{ $isDrive ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i> {{ $isDrive ? 'Buka Link' : 'Unduh' }}
                </a>
              </div>
            @endif
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-person-badge"></i></span>
              <input type="file" class="form-control @error('file_ktm') is-invalid @enderror" id="file_ktm" name="file_ktm" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: .pdf, .jpg, .jpeg, .png (Maks. 10 MB). Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="col-md-6">
            <label for="link_ktm" class="form-label fw-medium">Link KTM <span class="text-muted fw-normal">(URL)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_ktm') is-invalid @enderror" id="link_ktm" name="link_ktm" value="{{ old('link_ktm', $prestasiMandiri->link_ktm) }}" placeholder="https://...">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan berkas KTM mahasiswa untuk SIMKATMAWA.</small>
          </div>

          <div class="col-md-6">
            <label for="file_pengajuan_reward" class="form-label fw-medium">Upload Surat Pengajuan Reward Prestasi</label>
            @php 
              $existingReward = $prestasiMandiri->file_pengajuan_reward ?: $prestasiMandiri->link_pengajuan_reward;
            @endphp
            @if($existingReward)
              @php $isDrive = str_contains($existingReward, 'drive.google.com') || filter_var($existingReward, FILTER_VALIDATE_URL); @endphp
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi {{ $isDrive ? 'bi-link-45deg text-success' : 'bi-file-earmark-check text-success' }} me-1"></i>
                  {{ $isDrive ? 'Berkas URL / Drive' : basename($existingReward) }}
                </span>
                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'reward']) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi {{ $isDrive ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i> {{ $isDrive ? 'Buka Link' : 'Unduh' }}
                </a>
              </div>
            @endif
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-file-earmark-arrow-up"></i></span>
              <input type="file" class="form-control @error('file_pengajuan_reward') is-invalid @enderror" id="file_pengajuan_reward" name="file_pengajuan_reward" accept=".pdf,.doc,.docx">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: .pdf, .docx, .doc (Maks. 20 MB). Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="col-md-6">
            <label for="link_pengajuan_reward" class="form-label fw-medium">Link Surat Pengajuan Reward <span class="text-muted fw-normal">(URL)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_pengajuan_reward') is-invalid @enderror" id="link_pengajuan_reward" name="link_pengajuan_reward" value="{{ old('link_pengajuan_reward', $prestasiMandiri->link_pengajuan_reward) }}" placeholder="https://...">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan surat pengajuan reward prestasi.</small>
          </div>

          <div class="col-md-6">
            <label for="file_surat_tugas" class="form-label fw-medium">Upload Surat Tugas dari WR III / WD I</label>
            @php 
              $existingSuratTugas = $prestasiMandiri->file_surat_tugas ?: $prestasiMandiri->link_surat_tugas;
            @endphp
            @if($existingSuratTugas)
              @php $isDrive = str_contains($existingSuratTugas, 'drive.google.com') || filter_var($existingSuratTugas, FILTER_VALIDATE_URL); @endphp
              <div class="mb-1 d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border">
                <span class="small text-truncate text-secondary" style="max-width: 250px;">
                  <i class="bi {{ $isDrive ? 'bi-link-45deg text-success' : 'bi-file-earmark-check text-success' }} me-1"></i>
                  {{ $isDrive ? 'Berkas URL / Drive' : basename($existingSuratTugas) }}
                </span>
                <a href="{{ route('prestasi-mandiri.download-file', [$prestasiMandiri->id, 'surat-tugas']) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;" target="_blank">
                  <i class="bi {{ $isDrive ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i> {{ $isDrive ? 'Buka Link' : 'Unduh' }}
                </a>
              </div>
            @endif
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-file-earmark-check"></i></span>
              <input type="file" class="form-control @error('file_surat_tugas') is-invalid @enderror" id="file_surat_tugas" name="file_surat_tugas" accept=".pdf,.doc,.docx">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Surat Tugas WR III atau Wakil Dekan I. Format: .pdf, .docx, .doc (Maks. 20 MB). Kosongkan jika tidak ingin mengganti.</small>
          </div>

          <div class="col-md-6">
            <label for="link_surat_tugas" class="form-label fw-medium">Link Surat Tugas <span class="text-muted fw-normal">(URL)</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-link-45deg"></i></span>
              <input type="url" class="form-control @error('link_surat_tugas') is-invalid @enderror" id="link_surat_tugas" name="link_surat_tugas" value="{{ old('link_surat_tugas', $prestasiMandiri->link_surat_tugas) }}" placeholder="https://...">
            </div>
            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Tautan surat tugas WR III / WD I untuk SIMKATMAWA.</small>
          </div>

          <div class="col-12">
            <label for="keterangan" class="form-label fw-medium">Keterangan</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-secondary"><i class="bi bi-card-text"></i></span>
              <input type="text" class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" value="{{ old('keterangan', $prestasiMandiri->keterangan) }}" placeholder="Keterangan tambahan">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Data Mahasiswa -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center">
            <div class="p-2 rounded-circle bg-primary-subtle text-primary me-3">
              <i class="bi bi-person fs-4"></i>
            </div>
            <div>
              <h5 class="fw-bold text-dark mb-0">Data Mahasiswa</h5>
              <small class="text-muted">Minimal satu mahasiswa wajib diisi sebelum data dapat disimpan.</small>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btn-add-mahasiswa">
            <i class="bi bi-plus-lg me-1"></i> Tambah
          </button>
        </div>

        <div id="container-mahasiswa">
          @php
            $mahasiswaList = old('data_mahasiswa', $prestasiMandiri->data_mahasiswa ?? [['nim' => '', 'nama' => '']]);
            if (empty($mahasiswaList)) {
              $mahasiswaList = [['nim' => '', 'nama' => '']];
            }
          @endphp

          @foreach($mahasiswaList as $index => $mhs)
            <div class="row g-3 align-items-end mb-2 row-mahasiswa">
              <div class="col-md-5">
                <label class="form-label small fw-medium text-secondary mb-1">NIM <span class="text-danger">*</span></label>
                <input type="text" name="data_mahasiswa[{{ $index }}][nim]" class="form-control form-control-sm" placeholder="NIM" value="{{ $mhs['nim'] ?? '' }}">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-medium text-secondary mb-1">Nama Mahasiswa <span class="text-danger">*</span></label>
                <input type="text" name="data_mahasiswa[{{ $index }}][nama]" class="form-control form-control-sm" placeholder="Nama Mahasiswa" value="{{ $mhs['nama'] ?? '' }}">
              </div>
              <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="padding: 4px 10px;">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Card 3: Data Dosen Pendamping -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex align-items-center">
            <div class="p-2 rounded-circle bg-primary-subtle text-primary me-3">
              <i class="bi bi-person-badge fs-4"></i>
            </div>
            <div>
              <h5 class="fw-bold text-dark mb-0">Data Dosen Pendamping</h5>
              <small class="text-muted">Isi dosen pendamping beserta tautan surat tugasnya.</small>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btn-add-dosen">
            <i class="bi bi-plus-lg me-1"></i> Tambah
          </button>
        </div>

        <div id="container-dosen">
          @php
            $dosenList = old('data_dosen', $prestasiMandiri->data_dosen ?? [['nidn' => '', 'nama' => '', 'url_surat' => '']]);
            if (empty($dosenList)) {
              $dosenList = [['nidn' => '', 'nama' => '', 'url_surat' => '']];
            }
          @endphp

          @foreach($dosenList as $index => $dsn)
            <div class="row g-3 align-items-end mb-2 row-dosen">
              <div class="col-md-3">
                <label class="form-label small fw-medium text-secondary mb-1">NIDN/NUPTK</label>
                <input type="text" name="data_dosen[{{ $index }}][nidn]" class="form-control form-control-sm" placeholder="NIDN/NUPTK" value="{{ $dsn['nidn'] ?? '' }}">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-medium text-secondary mb-1">Nama Dosen</label>
                <input type="text" name="data_dosen[{{ $index }}][nama]" class="form-control form-control-sm" placeholder="Nama Dosen" value="{{ $dsn['nama'] ?? '' }}">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-medium text-secondary mb-1">URL Surat Tugas</label>
                <input type="text" name="data_dosen[{{ $index }}][url_surat]" class="form-control form-control-sm" placeholder="URL Surat Tugas" value="{{ $dsn['url_surat'] ?? '' }}">
              </div>
              <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="padding: 4px 10px;">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Action Footer -->
    <div class="d-flex justify-content-between align-items-center mb-5">
      <a href="{{ route('prestasi-mandiri.index') }}" class="btn btn-light border px-4 shadow-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
      <button type="submit" class="btn btn-dark px-4 shadow-sm" style="background-color: #0f172a; border-color: #0f172a;">
        <i class="bi bi-save me-1"></i> Perbarui Data
      </button>
    </div>
  </form>
</section>

<!-- Dynamic Rows JS -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  let mhsIndex = {{ count($mahasiswaList) }};
  let dsnIndex = {{ count($dosenList) }};

  // Add Mahasiswa Row
  document.getElementById('btn-add-mahasiswa').addEventListener('click', function () {
    const container = document.getElementById('container-mahasiswa');
    const row = document.createElement('div');
    row.className = 'row g-3 align-items-end mb-2 row-mahasiswa';
    row.innerHTML = `
      <div class="col-md-5">
        <label class="form-label small fw-medium text-secondary mb-1">NIM <span class="text-danger">*</span></label>
        <input type="text" name="data_mahasiswa[${mhsIndex}][nim]" class="form-control form-control-sm" placeholder="NIM">
      </div>
      <div class="col-md-6">
        <label class="form-label small fw-medium text-secondary mb-1">Nama Mahasiswa <span class="text-danger">*</span></label>
        <input type="text" name="data_mahasiswa[${mhsIndex}][nama]" class="form-control form-control-sm" placeholder="Nama Mahasiswa">
      </div>
      <div class="col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="padding: 4px 10px;">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    `;
    container.appendChild(row);
    mhsIndex++;
  });

  // Add Dosen Row
  document.getElementById('btn-add-dosen').addEventListener('click', function () {
    const container = document.getElementById('container-dosen');
    const row = document.createElement('div');
    row.className = 'row g-3 align-items-end mb-2 row-dosen';
    row.innerHTML = `
      <div class="col-md-3">
        <label class="form-label small fw-medium text-secondary mb-1">NIDN/NUPTK</label>
        <input type="text" name="data_dosen[${dsnIndex}][nidn]" class="form-control form-control-sm" placeholder="NIDN/NUPTK">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-medium text-secondary mb-1">Nama Dosen</label>
        <input type="text" name="data_dosen[${dsnIndex}][nama]" class="form-control form-control-sm" placeholder="Nama Dosen">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-medium text-secondary mb-1">URL Surat Tugas</label>
        <input type="text" name="data_dosen[${dsnIndex}][url_surat]" class="form-control form-control-sm" placeholder="URL Surat Tugas">
      </div>
      <div class="col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" style="padding: 4px 10px;">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    `;
    container.appendChild(row);
    dsnIndex++;
  });

  // Delegate Remove Row
  document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-remove-row')) {
      const row = e.target.closest('.row-mahasiswa') || e.target.closest('.row-dosen');
      if (row) {
        row.remove();
      }
    }
  });

  // Client-Side Validation & SweetAlert Warning for Required Fields
  const formPrestasi = document.getElementById('form-prestasi-mandiri');
  if (formPrestasi) {
    formPrestasi.addEventListener('submit', function (e) {
      const missingFields = [];
      let firstInvalidEl = null;

      // Bersihkan error lama
      document.querySelectorAll('.client-invalid-msg').forEach(el => el.remove());
      formPrestasi.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

      function markFieldInvalid(el, labelName) {
        if (!el) return;
        el.classList.add('is-invalid');
        missingFields.push(labelName);
        if (!firstInvalidEl) {
          firstInvalidEl = el;
        }

        const colParent = el.closest('.col-md-6') || el.closest('.col-12') || el.closest('.col-md-4') || el.parentElement;
        if (colParent && !colParent.querySelector('.client-invalid-msg')) {
          const msg = document.createElement('div');
          msg.className = 'invalid-feedback d-block client-invalid-msg text-danger small mt-1';
          msg.innerHTML = `<i class="bi bi-exclamation-circle me-1"></i> Kolom <b>${labelName}</b> wajib diisi.`;
          colParent.appendChild(msg);
        }
      }

      // 1. Validasi field utama
      const requiredInputs = [
        { id: 'level', label: 'Level' },
        { id: 'kategori', label: 'Kategori' },
        { id: 'nama_kompetisi', label: 'Nama Kompetisi / Lomba' },
        { id: 'nama_cabang', label: 'Nama Cabang' },
        { id: 'peringkat', label: 'Peringkat' },
        { id: 'nama_penyelenggara', label: 'Nama Penyelenggara' },
        { id: 'jumlah_pt_peserta', label: 'Jumlah PT Peserta' },
        { id: 'kepesertaan', label: 'Kepesertaan' },
        { id: 'bentuk', label: 'Bentuk' },
        { id: 'url_kompetisi', label: 'URL Kompetisi / Lomba' },
        { id: 'tanggal_sertifikat', label: 'Tanggal Sertifikat' },
        { id: 'no_hp', label: 'No. HP / WhatsApp' }
      ];

      requiredInputs.forEach(item => {
        const inputEl = document.getElementById(item.id);
        if (inputEl && (!inputEl.value || !inputEl.value.trim())) {
          markFieldInvalid(inputEl, item.label);
        }
      });

      // 2. Validasi Data Mahasiswa (Minimal 1 mahasiswa dengan NIM & Nama)
      const rowsMahasiswa = document.querySelectorAll('.row-mahasiswa');
      let validMahasiswaCount = 0;
      rowsMahasiswa.forEach(row => {
        const nim = row.querySelector('input[name*="[nim]"]');
        const nama = row.querySelector('input[name*="[nama]"]');
        if (nim && nama && nim.value.trim() && nama.value.trim()) {
          validMahasiswaCount++;
        }
      });

      if (validMahasiswaCount === 0) {
        missingFields.push('Data Mahasiswa (NIM dan Nama)');
        const mhsContainer = document.getElementById('container-mahasiswa');
        if (mhsContainer) {
          const firstNim = mhsContainer.querySelector('input[name*="[nim]"]');
          const firstNama = mhsContainer.querySelector('input[name*="[nama]"]');
          if (firstNim) firstNim.classList.add('is-invalid');
          if (firstNama) firstNama.classList.add('is-invalid');
          if (!firstInvalidEl) firstInvalidEl = firstNim || firstNama;

          let mhsMsg = mhsContainer.parentElement.querySelector('.client-invalid-msg');
          if (!mhsMsg) {
            mhsMsg = document.createElement('div');
            mhsMsg.className = 'invalid-feedback d-block client-invalid-msg text-danger small mt-2';
            mhsMsg.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Minimal satu data mahasiswa (NIM & Nama) wajib diisi sebelum menyimpan.';
            mhsContainer.parentElement.appendChild(mhsMsg);
          }
        }
      }

      // Jika ada kolom wajib yang kosong: Tampilkan warning di setiap field & scroll ke field pertama
      if (missingFields.length > 0) {
        e.preventDefault();
        e.stopPropagation();

        if (firstInvalidEl) {
          firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
          setTimeout(() => firstInvalidEl.focus(), 200);
        }

        return false;
      }
    });

    // Fokus ke field pertama yang error jika ada validasi dari server (Form Request)
    const firstServerError = formPrestasi.querySelector('.is-invalid');
    if (firstServerError) {
      firstServerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      setTimeout(() => firstServerError.focus(), 200);
    }

    // Otomatis hilangkan border merah saat pengguna mengetik/memilih
    formPrestasi.addEventListener('input', function (e) {
      if (e.target && e.target.classList.contains('is-invalid')) {
        if (e.target.value.trim()) {
          e.target.classList.remove('is-invalid');
          const colParent = e.target.closest('.col-md-6') || e.target.closest('.col-12') || e.target.closest('.col-md-4') || e.target.parentElement;
          if (colParent) {
            const err = colParent.querySelector('.client-invalid-msg');
            if (err) err.remove();
          }
        }
      }
    });

    formPrestasi.addEventListener('change', function (e) {
      if (e.target && e.target.classList.contains('is-invalid')) {
        if (e.target.value.trim()) {
          e.target.classList.remove('is-invalid');
          const colParent = e.target.closest('.col-md-6') || e.target.closest('.col-12') || e.target.closest('.col-md-4') || e.target.parentElement;
          if (colParent) {
            const err = colParent.querySelector('.client-invalid-msg');
            if (err) err.remove();
          }
        }
      }
    });
  }
});
</script>
@endsection
