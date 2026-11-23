@extends('layouts.dashboard.template')

@section('content')
<div class="pagetitle">
  <h1>Buku Transkrip SKP Mahasiswa</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('skp.index') }}">SKP Mahasiswa</a></li>
      <li class="breadcrumb-item active">Pilih Mahasiswa</li>
    </ol>
  </nav>
  <p class="text-muted small">Pilih mahasiswa untuk melihat portofolio dan cetak transkrip Satuan Kredit Poin (SKP).</p>
</div>

<section class="section">
  <div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
          <thead class="table-light">
            <tr>
              <th>No</th>
              <th>Nama Mahasiswa</th>
              <th>NIM</th>
              <th>Program Studi</th>
              <th>Fakultas</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($mahasiswaList as $index => $mhs)
              <tr>
                <td>{{ $mahasiswaList->firstItem() + $index }}</td>
                <td>
                  <span class="fw-bold text-dark d-block">{{ $mhs->name }}</span>
                  <small class="text-muted">{{ $mhs->email }}</small>
                </td>
                <td><span class="badge bg-light text-dark border">{{ $mhs->nim ?? '-' }}</span></td>
                <td>{{ $mhs->program_studi ?? '-' }}</td>
                <td>{{ $mhs->fakultas ?? '-' }}</td>
                <td class="text-end">
                  <a href="{{ route('skp.transkrip', $mhs->id) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">
                    <i class="bi bi-file-earmark-person me-1"></i> Buka Transkrip
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">Tidak ada data mahasiswa.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">Menampilkan {{ $mahasiswaList->firstItem() ?? 0 }} - {{ $mahasiswaList->lastItem() ?? 0 }} dari {{ $mahasiswaList->total() }} mahasiswa</small>
        {{ $mahasiswaList->links() }}
      </div>
    </div>
  </div>
</section>
@endsection
