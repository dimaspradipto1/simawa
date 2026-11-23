<?php

namespace App\Http\Controllers;

use App\Models\SkpKegiatan;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\SkpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Throwable;

class SkpMahasiswaController extends Controller
{
    public function __construct(
        protected GoogleDriveService $driveService
    ) {
        SkpKegiatan::ensureSchemaCompatibility();
    }

    /**
     * Daftar kegiatan SKP (menyesuaikan role yang sedang login).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SkpKegiatan::with(['mahasiswa', 'verifikator'])->forUser($user);

        // Filter Sifat Kegiatan (Akademik / Non-Akademik)
        if ($request->filled('sifat')) {
            $query->where('sifat_kegiatan', $request->sifat);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        // Pencarian Nama / NIM / Kegiatan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('nama_mahasiswa', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('program_studi', 'like', "%{$search}%");
            });
        }

        $kegiatans = $query->latest()->paginate(15)->withQueryString();

        // Statistik Ringkas
        $statQuery = SkpKegiatan::forUser($user);
        $stats = [
            'total'     => (clone $statQuery)->count(),
            'pending'   => (clone $statQuery)->where('status', 'Pending')->count(),
            'approved'  => (clone $statQuery)->where('status', 'Disetujui')->count(),
            'rejected'  => (clone $statQuery)->where('status', 'Ditolak')->count(),
            'total_skp' => (clone $statQuery)->where('status', 'Disetujui')->sum('poin_skp'),
        ];

        $gradeInfo = SkpService::getGradeAndPredicate($stats['total_skp']);
        $badgeInfo = SkpService::getDarmaBaktiKampus($stats['total_skp']);

        $kategoris = SkpService::getKategoriList();

        return view('pages.skp.index', compact('kegiatans', 'stats', 'gradeInfo', 'badgeInfo', 'kategoris'));
    }

    /**
     * Form pengajuan kegiatan SKP baru.
     */
    public function create()
    {
        $user = Auth::user();
        $kategoris = SkpService::getKategoriList();
        $isDriveReady = $this->driveService->isConfigured();

        return view('pages.skp.create', compact('user', 'kategoris', 'isDriveReady'));
    }

    /**
     * Simpan pengajuan kegiatan SKP.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'kategori_id'        => ['required', 'string'],
            'nama_kegiatan'      => ['required', 'string', 'max:255'],
            'jenis_kegiatan'     => ['nullable', 'string', 'max:255'],
            'lingkup_tingkat'    => ['nullable', 'string', 'max:100'],
            'peringkat_juara'    => ['nullable', 'string', 'max:100'],
            'jabatan'            => ['nullable', 'string', 'max:100'],
            'sifat_kegiatan'     => ['required', 'in:Akademik,Non-Akademik'],
            'tanggal_kegiatan'   => ['nullable', 'date'],
            'file_bukti'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'link_bukti'         => ['nullable', 'url', 'max:255'],
            'keterangan'         => ['nullable', 'string', 'max:1000'],
            // Jika diajukan oleh admin atas nama mahasiswa lain:
            'nim'                => ['nullable', 'string', 'max:50'],
            'nama_mahasiswa'     => ['nullable', 'string', 'max:255'],
            'program_studi'      => ['nullable', 'string', 'max:255'],
            'fakultas'           => ['nullable', 'string', 'max:255'],
            'tahun_akademik'     => ['nullable', 'string', 'max:20'],
        ], [
            'kategori_id.required'   => 'Kategori kegiatan wajib dipilih.',
            'nama_kegiatan.required' => 'Nama kegiatan wajib diisi.',
            'sifat_kegiatan.required'=> 'Sifat kegiatan (Akademik / Non-Akademik) wajib ditentukan.',
            'file_bukti.mimes'       => 'File bukti harus berformat PDF, JPG, JPEG, atau PNG.',
            'file_bukti.max'         => 'Ukuran file bukti maksimal 10 MB.',
        ]);

        // Tentukan data identitas mahasiswa
        $mhsUser = $user->isMahasiswa() ? $user : null;
        if (!$mhsUser && !empty($validated['nim'])) {
            $mhsUser = User::where('nim', $validated['nim'])->first();
        }

        $validated['user_id'] = $mhsUser ? $mhsUser->id : $user->id;
        $validated['nama_mahasiswa'] = ($validated['nama_mahasiswa'] ?? null) ?: ($mhsUser?->name ?? $user->name);
        $validated['nim'] = ($validated['nim'] ?? null) ?: ($mhsUser?->nim ?? $user->nim);
        $validated['program_studi'] = ($validated['program_studi'] ?? null) ?: ($mhsUser?->program_studi ?? $user->program_studi ?? 'S1 - Teknik Informatika');
        $validated['fakultas'] = ($validated['fakultas'] ?? null) ?: ($mhsUser?->fakultas ?? $user->fakultas ?? 'Fakultas Sains dan Teknologi');
        $validated['tahun_akademik'] = ($validated['tahun_akademik'] ?? null) ?: date('Y') . '/' . (date('Y') + 1);

        // Auto-assign Target Verifikator Role:
        // Jika Akademik -> wadek1; Jika Non-Akademik -> kabid
        $validated['target_role'] = ($validated['sifat_kegiatan'] === 'Akademik') ? 'wadek1' : 'kabid';

        // Hitung Poin SKP berdasarkan tabel pedoman 2025
        $validated['poin_skp'] = SkpService::calculatePoints(
            $validated['kategori_id'],
            $validated['lingkup_tingkat'] ?? null,
            $validated['peringkat_juara'] ?? null,
            $validated['jabatan'] ?? null,
            $validated['jenis_kegiatan'] ?? null
        );

        // Upload Berkas Bukti (Google Drive atau Lokal Storage)
        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $upload = $this->driveService->upload(
                $file,
                'GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT',
                'skp_bukti',
                $validated['nama_mahasiswa']
            );
            $validated['file_bukti'] = $upload['url'];
            if (empty($validated['link_bukti']) && !empty($upload['url']) && str_starts_with($upload['url'], 'http')) {
                $validated['link_bukti'] = $upload['url'];
            }
        }

        $validated['status'] = 'Pending';

        $skp = SkpKegiatan::create($validated);

        Alert::success(
            'Pengajuan SKP Berhasil',
            "Kegiatan berhasil diajukan dengan estimasi {$skp->poin_skp} Poin SKP. Menunggu verifikasi dari " . ($skp->target_role === 'wadek1' ? 'Wakil Dekan 1' : 'Kabid Kemahasiswaan') . '.'
        );

        return redirect()->route('skp.index');
    }

    /**
     * Detail kegiatan SKP.
     */
    public function show(SkpKegiatan $skp)
    {
        $skp->load(['mahasiswa', 'verifikator']);
        $currentUser = Auth::user();

        // Tentukan apakah user punya hak verifikasi
        $canVerify = false;
        if ($skp->status === 'Pending') {
            if ($currentUser->isAdmin()) {
                $canVerify = true;
            } elseif ($currentUser->isWadek1() && $skp->sifat_kegiatan === 'Akademik') {
                $canVerify = empty($currentUser->fakultas) || $currentUser->fakultas === $skp->fakultas;
            } elseif ($currentUser->isKabid() && $skp->sifat_kegiatan === 'Non-Akademik') {
                $canVerify = true;
            }
        }

        return view('pages.skp.show', compact('skp', 'canVerify'));
    }

    /**
     * Form edit kegiatan SKP.
     */
    public function edit(SkpKegiatan $skp)
    {
        $user = Auth::user();
        if ($skp->status === 'Disetujui' && !$user->isAdmin()) {
            Alert::warning('Akses Ditolak', 'Kegiatan yang sudah disetujui tidak dapat diubah.');
            return redirect()->route('skp.index');
        }

        $kategoris = SkpService::getKategoriList();
        $isDriveReady = $this->driveService->isConfigured();

        return view('pages.skp.edit', compact('skp', 'kategoris', 'isDriveReady'));
    }

    /**
     * Perbarui pengajuan kegiatan SKP.
     */
    public function update(Request $request, SkpKegiatan $skp)
    {
        $user = Auth::user();
        if ($skp->status === 'Disetujui' && !$user->isAdmin()) {
            Alert::warning('Akses Ditolak', 'Kegiatan yang sudah disetujui tidak dapat diubah.');
            return redirect()->route('skp.index');
        }

        $validated = $request->validate([
            'kategori_id'        => ['required', 'string'],
            'nama_kegiatan'      => ['required', 'string', 'max:255'],
            'jenis_kegiatan'     => ['nullable', 'string', 'max:255'],
            'lingkup_tingkat'    => ['nullable', 'string', 'max:100'],
            'peringkat_juara'    => ['nullable', 'string', 'max:100'],
            'jabatan'            => ['nullable', 'string', 'max:100'],
            'sifat_kegiatan'     => ['required', 'in:Akademik,Non-Akademik'],
            'tanggal_kegiatan'   => ['nullable', 'date'],
            'file_bukti'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'link_bukti'         => ['nullable', 'url', 'max:255'],
            'keterangan'         => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['target_role'] = ($validated['sifat_kegiatan'] === 'Akademik') ? 'wadek1' : 'kabid';

        $validated['poin_skp'] = SkpService::calculatePoints(
            $validated['kategori_id'],
            $validated['lingkup_tingkat'] ?? null,
            $validated['peringkat_juara'] ?? null,
            $validated['jabatan'] ?? null,
            $validated['jenis_kegiatan'] ?? null
        );

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $upload = $this->driveService->upload(
                $file,
                'GOOGLE_DRIVE_UPLOAD_FILE_SERTIFIKAT',
                'skp_bukti',
                $skp->nama_mahasiswa
            );
            $validated['file_bukti'] = $upload['url'];
            if (empty($validated['link_bukti']) && !empty($upload['url']) && str_starts_with($upload['url'], 'http')) {
                $validated['link_bukti'] = $upload['url'];
            }
        }

        // Kembalikan ke Pending jika sebelumnya Ditolak
        if ($skp->status === 'Ditolak') {
            $validated['status'] = 'Pending';
            $validated['catatan_verifikator'] = null;
        }

        $skp->update($validated);

        Alert::success('Berhasil Diperbarui', 'Data kegiatan SKP berhasil diperbarui.');
        return redirect()->route('skp.index');
    }

    /**
     * Hapus kegiatan SKP.
     */
    public function destroy(SkpKegiatan $skp)
    {
        $user = Auth::user();
        if ($skp->user_id !== $user->id && !$user->isAdmin()) {
            Alert::error('Akses Ditolak', 'Anda tidak memiliki hak untuk menghapus pengajuan ini.');
            return redirect()->route('skp.index');
        }

        $skp->delete();

        Alert::success('Berhasil Dihapus', 'Data pengajuan SKP telah dihapus.');
        return redirect()->route('skp.index');
    }

    /**
     * Proses Verifikasi (Setujui / Tolak) oleh Wakil Dekan 1 atau Kabid Kemahasiswaan.
     */
    public function verify(Request $request, SkpKegiatan $skp)
    {
        $user = Auth::user();

        // Validasi hak verifikasi
        if (!$user->isAdmin()) {
            if ($skp->sifat_kegiatan === 'Akademik' && !$user->isWadek1()) {
                Alert::error('Akses Ditolak', 'Kegiatan akademik hanya dapat diverifikasi oleh Wakil Dekan 1.');
                return redirect()->back();
            }
            if ($skp->sifat_kegiatan === 'Non-Akademik' && !$user->isKabid()) {
                Alert::error('Akses Ditolak', 'Kegiatan non-akademik hanya dapat diverifikasi oleh Kabid Kemahasiswaan.');
                return redirect()->back();
            }
        }

        $request->validate([
            'status'              => ['required', 'in:Disetujui,Ditolak'],
            'catatan_verifikator' => ['nullable', 'string', 'max:500'],
            'poin_skp'            => ['nullable', 'integer', 'min:0'],
        ]);

        $updateData = [
            'status'              => $request->status,
            'catatan_verifikator' => $request->catatan_verifikator,
            'verified_by'         => $user->id,
            'verified_at'         => now(),
        ];

        // Jika verifikator menyesuaikan perolehan poin secara manual
        if ($request->filled('poin_skp')) {
            $updateData['poin_skp'] = $request->poin_skp;
        }

        $skp->update($updateData);

        if ($request->status === 'Disetujui') {
            Alert::success('Kegiatan Disetujui', "Kegiatan '{$skp->nama_kegiatan}' disetujui dengan {$skp->poin_skp} Poin SKP.");
        } else {
            Alert::warning('Kegiatan Ditolak', "Kegiatan '{$skp->nama_kegiatan}' ditolak.");
        }

        return redirect()->back();
    }

    /**
     * Halaman Buku / Transkrip Portofolio SKP Mahasiswa.
     */
    public function transkrip(Request $request, ?User $user = null)
    {
        $currentUser = Auth::user();
        $targetUser = $user ?: ($currentUser->isMahasiswa() ? $currentUser : null);

        if (!$targetUser) {
            // Jika admin/WD1/Kabid membuka transkrip tanpa parameter user, tampilkan daftar mahasiswa
            $mahasiswaList = User::where('role', 'mahasiswa')
                ->when($currentUser->isWadek1() && !empty($currentUser->fakultas), function ($q) use ($currentUser) {
                    $q->where('fakultas', $currentUser->fakultas);
                })
                ->paginate(20);

            return view('pages.skp.transkrip-list', compact('mahasiswaList'));
        }

        $approvedKegiatans = SkpKegiatan::where('user_id', $targetUser->id)
            ->where('status', 'Disetujui')
            ->orderBy('tanggal_kegiatan', 'asc')
            ->get();

        $totalPoin = $approvedKegiatans->sum('poin_skp');
        $gradeInfo = SkpService::getGradeAndPredicate($totalPoin);
        $badgeInfo = SkpService::getDarmaBaktiKampus($totalPoin);

        $akademikPoin = $approvedKegiatans->where('sifat_kegiatan', 'Akademik')->sum('poin_skp');
        $nonAkademikPoin = $approvedKegiatans->where('sifat_kegiatan', 'Non-Akademik')->sum('poin_skp');

        return view('pages.skp.transkrip', compact(
            'targetUser',
            'approvedKegiatans',
            'totalPoin',
            'gradeInfo',
            'badgeInfo',
            'akademikPoin',
            'nonAkademikPoin'
        ));
    }

    /**
     * Cetak Transkrip SKP Mahasiswa (Versi Cetak / PDF).
     */
    public function cetakPdf(User $user)
    {
        $approvedKegiatans = SkpKegiatan::where('user_id', $user->id)
            ->where('status', 'Disetujui')
            ->orderBy('tanggal_kegiatan', 'asc')
            ->get();

        $totalPoin = $approvedKegiatans->sum('poin_skp');
        $gradeInfo = SkpService::getGradeAndPredicate($totalPoin);
        $badgeInfo = SkpService::getDarmaBaktiKampus($totalPoin);

        return view('pages.skp.pdf', compact('user', 'approvedKegiatans', 'totalPoin', 'gradeInfo', 'badgeInfo'));
    }

    /**
     * AJAX Endpoint: Hitung Live Preview Poin saat memilih kategori di formulir.
     */
    public function calculatePreview(Request $request)
    {
        $points = SkpService::calculatePoints(
            $request->input('kategori_id', ''),
            $request->input('lingkup_tingkat'),
            $request->input('peringkat_juara'),
            $request->input('jabatan'),
            $request->input('jenis_kegiatan')
        );

        return response()->json([
            'success' => true,
            'points'  => $points,
        ]);
    }
}
