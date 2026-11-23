<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Transkrip SKP - {{ $user->name }} ({{ $user->nim }})</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11pt;
      color: #1e293b;
      margin: 20px 30px;
      line-height: 1.4;
    }
    .header-table {
      width: 100%;
      border-bottom: 3px double #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    .header-table td {
      vertical-align: middle;
    }
    .inst-title {
      font-size: 14pt;
      font-weight: bold;
      text-transform: uppercase;
      text-align: center;
      margin: 0;
    }
    .inst-sub {
      font-size: 10pt;
      text-align: center;
      margin: 2px 0;
      color: #475569;
    }
    .doc-title {
      text-align: center;
      font-size: 13pt;
      font-weight: bold;
      text-transform: uppercase;
      margin: 15px 0 5px 0;
      letter-spacing: 0.5px;
    }
    .doc-sub {
      text-align: center;
      font-size: 10pt;
      margin-bottom: 20px;
      color: #64748b;
    }
    .bio-table {
      width: 100%;
      margin-bottom: 20px;
      font-size: 10pt;
    }
    .bio-table td {
      padding: 3px 0;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 9.5pt;
    }
    .data-table th, .data-table td {
      border: 1px solid #cbd5e1;
      padding: 6px 8px;
    }
    .data-table th {
      background-color: #f1f5f9;
      font-weight: bold;
      text-align: center;
    }
    .score-summary {
      width: 100%;
      margin-bottom: 30px;
      border: 1px solid #94a3b8;
      background-color: #f8fafc;
      padding: 10px 15px;
      border-radius: 4px;
      font-size: 10pt;
    }
    .signature-table {
      width: 100%;
      margin-top: 40px;
      font-size: 10pt;
    }
    .signature-table td {
      text-align: center;
      vertical-align: top;
      width: 50%;
    }
    @media print {
      body { margin: 0; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>

  <div class="no-print" style="margin-bottom: 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 16px; background-color: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
      Cetak Dokumen (Print)
    </button>
  </div>

  <!-- Kop Surat Universitas Ibnu Sina -->
  <table class="header-table">
    <tr>
      <td style="width: 15%; text-align: center;">
        <img src="{{ asset('assets/img/logo-uis.png') }}" style="max-height: 80px;" onerror="this.style.display='none'">
      </td>
      <td style="width: 85%;">
        <div class="inst-title">UNIVERSITAS IBNU SINA (UIS)</div>
        <div class="inst-sub">BIRO KEMAHASISWAAN, ALUMNI, DAN KONSELING (BKAK)</div>
        <div class="inst-sub">Jl. Teuku Umar, Lubuk Baja Kota, Kec. Lubuk Baja, Kota Batam, Kepulauan Riau</div>
        <div class="inst-sub" style="font-size: 8.5pt;">Website: uis.ac.id | Email: bkak@uis.ac.id</div>
      </td>
    </tr>
  </table>

  <div class="doc-title">TRANSKRIP SATUAN KREDIT POIN (SKP)</div>
  <div class="doc-sub">Lampiran Portofolio Kegiatan Ekstrakurikuler Mahasiswa (SKPI) - Pedoman 2025</div>

  <!-- Biodata Mahasiswa -->
  <table class="bio-table">
    <tr>
      <td style="width: 20%;"><strong>Nama Mahasiswa</strong></td>
      <td style="width: 2%;">:</td>
      <td style="width: 38%;">{{ $user->name }}</td>
      <td style="width: 15%;"><strong>Program Studi</strong></td>
      <td style="width: 2%;">:</td>
      <td style="width: 23%;">{{ $user->program_studi ?? '-' }}</td>
    </tr>
    <tr>
      <td><strong>Nomor Induk (NIM)</strong></td>
      <td>:</td>
      <td>{{ $user->nim ?? '-' }}</td>
      <td><strong>Fakultas</strong></td>
      <td>:</td>
      <td>{{ $user->fakultas ?? '-' }}</td>
    </tr>
    <tr>
      <td><strong>Tanggal Cetak</strong></td>
      <td>:</td>
      <td>{{ now()->translatedFormat('d F Y') }}</td>
      <td><strong>Status Kelulusan SKP</strong></td>
      <td>:</td>
      <td><strong>{{ $totalPoin >= 100 ? 'MEMENUHI SYARAT' : 'BELUM MEMENUHI' }}</strong></td>
    </tr>
  </table>

  <!-- Tabel Kegiatan SKP -->
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 4%;">No</th>
        <th>Nama Kegiatan / Pengalaman</th>
        <th style="width: 16%;">Kategori</th>
        <th style="width: 18%;">Tingkat / Capaian</th>
        <th style="width: 12%;">Sifat</th>
        <th style="width: 8%;">Poin</th>
      </tr>
    </thead>
    <tbody>
      @forelse($approvedKegiatans as $index => $item)
        <tr>
          <td style="text-align: center;">{{ $index + 1 }}</td>
          <td>
            <strong>{{ $item->nama_kegiatan }}</strong>
            @if($item->tanggal_kegiatan)<br><small style="color: #64748b;">Tanggal: {{ $item->tanggal_kegiatan->translatedFormat('d M Y') }}</small>@endif
          </td>
          <td>{{ $item->kategori_id }}</td>
          <td>
            {{ $item->lingkup_tingkat ?? '' }}
            {{ $item->peringkat_juara ? ' - '.$item->peringkat_juara : '' }}
            {{ $item->jabatan ? ' ('.$item->jabatan.')' : '' }}
          </td>
          <td style="text-align: center;">{{ $item->sifat_kegiatan }}</td>
          <td style="text-align: center; font-weight: bold; color: #0369a1;">+{{ $item->poin_skp }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">
            Belum ada kegiatan ekstrakurikuler yang diverifikasi dan disetujui.
          </td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr style="background-color: #f1f5f9; font-weight: bold;">
        <td colspan="5" style="text-align: right; padding-right: 15px;">TOTAL SATUAN KREDIT POIN (SKP):</td>
        <td style="text-align: center; font-size: 11pt; color: #0284c7;">{{ number_format($totalPoin) }}</td>
      </tr>
    </tfoot>
  </table>

  <!-- Rekapitulasi Predikat & Lencana -->
  <div class="score-summary">
    <table style="width: 100%;">
      <tr>
        <td style="width: 33%;">
          <strong>Nilai Huruf:</strong> {{ $gradeInfo['grade'] }} (Bobot: {{ number_format($gradeInfo['bobot'], 2) }})
        </td>
        <td style="width: 33%;">
          <strong>Predikat Penilaian:</strong> {{ $gradeInfo['predikat'] }}
        </td>
        <td style="width: 34%;">
          <strong>Lencana DBK:</strong> {{ $badgeInfo ? $badgeInfo['nama'] : 'Belum Memperoleh Lencana' }}
        </td>
      </tr>
    </table>
  </div>

  <!-- Tanda Tangan Pejabat (Wakil Dekan 1 & Kabid Kemahasiswaan) -->
  <table class="signature-table">
    <tr>
      <td>
        Mengetahui,<br>
        <strong>Wakil Dekan I Bidang Akademik</strong><br>
        {{ $user->fakultas ?? 'Fakultas' }} UIS
        <br><br><br><br><br>
        ( ............................................................ )<br>
        NIDN / NUPTK.
      </td>
      <td>
        Batam, {{ now()->translatedFormat('d F Y') }}<br>
        Mengesahkan,<br>
        <strong>Kepala Bidang Kemahasiswaan</strong><br>
        Universitas Ibnu Sina
        <br><br><br><br><br>
        ( ............................................................ )<br>
        NIDN / NUPTK.
      </td>
    </tr>
  </table>

</body>
</html>
