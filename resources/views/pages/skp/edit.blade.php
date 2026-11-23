@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Edit Kegiatan SKP</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('skp.index') }}">SKP Mahasiswa</a></li>
      <li class="breadcrumb-item active">Edit Kegiatan</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<section class="section">
  <form action="{{ route('skp.update', $skp->id) }}" method="POST" enctype="multipart/form-data" id="form-skp">
    @csrf
    @method('PUT')

    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
              <i class="bi bi-pencil-square text-primary me-2"></i>Perbarui Data Kegiatan
            </h6>

            <!-- Kategori Kegiatan -->
            <div class="mb-3">
              <label for="kategori_id" class="form-label fw-semibold">Kategori Kegiatan <span class="text-danger">*</span></label>
              <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                @foreach($kategoris as $key => $cat)
                  <option value="{{ $key }}" data-sifat="{{ $cat['sifat'] }}" data-tingkat="{{ $cat['has_tingkat'] ? '1':'0' }}" data-juara="{{ $cat['has_juara'] ? '1':'0' }}" data-jabatan="{{ $cat['has_jabatan'] ? '1':'0' }}" data-subtipe="{{ $cat['has_subtipe'] ? '1':'0' }}" {{ old('kategori_id', $skp->kategori_id) === $key ? 'selected' : '' }}>
                    {{ $cat['nama'] }}
                  </option>
                @endforeach
              </select>
              @error('kategori_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Sifat Kegiatan (Akademik / Non-Akademik) -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Sifat Kegiatan & Jalur Verifikasi <span class="text-danger">*</span></label>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sifat_kegiatan" id="sifat_akademik" value="Akademik" {{ old('sifat_kegiatan', $skp->sifat_kegiatan) === 'Akademik' ? 'checked' : '' }}>
                  <label class="form-check-label fw-medium text-primary" for="sifat_akademik">
                    <i class="bi bi-mortarboard me-1"></i> Akademik <span class="text-muted small">(Verifikasi: WD 1)</span>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sifat_kegiatan" id="sifat_non_akademik" value="Non-Akademik" {{ old('sifat_kegiatan', $skp->sifat_kegiatan) === 'Non-Akademik' ? 'checked' : '' }}>
                  <label class="form-check-label fw-medium text-success" for="sifat_non_akademik">
                    <i class="bi bi-people me-1"></i> Non-Akademik <span class="text-muted small">(Verifikasi: Kabid Kemahasiswaan)</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Nama Kegiatan -->
            <div class="mb-3">
              <label for="nama_kegiatan" class="form-label fw-semibold">Nama / Judul Kegiatan <span class="text-danger">*</span></label>
              <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan', $skp->nama_kegiatan) }}" required>
            </div>

            <!-- Jenis Khusus / Subtipe -->
            <div class="mb-3 {{ $skp->jenis_kegiatan ? '' : 'd-none' }}" id="group-subtipe">
              <label for="jenis_kegiatan" class="form-label fw-semibold" id="label-subtipe">Jenis / Pilihan Item</label>
              <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-select">
                @if($skp->jenis_kegiatan)
                  <option value="{{ $skp->jenis_kegiatan }}" selected>{{ $skp->jenis_kegiatan }}</option>
                @endif
              </select>
            </div>

            <div class="row g-3 mb-3">
              <!-- Tingkat -->
              <div class="col-md-6" id="group-tingkat">
                <label for="lingkup_tingkat" class="form-label fw-semibold">Lingkup Tingkat</label>
                <select name="lingkup_tingkat" id="lingkup_tingkat" class="form-select">
                  <option value="Kota" {{ old('lingkup_tingkat', $skp->lingkup_tingkat) === 'Kota' ? 'selected' : '' }}>Kota / Internal Kampus</option>
                  <option value="Provinsi" {{ old('lingkup_tingkat', $skp->lingkup_tingkat) === 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                  <option value="Nasional" {{ old('lingkup_tingkat', $skp->lingkup_tingkat) === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                  <option value="Internasional" {{ old('lingkup_tingkat', $skp->lingkup_tingkat) === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
              </div>

              <!-- Juara -->
              <div class="col-md-6 {{ $skp->peringkat_juara ? '' : 'd-none' }}" id="group-juara">
                <label for="peringkat_juara" class="form-label fw-semibold">Peringkat Juara</label>
                <select name="peringkat_juara" id="peringkat_juara" class="form-select">
                  <option value="Juara I" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Juara I' ? 'selected' : '' }}>Juara I (Pertama)</option>
                  <option value="Juara II" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Juara II' ? 'selected' : '' }}>Juara II (Kedua)</option>
                  <option value="Juara III" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Juara III' ? 'selected' : '' }}>Juara III (Ketiga)</option>
                  <option value="Harapan-1" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Harapan-1' ? 'selected' : '' }}>Harapan 1</option>
                  <option value="Harapan-2" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Harapan-2' ? 'selected' : '' }}>Harapan 2</option>
                  <option value="Harapan-3" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Harapan-3' ? 'selected' : '' }}>Harapan 3</option>
                  <option value="Non Juara" {{ old('peringkat_juara', $skp->peringkat_juara) === 'Non Juara' ? 'selected' : '' }}>Peserta (Non Juara)</option>
                </select>
              </div>

              <!-- Jabatan -->
              <div class="col-md-6 {{ $skp->jabatan ? '' : 'd-none' }}" id="group-jabatan">
                <label for="jabatan" class="form-label fw-semibold">Jabatan / Peran</label>
                <select name="jabatan" id="jabatan" class="form-select">
                  <option value="Ketua" {{ old('jabatan', $skp->jabatan) === 'Ketua' ? 'selected' : '' }}>Ketua</option>
                  <option value="Wakil" {{ old('jabatan', $skp->jabatan) === 'Wakil' ? 'selected' : '' }}>Wakil Ketua</option>
                  <option value="Sekretaris" {{ old('jabatan', $skp->jabatan) === 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                  <option value="Bendahara" {{ old('jabatan', $skp->jabatan) === 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                  <option value="Koordinator" {{ old('jabatan', $skp->jabatan) === 'Koordinator' ? 'selected' : '' }}>Koordinator</option>
                  <option value="Anggota" {{ old('jabatan', $skp->jabatan) === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                </select>
              </div>
            </div>

            <!-- Tanggal -->
            <div class="mb-3">
              <label for="tanggal_kegiatan" class="form-label fw-semibold">Tanggal Pelaksanaan</label>
              <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control" value="{{ old('tanggal_kegiatan', $skp->tanggal_kegiatan ? $skp->tanggal_kegiatan->format('Y-m-d') : '') }}">
            </div>

            <!-- Keterangan -->
            <div class="mb-0">
              <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
              <textarea name="keterangan" id="keterangan" rows="2" class="form-control">{{ old('keterangan', $skp->keterangan) }}</textarea>
            </div>
          </div>
        </div>

        <!-- Berkas Bukti -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
              <i class="bi bi-file-earmark-check text-success me-2"></i>Berkas Bukti
            </h6>
            @if($skp->bukti_url)
              <div class="alert alert-light border d-flex align-items-center justify-content-between p-2 mb-3">
                <span class="small"><i class="bi bi-file-earmark-text text-primary me-1"></i> Berkas bukti saat ini tersedia.</span>
                <a href="{{ $skp->bukti_url }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                  <i class="bi bi-eye me-1"></i> Buka File
                </a>
              </div>
            @endif
            <div class="mb-3">
              <label for="file_bukti" class="form-label fw-semibold">Ganti File Bukti (Opsional)</label>
              <input type="file" name="file_bukti" id="file_bukti" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="mb-0">
              <label for="link_bukti" class="form-label fw-semibold">Link Dokumen (Google Drive / URL Luar)</label>
              <input type="url" name="link_bukti" id="link_bukti" class="form-control" value="{{ old('link_bukti', $skp->link_bukti) }}">
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-5">
          <a href="{{ route('skp.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">Batal</a>
          <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow">Perbarui Data</button>
        </div>
      </div>

      <!-- Kolom Kanan: Preview Poin -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 p-4 sticky-top" style="top: 20px; background: #f8fafc;">
          <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calculator text-primary me-2"></i>Estimasi Poin</h6>
          <div class="p-3 bg-white rounded-3 shadow-sm text-center mb-3 border">
            <span class="text-secondary small d-block">Poin SKP</span>
            <div class="display-4 fw-bold text-primary my-1" id="preview-poin">{{ $skp->poin_skp }}</div>
          </div>
          <div class="small text-secondary mb-3">
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span>Jalur:</span><strong id="preview-jalur">{{ $skp->sifat_kegiatan }}</strong>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span>Pejabat:</span><strong id="preview-pejabat">{{ $skp->sifat_kegiatan === 'Akademik' ? 'Wakil Dekan 1' : 'Kabid Kemahasiswaan' }}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
@endsection
