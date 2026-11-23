<?php

namespace App\Services;

class SkpService
{
    /**
     * Daftar Kategori Kegiatan SKP UIS 2025.
     */
    public static function getKategoriList(): array
    {
        return [
            'perlombaan' => [
                'nama'           => 'Pertandingan / Perlombaan',
                'sifat'          => 'Non-Akademik', // default, bisa dipilih Akademik/Non-Akademik
                'target_role'    => 'kabid',
                'has_tingkat'    => true,
                'has_juara'      => true,
                'has_jabatan'    => false,
                'has_subtipe'    => false,
                'deskripsi'      => 'Kejuaraan, perlombaan, ajang, atau kompetisi utusan/membawa nama kampus.',
            ],
            'pengembangan_diri' => [
                'nama'           => 'Pengembangan Diri (Seminar / Pelatihan / Workshop)',
                'sifat'          => 'Akademik',
                'target_role'    => 'wadek1',
                'has_tingkat'    => true,
                'has_juara'      => false,
                'has_jabatan'    => false,
                'has_subtipe'    => true,
                'subtipe_options'=> ['Seminar', 'Pelatihan', 'Workshop'],
                'deskripsi'      => 'Kegiatan pendukung akademik (maksimal dua kali per item).',
            ],
            'pelaksana_luring' => [
                'nama'           => 'Pelaksana Kegiatan (Kepanitiaan Luring)',
                'sifat'          => 'Non-Akademik',
                'target_role'    => 'kabid',
                'has_tingkat'    => true,
                'has_juara'      => false,
                'has_jabatan'    => true,
                'has_subtipe'    => false,
                'deskripsi'      => 'Kepanitiaan kegiatan yang dilaksanakan secara tatap muka (luring / offline).',
            ],
            'pelaksana_daring' => [
                'nama'           => 'Pelaksana Kegiatan (Kepanitiaan Daring)',
                'sifat'          => 'Non-Akademik',
                'target_role'    => 'kabid',
                'has_tingkat'    => true,
                'has_juara'      => false,
                'has_jabatan'    => true,
                'has_subtipe'    => false,
                'deskripsi'      => 'Kepanitiaan kegiatan yang dilaksanakan secara daring / online.',
            ],
            'pengurus_ormawa' => [
                'nama'           => 'Pengurus Organisasi Internal Kampus',
                'sifat'          => 'Non-Akademik',
                'target_role'    => 'kabid',
                'has_tingkat'    => false,
                'has_juara'      => false,
                'has_jabatan'    => true,
                'has_subtipe'    => true,
                'subtipe_options'=> ['BEM Universitas', 'DPM Universitas', 'BEM Fakultas', 'DPM Fakultas', 'HIMA (Himpunan Mahasiswa)', 'UKM (Unit Kegiatan Mahasiswa)'],
                'deskripsi'      => 'Pengurus ormawa berdasarkan SK resmi (satu kali penilaian selama kuliah dengan SKP tertinggi).',
            ],
            'relawan' => [
                'nama'           => 'Relawan (Bakti Sosial / Musibah)',
                'sifat'          => 'Non-Akademik',
                'target_role'    => 'kabid',
                'has_tingkat'    => true,
                'has_juara'      => false,
                'has_jabatan'    => true,
                'has_subtipe'    => false,
                'deskripsi'      => 'Relawan musibah / aksi kemanusiaan utusan kampus dilengkapi surat tugas.',
            ],
            'kegiatan_wajib' => [
                'nama'           => 'Kegiatan Wajib Kampus',
                'sifat'          => 'Non-Akademik',
                'target_role'    => 'kabid',
                'has_tingkat'    => false,
                'has_juara'      => false,
                'has_jabatan'    => false,
                'has_subtipe'    => true,
                'subtipe_options'=> ['PKKMB', 'Bina Mental (ESQ)', 'TOEFL', 'Kewirausahaan'],
                'deskripsi'      => 'Kegiatan pembinaan wajib mahasiswa UIS dan kegiatan tambahan sesuai kebijakan kampus.',
            ],
            'rekognisi' => [
                'nama'           => 'Rekognisi & Publikasi Ilmiah',
                'sifat'          => 'Akademik',
                'target_role'    => 'wadek1',
                'has_tingkat'    => true,
                'has_juara'      => false,
                'has_jabatan'    => false,
                'has_subtipe'    => true,
                'subtipe_options'=> [
                    'Juri / Pelatih / Wasit (Sertifikat Kompetensi)',
                    'Pemakalah Seminar / Konferensi Ilmiah',
                    'Pembicara / Narasumber Pelatihan/Seminar',
                    'Pendaftaran Paten / Paten Sederhana',
                    'Penulis Buku Ber-ISBN (bersama dosen UIS)',
                    'Penulis Jurnal SINTA 1-3 / Internasional Bereputasi',
                    'Peserta Pameran Seni / Produk Kampus',
                    'Karya Cipta Lagu Dipublikasikan (HKI)',
                    'Karya Cipta Seni Tari Dipentaskan (HKI)',
                ],
                'deskripsi'      => 'Rekognisi akademik, karya cipta HKI, publikasi jurnal, dan narasumber ilmiah.',
            ],
        ];
    }

    /**
     * Hitung Poin SKP berdasarkan pedoman UIS 2025.
     */
    public static function calculatePoints(
        string $kategori,
        ?string $tingkat = null,
        ?string $juara = null,
        ?string $jabatan = null,
        ?string $subtipe = null
    ): int {
        $tingkat = ucfirst(strtolower(trim((string)$tingkat)));
        if (in_array($tingkat, ['Kota', 'Kampus', 'Kota (kampus)', 'Kota/kampus', 'Lokal'])) {
            $tingkat = 'Kota';
        }

        switch ($kategori) {
            case 'perlombaan':
                $table = [
                    'Juara I'     => ['Kota' => 25, 'Provinsi' => 87, 'Nasional' => 90, 'Internasional' => 100],
                    'Juara II'    => ['Kota' => 20, 'Provinsi' => 86, 'Nasional' => 89, 'Internasional' => 95],
                    'Juara III'   => ['Kota' => 15, 'Provinsi' => 85, 'Nasional' => 88, 'Internasional' => 90],
                    'Harapan-1'   => ['Kota' => 10, 'Provinsi' => 40, 'Nasional' => 55, 'Internasional' => 70],
                    'Harapan-2'   => ['Kota' => 7,  'Provinsi' => 35, 'Nasional' => 50, 'Internasional' => 65],
                    'Harapan-3'   => ['Kota' => 5,  'Provinsi' => 30, 'Nasional' => 45, 'Internasional' => 60],
                    'Non Juara'   => ['Kota' => 0,  'Provinsi' => 5,  'Nasional' => 7,  'Internasional' => 10],
                    'Peserta'     => ['Kota' => 0,  'Provinsi' => 5,  'Nasional' => 7,  'Internasional' => 10],
                ];
                return $table[$juara][$tingkat] ?? ($table[$juara]['Nasional'] ?? 10);

            case 'pengembangan_diri':
                $sub = strtolower((string)$subtipe);
                if (str_contains($sub, 'seminar')) {
                    $map = ['Kota' => 5, 'Provinsi' => 12, 'Nasional' => 13, 'Internasional' => 15];
                } elseif (str_contains($sub, 'pelatihan')) {
                    $map = ['Kota' => 5, 'Provinsi' => 13, 'Nasional' => 17, 'Internasional' => 20];
                } elseif (str_contains($sub, 'workshop')) {
                    $map = ['Kota' => 5, 'Provinsi' => 15, 'Nasional' => 20, 'Internasional' => 25];
                } else {
                    $map = ['Kota' => 5, 'Provinsi' => 12, 'Nasional' => 15, 'Internasional' => 20];
                }
                return $map[$tingkat] ?? 5;

            case 'pelaksana_luring':
                $table = [
                    'Ketua'       => ['Kota' => 10, 'Provinsi' => 30, 'Nasional' => 40, 'Internasional' => 50],
                    'Wakil'       => ['Kota' => 8,  'Provinsi' => 25, 'Nasional' => 35, 'Internasional' => 45],
                    'Sekretaris'  => ['Kota' => 7,  'Provinsi' => 20, 'Nasional' => 30, 'Internasional' => 40],
                    'Bendahara'   => ['Kota' => 7,  'Provinsi' => 20, 'Nasional' => 30, 'Internasional' => 40],
                    'Koordinator' => ['Kota' => 6,  'Provinsi' => 15, 'Nasional' => 25, 'Internasional' => 35],
                    'Anggota'     => ['Kota' => 5,  'Provinsi' => 10, 'Nasional' => 20, 'Internasional' => 30],
                ];
                return $table[$jabatan][$tingkat] ?? ($table['Anggota'][$tingkat] ?? 5);

            case 'pelaksana_daring':
                $table = [
                    'Ketua'       => ['Kota' => 7,  'Provinsi' => 10, 'Nasional' => 30, 'Internasional' => 40],
                    'Wakil'       => ['Kota' => 6,  'Provinsi' => 8,  'Nasional' => 25, 'Internasional' => 35],
                    'Sekretaris'  => ['Kota' => 5,  'Provinsi' => 7,  'Nasional' => 20, 'Internasional' => 30],
                    'Bendahara'   => ['Kota' => 5,  'Provinsi' => 7,  'Nasional' => 20, 'Internasional' => 30],
                    'Koordinator' => ['Kota' => 5,  'Provinsi' => 6,  'Nasional' => 15, 'Internasional' => 25],
                    'Anggota'     => ['Kota' => 4,  'Provinsi' => 5,  'Nasional' => 10, 'Internasional' => 20],
                ];
                return $table[$jabatan][$tingkat] ?? ($table['Anggota'][$tingkat] ?? 4);

            case 'pengurus_ormawa':
                $map = [
                    'Ketua'                  => 50,
                    'Wakil'                  => 40,
                    'Sekretaris'             => 30,
                    'Bendahara'              => 30,
                    'Sekretaris / Bendahara' => 30,
                    'Koordinator'            => 20,
                    'Anggota'                => 15,
                ];
                return $map[$jabatan] ?? 15;

            case 'relawan':
                $table = [
                    'Ketua'   => ['Kota' => 7, 'Provinsi' => 10, 'Nasional' => 30, 'Internasional' => 40],
                    'Anggota' => ['Kota' => 5, 'Provinsi' => 7,  'Nasional' => 20, 'Internasional' => 30],
                ];
                $jab = (str_contains(strtolower((string)$jabatan), 'ketua')) ? 'Ketua' : 'Anggota';
                return $table[$jab][$tingkat] ?? 5;

            case 'kegiatan_wajib':
                $sub = strtolower((string)$subtipe);
                if (str_contains($sub, 'wirausaha')) {
                    return 10;
                }
                return 7; // PKKMB, Bina Mental (ESQ), TOEFL = 7

            case 'rekognisi':
                $sub = strtolower((string)$subtipe);
                if (str_contains($sub, 'paten')) {
                    return 15;
                }
                if (str_contains($sub, 'buku')) {
                    return 25;
                }
                if (str_contains($sub, 'jurnal')) {
                    return 30;
                }
                if (str_contains($sub, 'lagu')) {
                    return 25;
                }
                if (str_contains($sub, 'tari')) {
                    return 30;
                }

                // Juri / Pemakalah / Pembicara / Peserta Pameran (bertingkat)
                $map = ['Provinsi' => 10, 'Nasional' => 15, 'Internasional' => 20];
                return $map[$tingkat] ?? 10;

            default:
                return 5;
        }
    }

    /**
     * Konversi Total Poin SKP ke Nilai Huruf, Bobot SKS, dan Predikat Kata (Pedoman UIS 2025).
     */
    public static function getGradeAndPredicate(float|int $totalPoints): array
    {
        if ($totalPoints >= 90) {
            return ['grade' => 'A',  'bobot' => 4.00, 'predikat' => 'Sangat Baik (SB)', 'badge' => 'bg-success'];
        }
        if ($totalPoints >= 85) {
            return ['grade' => 'A-', 'bobot' => 3.70, 'predikat' => 'Sangat Baik (SB)', 'badge' => 'bg-success'];
        }
        if ($totalPoints >= 80) {
            return ['grade' => 'B+', 'bobot' => 3.30, 'predikat' => 'Baik (B)',        'badge' => 'bg-primary'];
        }
        if ($totalPoints >= 75) {
            return ['grade' => 'B',  'bobot' => 3.00, 'predikat' => 'Baik (B)',        'badge' => 'bg-primary'];
        }
        if ($totalPoints >= 70) {
            return ['grade' => 'B-', 'bobot' => 2.70, 'predikat' => 'Baik (B)',        'badge' => 'bg-info text-dark'];
        }
        if ($totalPoints >= 65) {
            return ['grade' => 'C+', 'bobot' => 2.30, 'predikat' => 'Cukup (C)',       'badge' => 'bg-warning text-dark'];
        }
        if ($totalPoints >= 60) {
            return ['grade' => 'C',  'bobot' => 2.00, 'predikat' => 'Cukup (C)',       'badge' => 'bg-warning text-dark'];
        }
        if ($totalPoints >= 55) {
            return ['grade' => 'C-', 'bobot' => 1.70, 'predikat' => 'Kurang (K)',      'badge' => 'bg-secondary'];
        }
        if ($totalPoints >= 50) {
            return ['grade' => 'D',  'bobot' => 1.00, 'predikat' => 'Kurang (K)',      'badge' => 'bg-danger'];
        }
        return ['grade' => 'E',  'bobot' => 0.00, 'predikat' => 'Sangat Kurang (SK)', 'badge' => 'bg-dark'];
    }

    /**
     * Hitung Penghargaan Lencana Darma Bakti Kampus (DBK) UIS.
     */
    public static function getDarmaBaktiKampus(float|int $totalPoints): ?array
    {
        if ($totalPoints > 300) {
            return [
                'nama'   => 'Lencana Emas Darma Bakti Kampus (DBK)',
                'level'  => 'Emas',
                'warna'  => '#f59e0b',
                'icon'   => 'bi-award-fill',
                'badge'  => 'bg-warning text-dark border border-warning',
            ];
        }
        if ($totalPoints >= 201) {
            return [
                'nama'   => 'Lencana Perunggu Darma Bakti Kampus (DBK)',
                'level'  => 'Perunggu',
                'warna'  => '#b45309',
                'icon'   => 'bi-shield-shaded',
                'badge'  => 'bg-danger text-white',
            ];
        }
        if ($totalPoints >= 101) {
            return [
                'nama'   => 'Lencana Perak Darma Bakti Kampus (DBK)',
                'level'  => 'Perak',
                'warna'  => '#94a3b8',
                'icon'   => 'bi-shield-check',
                'badge'  => 'bg-secondary text-white',
            ];
        }
        return null;
    }
}
