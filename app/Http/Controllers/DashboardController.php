<?php

namespace App\Http\Controllers;

use App\Models\Institusi;
use App\Models\Kejuaraan;
use App\Models\KtaSetting;
use App\Models\LogoProdi;
use App\Models\PrestasiBelmawa;
use App\Models\PrestasiMandiri;
use App\Models\Rekognisi;
use App\Models\Sertifikasi;
use App\Models\User;
use App\Models\SkpKegiatan;
use App\Services\SkpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user && $user->isWadek1()) {
            return $this->dashboardWadek1($request, $user);
        }

        $selectedTahun = $request->query('tahun', date('Y'));
        $selectedProdi = $request->query('prodi', 'all');
        $selectedJenis = $request->query('jenis', 'all');

        // Master List of Official Prodi at Universitas Ibnu Sina
        $defaultProdis = [
            'S1 - Teknik Informatika',
            'S1 - Sistem Informasi',
            'S1 - Teknik Industri',
            'S1 - Teknik Logistik',
            'S1 - Teknik Perkapalan',
            'S1 - Manajemen',
            'S1 - Akuntansi',
            'S1 - Kesehatan Masyarakat',
            'S1 - Kesehatan dan Keselamatan Kerja',
            'S1 - Kesehatan Lingkungan',
            'S2 - Magister Manajemen',
            'S2 - Magister Kesehatan Masyarakat',
        ];

        // Retrieve all records for memory-efficient mapping & filtering
        $allBelmawa   = PrestasiBelmawa::all();
        $allMandiri   = PrestasiMandiri::all();
        $allRekognisi = Rekognisi::all();
        $allSertifikasi = Sertifikasi::all();
        $allKejuaraan = Kejuaraan::all();

        // Collect available years
        $yearsFromDb = collect()
            ->concat($allBelmawa->pluck('tahun'))
            ->concat($allMandiri->pluck('tahun'))
            ->concat($allRekognisi->pluck('tahun'))
            ->concat($allSertifikasi->pluck('tahun'))
            ->concat($allKejuaraan->pluck('tahun'))
            ->filter()
            ->map(fn($y) => (int) $y)
            ->push((int) date('Y'))
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        $availableYears = $yearsFromDb;

        // Normalizer helper for robust prodi comparisons
        $normalizeProdi = function (?string $prodi): string {
            if (!$prodi) return '';
            $p = strtolower(trim($prodi));
            $p = preg_replace('/^(s1|s2|d3)[\s\-_]*/i', '', $p);
            $p = preg_replace('/[^a-z0-9]/', ' ', $p);
            return trim(preg_replace('/\s+/', ' ', $p));
        };

        $matchOfficialProdi = function (?string $inputProdi) use ($defaultProdis, $normalizeProdi): string {
            if (!$inputProdi) return 'S1 - Teknik Informatika';
            $norm = $normalizeProdi($inputProdi);
            foreach ($defaultProdis as $official) {
                if ($normalizeProdi($official) === $norm) {
                    return $official;
                }
            }
            return $inputProdi;
        };

        // Helper to extract prodi from item
        $getProdisFromItem = function ($item, $type) use ($matchOfficialProdi) {
            if ($type === 'belmawa') {
                return array_filter([$matchOfficialProdi($item->program_studi)]);
            }
            $prodis = [];
            if (!empty($item->data_mahasiswa) && is_array($item->data_mahasiswa)) {
                foreach ($item->data_mahasiswa as $mhs) {
                    if (!empty($mhs['prodi'])) {
                        $prodis[] = $matchOfficialProdi($mhs['prodi']);
                    }
                }
            }
            return array_unique($prodis);
        };

        // Helper to categorize Academic vs Non-Academic
        $getJenisFromItem = function ($item, $type) {
            if ($type === 'belmawa' || $type === 'sertifikasi' || $type === 'rekognisi') {
                return 'Akademik';
            }
            if ($type === 'mandiri') {
                $kat = strtolower($item->kategori ?? '');
                if (str_contains($kat, 'sains') || str_contains($kat, 'teknologi') || str_contains($kat, 'wirausaha') || str_contains($kat, 'ssi')) {
                    return 'Akademik';
                }
                return 'Non-Akademik';
            }
            return 'Non-Akademik';
        };

        // Helper to check if item belongs to current student (when role == mahasiswa)
        $isForCurrentStudent = function ($item, $type) {
            $user = auth()->user();
            if (!$user || $user->role !== 'mahasiswa') {
                return true;
            }
            $studentName = strtolower(trim($user->name));
            if ($type === 'belmawa') {
                return str_contains(strtolower($item->nama_mahasiswa ?? ''), $studentName);
            }
            if (!empty($item->data_mahasiswa) && is_array($item->data_mahasiswa)) {
                foreach ($item->data_mahasiswa as $mhs) {
                    if (!empty($mhs['nama']) && str_contains(strtolower($mhs['nama']), $studentName)) {
                        return true;
                    }
                }
            }
            return false;
        };

        // Standardized Dataset items
        $dataset = [];

        foreach ($allMandiri as $item) {
            if (!$isForCurrentStudent($item, 'mandiri')) continue;
            $prodis = $getProdisFromItem($item, 'mandiri');
            $jenis  = $getJenisFromItem($item, 'mandiri');
            $dataset[] = [
                'type'   => 'mandiri',
                'tahun'  => (int) ($item->tahun ?? date('Y')),
                'prodis' => !empty($prodis) ? $prodis : ['Teknik Informatika'],
                'jenis'  => $jenis,
                'model'  => $item,
            ];
        }

        foreach ($allBelmawa as $item) {
            if (!$isForCurrentStudent($item, 'belmawa')) continue;
            $prodis = $getProdisFromItem($item, 'belmawa');
            $jenis  = $getJenisFromItem($item, 'belmawa');
            $dataset[] = [
                'type'   => 'belmawa',
                'tahun'  => (int) ($item->tahun ?? date('Y')),
                'prodis' => !empty($prodis) ? $prodis : ['Teknik Informatika'],
                'jenis'  => $jenis,
                'model'  => $item,
            ];
        }

        foreach ($allRekognisi as $item) {
            if (!$isForCurrentStudent($item, 'rekognisi')) continue;
            $prodis = $getProdisFromItem($item, 'rekognisi');
            $jenis  = $getJenisFromItem($item, 'rekognisi');
            $dataset[] = [
                'type'   => 'rekognisi',
                'tahun'  => (int) ($item->tahun ?? date('Y')),
                'prodis' => !empty($prodis) ? $prodis : ['Teknik Informatika'],
                'jenis'  => $jenis,
                'model'  => $item,
            ];
        }

        foreach ($allSertifikasi as $item) {
            if (!$isForCurrentStudent($item, 'sertifikasi')) continue;
            $prodis = $getProdisFromItem($item, 'sertifikasi');
            $jenis  = $getJenisFromItem($item, 'sertifikasi');
            $dataset[] = [
                'type'   => 'sertifikasi',
                'tahun'  => (int) ($item->tahun ?? date('Y')),
                'prodis' => !empty($prodis) ? $prodis : ['Teknik Informatika'],
                'jenis'  => $jenis,
                'model'  => $item,
            ];
        }

        // Apply Active Filters to Dataset
        $filteredDataset = array_filter($dataset, function ($row) use ($selectedTahun, $selectedProdi, $selectedJenis, $normalizeProdi) {
            if ($selectedTahun !== 'all' && (int)$row['tahun'] !== (int)$selectedTahun) {
                return false;
            }
            if ($selectedProdi !== 'all') {
                $hasProdi = false;
                foreach ($row['prodis'] as $p) {
                    if ($normalizeProdi($p) === $normalizeProdi($selectedProdi)) {
                        $hasProdi = true;
                        break;
                    }
                }
                if (!$hasProdi) {
                    return false;
                }
            }
            if ($selectedJenis !== 'all' && strtolower($row['jenis']) !== strtolower($selectedJenis)) {
                return false;
            }
            return true;
        });

        // KTA Statistics & Data Mahasiswa
        $mahasiswaUsers = User::where('role', 'mahasiswa')->get();
        $totalMahasiswa = $mahasiswaUsers->count();

        // Mahasiswa yang sudah memiliki KTA (terdaftar dalam organisasi)
        $allMahasiswaBerKta = $mahasiswaUsers->filter(function ($u) {
            return !empty(trim((string)$u->organisasi));
        })->values();

        $totalKtaCards = 0;
        $ormawaDistribution = [];
        foreach ($allMahasiswaBerKta as $u) {
            $orgs = is_array($u->organisasi)
                ? $u->organisasi
                : array_filter(array_map('trim', explode(',', (string)$u->organisasi)));
            $totalKtaCards += count($orgs);
            foreach ($orgs as $org) {
                if ($org) {
                    $ormawaDistribution[$org] = ($ormawaDistribution[$org] ?? 0) + 1;
                }
            }
        }
        arsort($ormawaDistribution);

        // Filtered KTA Mahasiswa jika ada filter program studi
        $filteredMahasiswaBerKta = $allMahasiswaBerKta;
        if ($selectedProdi !== 'all') {
            $filteredMahasiswaBerKta = $allMahasiswaBerKta->filter(function ($u) use ($selectedProdi, $normalizeProdi) {
                return $normalizeProdi($u->program_studi) === $normalizeProdi($selectedProdi);
            })->values();
        }

        $currentUser = Auth::user();
        $userKtaCards = $currentUser ? LogoProdi::getKtaCardsForUser($currentUser) : [];
        $userHasKta = !empty(trim((string)($currentUser?->organisasi ?? '')));
        $userRealKtaCards = array_values(array_filter($userKtaCards, fn($c) => $c['id'] !== 'kta-default'));
        $ktaSetting = KtaSetting::getSetting();

        // 1. Statistics Cards
        $stats = [
            'mandiri'       => count(array_filter($filteredDataset, fn($r) => $r['type'] === 'mandiri')),
            'belmawa'       => count(array_filter($filteredDataset, fn($r) => $r['type'] === 'belmawa')),
            'rekognisi'     => count(array_filter($filteredDataset, fn($r) => $r['type'] === 'rekognisi')),
            'sertifikasi'   => count(array_filter($filteredDataset, fn($r) => $r['type'] === 'sertifikasi')),
            'kejuaraan'     => $allKejuaraan->count(),
            'institusi'     => Institusi::query()->count(),
            'users'         => User::query()->count(),
            'total_mhs'     => $totalMahasiswa,
            'kta_mhs'       => $allMahasiswaBerKta->count(),
            'kta_mhs_filtered' => $filteredMahasiswaBerKta->count(),
            'total_kta'     => $totalKtaCards,
            'user_has_kta'  => $userHasKta,
            'user_kta_count'=> count($userRealKtaCards) ?: count($userKtaCards),
        ];

        // 2. Chart per Program Studi
        $prodiCounts = array_fill_keys($defaultProdis, 0);
        foreach ($filteredDataset as $row) {
            foreach ($row['prodis'] as $p) {
                if (isset($prodiCounts[$p])) {
                    $prodiCounts[$p]++;
                } else {
                    $matched = false;
                    foreach ($defaultProdis as $official) {
                        if ($normalizeProdi($official) === $normalizeProdi($p)) {
                            $prodiCounts[$official]++;
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched) {
                        $prodiCounts[$p] = ($prodiCounts[$p] ?? 0) + 1;
                    }
                }
            }
        }
        $chartProdiLabels = array_keys($prodiCounts);
        $chartProdiSeries = array_values($prodiCounts);

        // 3. Chart Akademik vs Non-Akademik
        $akademikCount = count(array_filter($filteredDataset, fn($r) => $r['jenis'] === 'Akademik'));
        $nonAkademikCount = count(array_filter($filteredDataset, fn($r) => $r['jenis'] === 'Non-Akademik'));
        $chartJenisLabels = ['Akademik', 'Non-Akademik'];
        $chartJenisSeries = [$akademikCount, $nonAkademikCount];

        // 4. Chart Tren per Tahun (Use full timeline or filtered timeline)
        $timelineYears = array_reverse($availableYears);
        $chartTahunSeries = [];
        foreach ($timelineYears as $year) {
            $countForYear = count(array_filter($dataset, function ($row) use ($year, $selectedProdi, $selectedJenis, $normalizeProdi) {
                if ((int)$row['tahun'] !== (int)$year) return false;
                if ($selectedProdi !== 'all') {
                    $hasProdi = false;
                    foreach ($row['prodis'] as $p) {
                        if ($normalizeProdi($p) === $normalizeProdi($selectedProdi)) {
                            $hasProdi = true;
                            break;
                        }
                    }
                    if (!$hasProdi) return false;
                }
                if ($selectedJenis !== 'all' && strtolower($row['jenis']) !== strtolower($selectedJenis)) return false;
                return true;
            }));
            $chartTahunSeries[] = $countForYear;
        }

        // Latest Data
        $latestBelmawa      = $allBelmawa->filter(fn($item) => $isForCurrentStudent($item, 'belmawa'))->sortByDesc('created_at')->take(5);
        $latestMandiri      = $allMandiri->filter(fn($item) => $isForCurrentStudent($item, 'mandiri'))->sortByDesc('created_at')->take(5);
        $latestKejuaraan    = $allKejuaraan->sortByDesc('created_at')->take(5);
        $latestKtaMahasiswa = $filteredMahasiswaBerKta->sortByDesc('updated_at')->take(6)->values();

        return view('layouts.dashboard.index', compact(
            'stats',
            'latestBelmawa',
            'latestMandiri',
            'latestKejuaraan',
            'latestKtaMahasiswa',
            'userKtaCards',
            'userRealKtaCards',
            'ktaSetting',
            'ormawaDistribution',
            'availableYears',
            'defaultProdis',
            'selectedTahun',
            'selectedProdi',
            'selectedJenis',
            'chartProdiLabels',
            'chartProdiSeries',
            'chartJenisLabels',
            'chartJenisSeries',
            'timelineYears',
            'chartTahunSeries'
        ));
    }

    /**
     * Dashboard khusus Wakil Dekan 1 (Bidang Akademik Fakultas).
     */
    private function dashboardWadek1(Request $request, User $user)
    {
        SkpKegiatan::ensureSchemaCompatibility();

        $fakultas = $user->fakultas ?: 'Fakultas Sains dan Teknologi';

        // Base query kegiatan akademik khusus fakultas Wakil Dekan 1
        $baseQuery = SkpKegiatan::forUser($user);

        // Stats
        $pendingCount = (clone $baseQuery)->where('status', 'Pending')->count();
        $approvedCount = (clone $baseQuery)->where('status', 'Disetujui')->count();
        $rejectedCount = (clone $baseQuery)->where('status', 'Ditolak')->count();
        $totalPoin = (clone $baseQuery)->where('status', 'Disetujui')->sum('poin_skp');

        // Publikasi & Karya Ilmiah (Jurnal SINTA, Scopus, Buku ber-ISBN, Paten)
        $publikasiCount = (clone $baseQuery)
            ->where('status', 'Disetujui')
            ->where(function($q) {
                $q->where('kategori_id', 'rekognisi')
                  ->orWhere('jenis_kegiatan', 'LIKE', '%jurnal%')
                  ->orWhere('jenis_kegiatan', 'LIKE', '%isbn%')
                  ->orWhere('jenis_kegiatan', 'LIKE', '%paten%')
                  ->orWhere('nama_kegiatan', 'LIKE', '%jurnal%')
                  ->orWhere('nama_kegiatan', 'LIKE', '%buku%')
                  ->orWhere('nama_kegiatan', 'LIKE', '%paten%');
            })->count();

        // Antrean 6 pengajuan akademik terbaru yang menunggu verifikasi WD 1
        $pendingList = (clone $baseQuery)
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 5 kegiatan akademik yang baru disetujui
        $recentApproved = (clone $baseQuery)
            ->where('status', 'Disetujui')
            ->orderBy('verified_at', 'desc')
            ->take(5)
            ->get();

        // Distribusi Capaian SKP per Program Studi di Fakultas ini
        $prodis = (clone $baseQuery)->select('program_studi')
            ->whereNotNull('program_studi')
            ->distinct()
            ->pluck('program_studi');

        $prodiChartLabels = [];
        $prodiChartPoints = [];
        $prodiChartKegiatan = [];
        foreach ($prodis as $prodiName) {
            $prodiChartLabels[] = $prodiName;
            $prodiChartPoints[] = (clone $baseQuery)->where('program_studi', $prodiName)->where('status', 'Disetujui')->sum('poin_skp');
            $prodiChartKegiatan[] = (clone $baseQuery)->where('program_studi', $prodiName)->count();
        }

        // Top Mahasiswa Berprestasi Akademik di Fakultas ini
        $topMahasiswa = (clone $baseQuery)
            ->where('status', 'Disetujui')
            ->selectRaw('user_id, nama_mahasiswa, nim, program_studi, SUM(poin_skp) as total_poin, COUNT(*) as total_kegiatan')
            ->groupBy('user_id', 'nama_mahasiswa', 'nim', 'program_studi')
            ->orderByDesc('total_poin')
            ->take(5)
            ->get()
            ->map(function($item) {
                $eval = SkpService::getGradeAndPredicate($item->total_poin);
                $badge = SkpService::getDarmaBaktiKampusBadge($item->total_poin);
                $item->grade = $eval['grade'];
                $item->bobot = $eval['bobot'];
                $item->predikat = $eval['predikat'];
                $item->lencana = $badge['badge'];
                return $item;
            });

        return view('pages.dashboard.wadek1', compact(
            'user',
            'fakultas',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalPoin',
            'publikasiCount',
            'pendingList',
            'recentApproved',
            'prodiChartLabels',
            'prodiChartPoints',
            'prodiChartKegiatan',
            'topMahasiswa'
        ));
    }
}
