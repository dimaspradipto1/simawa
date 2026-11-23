@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Ajukan Kegiatan SKP</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('skp.index') }}">SKP Mahasiswa</a></li>
      <li class="breadcrumb-item active">Pengajuan Baru</li>
    </ol>
  </nav>
  <p class="text-muted small">Laporkan kegiatan ekstrakurikuler atau prestasi Anda untuk memperoleh Satuan Kredit Poin (SKP).</p>
</div><!-- End Page Title -->

<section class="section">
  <!-- Banner Petunjuk -->
  <div class="card border-0 shadow-sm rounded-3 mb-4" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);">
    <div class="card-body p-4">
      <div class="d-flex align-items-center gap-3">
        <div class="p-3 bg-white rounded-circle shadow-sm text-primary">
          <i class="bi bi-mortarboard-fill fs-3"></i>
        </div>
        <div>
          <h5 class="fw-bold text-dark mb-1">Form Pengajuan Satuan Kredit Poin (SKP)</h5>
          <p class="small text-secondary mb-0">
            Kegiatan <strong>Akademik</strong> akan diverifikasi oleh <strong>Wakil Dekan 1</strong> fakultas Anda, sedangkan kegiatan <strong>Non-Akademik</strong> akan diverifikasi oleh <strong>Kabid Kemahasiswaan UIS</strong>.
          </p>
        </div>
      </div>
    </div>
  </div>

  <form action="{{ route('skp.store') }}" method="POST" enctype="multipart/form-data" id="form-skp">
    @csrf

    <div class="row g-4">
      <!-- Kolom Kiri: Form Input -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
              <i class="bi bi-info-circle text-primary me-2"></i>Informasi Kegiatan
            </h6>

            <!-- Kategori Kegiatan -->
            <div class="mb-3">
              <label for="kategori_id" class="form-label fw-semibold">Kategori Kegiatan <span class="text-danger">*</span></label>
              <select name="kategori_id" id="kategori_id" class="form-select @error('kategori_id') is-invalid @enderror" required>
                <option value="" disabled selected>-- Pilih Kategori Kegiatan --</option>
                @foreach($kategoris as $key => $cat)
                  <option value="{{ $key }}" data-sifat="{{ $cat['sifat'] }}" data-tingkat="{{ $cat['has_tingkat'] ? '1':'0' }}" data-juara="{{ $cat['has_juara'] ? '1':'0' }}" data-jabatan="{{ $cat['has_jabatan'] ? '1':'0' }}" data-subtipe="{{ $cat['has_subtipe'] ? '1':'0' }}" {{ old('kategori_id') === $key ? 'selected' : '' }}>
                    {{ $cat['nama'] }}
                  </option>
                @endforeach
              </select>
              @error('kategori_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
              <small id="kategori-deskripsi" class="text-muted d-block mt-1" style="font-size: 0.78rem;"></small>
            </div>

            <!-- Sifat Kegiatan (Akademik / Non-Akademik) -->
            <div class="mb-3">
              <label class="form-label fw-semibold">Sifat Kegiatan & Jalur Verifikasi <span class="text-danger">*</span></label>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sifat_kegiatan" id="sifat_akademik" value="Akademik" {{ old('sifat_kegiatan') === 'Akademik' ? 'checked' : '' }}>
                  <label class="form-check-label fw-medium text-primary" for="sifat_akademik">
                    <i class="bi bi-mortarboard me-1"></i> Akademik <span class="text-muted small">(Verifikasi: Wakil Dekan 1)</span>
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="sifat_kegiatan" id="sifat_non_akademik" value="Non-Akademik" {{ old('sifat_kegiatan', 'Non-Akademik') === 'Non-Akademik' ? 'checked' : '' }}>
                  <label class="form-check-label fw-medium text-success" for="sifat_non_akademik">
                    <i class="bi bi-people me-1"></i> Non-Akademik <span class="text-muted small">(Verifikasi: Kabid Kemahasiswaan)</span>
                  </label>
                </div>
              </div>
              @error('sifat_kegiatan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Nama Kegiatan -->
            <div class="mb-3">
              <label for="nama_kegiatan" class="form-label fw-semibold">Nama / Judul Kegiatan <span class="text-danger">*</span></label>
              <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan') }}" placeholder="Contoh: Lomba Inovasi Teknologi Nasional 2025 / Seminar Kepemimpinan Mahasiswa" required>
              @error('nama_kegiatan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Bidang Subtipe / Jenis Khusus (Dinamis) -->
            <div class="mb-3 d-none" id="group-subtipe">
              <label for="jenis_kegiatan" class="form-label fw-semibold" id="label-subtipe">Jenis / Pilihan Item</label>
              <select name="jenis_kegiatan" id="jenis_kegiatan" class="form-select @error('jenis_kegiatan') is-invalid @enderror">
                <option value="">-- Pilih Jenis Item --</option>
              </select>
              @error('jenis_kegiatan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Baris Dinamis: Lingkup Tingkat & Peringkat Juara -->
            <div class="row g-3 mb-3">
              <!-- Lingkup Tingkat -->
              <div class="col-md-6 d-none" id="group-tingkat">
                <label for="lingkup_tingkat" class="form-label fw-semibold">Lingkup Tingkat Kegiatan</label>
                <select name="lingkup_tingkat" id="lingkup_tingkat" class="form-select @error('lingkup_tingkat') is-invalid @enderror">
                  <option value="Kota" {{ old('lingkup_tingkat') === 'Kota' ? 'selected' : '' }}>Kota / Internal Kampus</option>
                  <option value="Provinsi" {{ old('lingkup_tingkat') === 'Provinsi' ? 'selected' : '' }}>Provinsi</option>
                  <option value="Nasional" {{ old('lingkup_tingkat', 'Nasional') === 'Nasional' ? 'selected' : '' }}>Nasional</option>
                  <option value="Internasional" {{ old('lingkup_tingkat') === 'Internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
                @error('lingkup_tingkat')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Peringkat Juara (HANYA MUNCUL DI PERLOMBAAN) -->
              <div class="col-md-6 d-none" id="group-juara">
                <label for="peringkat_juara" class="form-label fw-semibold">Peringkat / Capaian Juara</label>
                <select name="peringkat_juara" id="peringkat_juara" class="form-select @error('peringkat_juara') is-invalid @enderror">
                  <option value="Juara I" {{ old('peringkat_juara', 'Juara I') === 'Juara I' ? 'selected' : '' }}>Juara I (Pertama)</option>
                  <option value="Juara II" {{ old('peringkat_juara') === 'Juara II' ? 'selected' : '' }}>Juara II (Kedua)</option>
                  <option value="Juara III" {{ old('peringkat_juara') === 'Juara III' ? 'selected' : '' }}>Juara III (Ketiga)</option>
                  <option value="Harapan-1" {{ old('peringkat_juara') === 'Harapan-1' ? 'selected' : '' }}>Harapan 1</option>
                  <option value="Harapan-2" {{ old('peringkat_juara') === 'Harapan-2' ? 'selected' : '' }}>Harapan 2</option>
                  <option value="Harapan-3" {{ old('peringkat_juara') === 'Harapan-3' ? 'selected' : '' }}>Harapan 3</option>
                  <option value="Non Juara" {{ old('peringkat_juara') === 'Non Juara' ? 'selected' : '' }}>Peserta (Non Juara)</option>
                </select>
                @error('peringkat_juara')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <!-- Jabatan / Peran (HANYA MUNCUL DI KEPANITIAAN / ORMAWA / RELAWAN) -->
              <div class="col-md-6 d-none" id="group-jabatan">
                <label for="jabatan" class="form-label fw-semibold">Jabatan / Peran</label>
                <select name="jabatan" id="jabatan" class="form-select @error('jabatan') is-invalid @enderror">
                  <option value="Ketua" {{ old('jabatan') === 'Ketua' ? 'selected' : '' }}>Ketua</option>
                  <option value="Wakil" {{ old('jabatan') === 'Wakil' ? 'selected' : '' }}>Wakil Ketua</option>
                  <option value="Sekretaris" {{ old('jabatan') === 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                  <option value="Bendahara" {{ old('jabatan') === 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                  <option value="Koordinator" {{ old('jabatan') === 'Koordinator' ? 'selected' : '' }}>Koordinator Seksi / Bidang</option>
                  <option value="Anggota" {{ old('jabatan', 'Anggota') === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                </select>
                @error('jabatan')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <!-- Tanggal Kegiatan -->
            <div class="mb-3">
              <label for="tanggal_kegiatan" class="form-label fw-semibold">Tanggal Pelaksanaan / Tanggal SK / Sertifikat</label>
              <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control @error('tanggal_kegiatan') is-invalid @enderror" value="{{ old('tanggal_kegiatan', date('Y-m-d')) }}">
              @error('tanggal_kegiatan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <!-- Keterangan Tambahan -->
            <div class="mb-0">
              <label for="keterangan" class="form-label fw-semibold">Keterangan Tambahan (Opsional)</label>
              <textarea name="keterangan" id="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror" placeholder="Catatan tambahan mengenai kegiatan, penyelenggara, atau nomor SK...">{{ old('keterangan') }}</textarea>
              @error('keterangan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        <!-- Card Upload Berkas Bukti -->
        <div class="card border-0 shadow-sm rounded-3 mb-4">
          <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
              <i class="bi bi-file-earmark-check text-success me-2"></i>Berkas Bukti / Dokumen Pendukung
            </h6>

            <div class="mb-3">
              <label for="file_bukti" class="form-label fw-semibold">Upload Berkas Bukti (Sertifikat / Piagam / SK Kepengurusan / Surat Tugas)</label>
              <input type="file" name="file_bukti" id="file_bukti" class="form-control @error('file_bukti') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
              <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Format: PDF, JPG, JPEG, PNG (Maks. 10 MB).</small>
              @error('file_bukti')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-0">
              <label for="link_bukti" class="form-label fw-semibold">Atau Tautan Dokumen (Google Drive / URL Bukti Luar)</label>
              <input type="url" name="link_bukti" id="link_bukti" class="form-control @error('link_bukti') is-invalid @enderror" value="{{ old('link_bukti') }}" placeholder="https://drive.google.com/file/d/... atau https://...">
              <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Gunakan jika dokumen telah diunggah di Google Drive pribadi atau web resmi lomba.</small>
              @error('link_bukti')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>

        @if(!auth()->user()->isMahasiswa())
          <!-- Informasi Mahasiswa (Khusus jika diinputkan oleh Admin) -->
          <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
              <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                <i class="bi bi-person-badge text-warning me-2"></i>Identitas Mahasiswa Pemohon
              </h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">NIM Mahasiswa</label>
                  <input type="text" name="nim" class="form-control form-control-sm" value="{{ old('nim') }}" placeholder="Contoh: 210101001">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold small">Nama Lengkap Mahasiswa</label>
                  <input type="text" name="nama_mahasiswa" class="form-control form-control-sm" value="{{ old('nama_mahasiswa') }}" placeholder="Nama Mahasiswa">
                </div>
              </div>
            </div>
          </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-5">
          <a href="{{ route('skp.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Kembali
          </a>
          <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow">
            <i class="bi bi-send-check me-2"></i> Ajukan Kegiatan Sekarang
          </button>
        </div>
      </div>

      <!-- Kolom Kanan: Live Point Preview Calculator -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 p-4 sticky-top" style="top: 20px; background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
          <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-calculator-fill text-primary"></i> Estimasi Perolehan Poin
          </h6>

          <div class="p-3 bg-white rounded-3 shadow-sm text-center mb-3 border">
            <span class="text-secondary small fw-medium d-block">Poin SKP Yang Diperoleh</span>
            <div class="display-4 fw-bold text-primary my-1" id="preview-poin">0</div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fw-normal" style="font-size: 0.75rem;">
              Pedoman SKP UIS 2025
            </span>
          </div>

          <div class="small text-secondary mb-3">
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span>Jalur Verifikasi:</span>
              <strong id="preview-jalur" class="text-dark">-</strong>
            </div>
            <div class="d-flex justify-content-between py-1 border-bottom">
              <span>Pejabat Verifikator:</span>
              <strong id="preview-pejabat" class="text-dark">-</strong>
            </div>
            <div class="d-flex justify-content-between py-1">
              <span>Status Awal:</span>
              <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
            </div>
          </div>

          <div class="alert alert-info py-2 px-3 small border-0 bg-info bg-opacity-10 text-info-emphasis mb-0">
            <i class="bi bi-lightbulb-fill me-1 text-warning"></i> 
            Poin akan resmi terakumulasi ke dalam transkrip Anda setelah disetujui oleh verifikator terkait.
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const kategoriSelect = document.getElementById('kategori_id');
  const sifatRadios = document.querySelectorAll('input[name="sifat_kegiatan"]');
  const subtipeGroup = document.getElementById('group-subtipe');
  const subtipeSelect = document.getElementById('jenis_kegiatan');
  const subtipeLabel = document.getElementById('label-subtipe');
  const tingkatGroup = document.getElementById('group-tingkat');
  const tingkatSelect = document.getElementById('lingkup_tingkat');
  const juaraGroup = document.getElementById('group-juara');
  const juaraSelect = document.getElementById('peringkat_juara');
  const jabatanGroup = document.getElementById('group-jabatan');
  const jabatanSelect = document.getElementById('jabatan');
  const deskripsiEl = document.getElementById('kategori-deskripsi');

  const previewPoin = document.getElementById('preview-poin');
  const previewJalur = document.getElementById('preview-jalur');
  const previewPejabat = document.getElementById('preview-pejabat');

  const kategoriData = @json($kategoris);

  function updateFormFields() {
    const selectedKat = kategoriSelect.value;
    if (!selectedKat || !kategoriData[selectedKat]) {
      subtipeGroup.classList.add('d-none');
      tingkatGroup.classList.add('d-none');
      juaraGroup.classList.add('d-none');
      jabatanGroup.classList.add('d-none');
      deskripsiEl.textContent = '';
      return;
    }

    const cfg = kategoriData[selectedKat];
    deskripsiEl.textContent = cfg.deskripsi || '';

    // 1. Set default sifat & target role jika belum diubah manual
    if (cfg.sifat === 'Akademik') {
      document.getElementById('sifat_akademik').checked = true;
    } else {
      document.getElementById('sifat_non_akademik').checked = true;
    }

    // 2. Tampilkan / sembunyikan Tingkat
    if (cfg.has_tingkat) {
      tingkatGroup.classList.remove('d-none');
    } else {
      tingkatGroup.classList.add('d-none');
    }

    // 3. Tampilkan / sembunyikan Juara (HANYA UNTUK PERLOMBAAN)
    if (cfg.has_juara) {
      juaraGroup.classList.remove('d-none');
    } else {
      juaraGroup.classList.add('d-none');
    }

    // 4. Tampilkan / sembunyikan Jabatan
    if (cfg.has_jabatan) {
      jabatanGroup.classList.remove('d-none');
    } else {
      jabatanGroup.classList.add('d-none');
    }

    // 5. Tampilkan / sembunyikan Subtipe & isi pilihannya
    if (cfg.has_subtipe && cfg.subtipe_options && cfg.subtipe_options.length > 0) {
      subtipeGroup.classList.remove('d-none');
      subtipeLabel.textContent = (selectedKat === 'kegiatan_wajib') ? 'Pilihan Kegiatan Wajib' : ((selectedKat === 'pengurus_ormawa') ? 'Lembaga / Organisasi' : 'Jenis Kegiatan');
      subtipeSelect.innerHTML = '<option value="">-- Pilih ' + subtipeLabel.textContent + ' --</option>';
      cfg.subtipe_options.forEach(opt => {
        const optionEl = document.createElement('option');
        optionEl.value = opt;
        optionEl.textContent = opt;
        subtipeSelect.appendChild(optionEl);
      });
    } else {
      subtipeGroup.classList.add('d-none');
      subtipeSelect.innerHTML = '<option value="">-- Tidak Diperlukan --</option>';
    }

    calculatePointsLive();
  }

  function calculatePointsLive() {
    const kat = kategoriSelect.value;
    const sifat = document.querySelector('input[name="sifat_kegiatan"]:checked')?.value || 'Non-Akademik';
    const tingkat = tingkatSelect.value;
    const juara = juaraSelect.value;
    const jabatan = jabatanSelect.value;
    const subtipe = subtipeSelect.value;

    previewJalur.textContent = sifat;
    previewPejabat.textContent = (sifat === 'Akademik') ? 'Wakil Dekan 1' : 'Kabid Kemahasiswaan';

    if (!kat) {
      previewPoin.textContent = '0';
      return;
    }

    // Panggil AJAX kalkulator preview
    fetch(`{{ route('skp.calculate-preview') }}?kategori_id=${encodeURIComponent(kat)}&lingkup_tingkat=${encodeURIComponent(tingkat)}&peringkat_juara=${encodeURIComponent(juara)}&jabatan=${encodeURIComponent(jabatan)}&jenis_kegiatan=${encodeURIComponent(subtipe)}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          previewPoin.textContent = data.points;
        }
      })
      .catch(() => {
        // Fallback default
        previewPoin.textContent = '10';
      });
  }

  kategoriSelect.addEventListener('change', updateFormFields);
  sifatRadios.forEach(radio => radio.addEventListener('change', calculatePointsLive));
  tingkatSelect.addEventListener('change', calculatePointsLive);
  juaraSelect.addEventListener('change', calculatePointsLive);
  jabatanSelect.addEventListener('change', calculatePointsLive);
  subtipeSelect.addEventListener('change', calculatePointsLive);

  // Inisialisasi awal saat halaman dimuat
  if (kategoriSelect.value) {
    updateFormFields();
  }
});
</script>
@endsection
