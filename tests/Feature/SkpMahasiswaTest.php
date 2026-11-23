<?php

namespace Tests\Feature;

use App\Models\SkpKegiatan;
use App\Models\User;
use App\Services\SkpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkpMahasiswaTest extends TestCase
{
    public function test_skp_service_calculates_points_correctly()
    {
        // Perlombaan Juara 1 Nasional = 90
        $p1 = SkpService::calculatePoints('perlombaan', 'Nasional', 'Juara I');
        $this->assertEquals(90, $p1);

        // Perlombaan Juara 1 Internasional = 100
        $p2 = SkpService::calculatePoints('perlombaan', 'Internasional', 'Juara I');
        $this->assertEquals(100, $p2);

        // Pengurus Ormawa Ketua = 50
        $p3 = SkpService::calculatePoints('pengurus_ormawa', null, null, 'Ketua');
        $this->assertEquals(50, $p3);

        // Kegiatan Wajib PKKMB = 7
        $p4 = SkpService::calculatePoints('kegiatan_wajib', null, null, null, 'PKKMB');
        $this->assertEquals(7, $p4);

        // Kegiatan Wajib Wirausaha = 10
        $p5 = SkpService::calculatePoints('kegiatan_wajib', null, null, null, 'Kewirausahaan');
        $this->assertEquals(10, $p5);

        // Rekognisi Jurnal = 30
        $p6 = SkpService::calculatePoints('rekognisi', null, null, null, 'Penulis Jurnal SINTA 1-3');
        $this->assertEquals(30, $p6);
    }

    public function test_grade_and_predicate_conversion()
    {
        $g1 = SkpService::getGradeAndPredicate(95);
        $this->assertEquals('A', $g1['grade']);
        $this->assertEquals(4.00, $g1['bobot']);
        $this->assertEquals('Sangat Baik (SB)', $g1['predikat']);

        $g2 = SkpService::getGradeAndPredicate(78);
        $this->assertEquals('B', $g2['grade']);

        $g3 = SkpService::getGradeAndPredicate(40);
        $this->assertEquals('E', $g3['grade']);
    }

    public function test_darma_bakti_kampus_badge()
    {
        // > 300 = Emas
        $b1 = SkpService::getDarmaBaktiKampus(350);
        $this->assertEquals('Emas', $b1['level']);

        // 201-300 = Perunggu
        $b2 = SkpService::getDarmaBaktiKampus(250);
        $this->assertEquals('Perunggu', $b2['level']);

        // 101-200 = Perak
        $b3 = SkpService::getDarmaBaktiKampus(150);
        $this->assertEquals('Perak', $b3['level']);

        // <= 100 = null
        $b4 = SkpService::getDarmaBaktiKampus(90);
        $this->assertNull($b4);
    }

    public function test_student_can_submit_skp_and_auto_routes_verifikator()
    {
        $student = User::firstOrCreate(
            ['email' => 'mhs_skp_test@uis.ac.id'],
            [
                'name' => 'Mahasiswa Test SKP',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
                'nim' => '210101999',
                'fakultas' => 'Fakultas Sains dan Teknologi',
                'program_studi' => 'S1 - Teknik Informatika',
            ]
        );

        // 1. Submit Kegiatan Akademik -> target_role should be wadek1
        $response = $this->actingAs($student)->post(route('skp.store'), [
            'kategori_id' => 'pengembangan_diri',
            'nama_kegiatan' => 'Seminar Nasional AI UIS',
            'jenis_kegiatan' => 'Seminar',
            'lingkup_tingkat' => 'Nasional',
            'sifat_kegiatan' => 'Akademik',
            'tanggal_kegiatan' => '2026-11-20',
        ]);

        $response->assertRedirect(route('skp.index'));

        $record = SkpKegiatan::where('nama_kegiatan', 'Seminar Nasional AI UIS')->first();
        $this->assertNotNull($record);
        $this->assertEquals('Akademik', $record->sifat_kegiatan);
        $this->assertEquals('wadek1', $record->target_role);
        $this->assertEquals('Pending', $record->status);
        $this->assertEquals(13, $record->poin_skp);

        // 2. Submit Kegiatan Non-Akademik -> target_role should be kabid
        $resp2 = $this->actingAs($student)->post(route('skp.store'), [
            'kategori_id' => 'pengurus_ormawa',
            'nama_kegiatan' => 'Pengurus HMTI Periode 2025/2026',
            'jenis_kegiatan' => 'HIMA (Himpunan Mahasiswa)',
            'jabatan' => 'Ketua',
            'sifat_kegiatan' => 'Non-Akademik',
            'tanggal_kegiatan' => '2026-11-01',
        ]);

        $resp2->assertRedirect(route('skp.index'));

        $rec2 = SkpKegiatan::where('nama_kegiatan', 'Pengurus HMTI Periode 2025/2026')->first();
        $this->assertNotNull($rec2);
        $this->assertEquals('Non-Akademik', $rec2->sifat_kegiatan);
        $this->assertEquals('kabid', $rec2->target_role);
        $this->assertEquals(50, $rec2->poin_skp);

        // Clean up
        $record->delete();
        $rec2->delete();
    }

    public function test_wadek1_and_kabid_can_verify_submissions()
    {
        $student = User::firstOrCreate(
            ['email' => 'mhs_skp_test@uis.ac.id'],
            [
                'name' => 'Mahasiswa Test SKP',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
                'nim' => '210101999',
            ]
        );

        $wadek1 = User::firstOrCreate(
            ['email' => 'wadek1_test@uis.ac.id'],
            [
                'name' => 'Dr. Wadek 1 FST',
                'password' => bcrypt('password'),
                'role' => 'wadek1',
                'fakultas' => 'Fakultas Sains dan Teknologi',
            ]
        );

        $kabid = User::firstOrCreate(
            ['email' => 'kabid_test@uis.ac.id'],
            [
                'name' => 'Kabid Kemahasiswaan UIS',
                'password' => bcrypt('password'),
                'role' => 'kabid',
            ]
        );

        $akademik = SkpKegiatan::create([
            'user_id' => $student->id,
            'nama_mahasiswa' => $student->name,
            'nim' => $student->nim,
            'kategori_id' => 'pengembangan_diri',
            'nama_kegiatan' => 'Workshop Penulisan Karya Ilmiah',
            'sifat_kegiatan' => 'Akademik',
            'target_role' => 'wadek1',
            'poin_skp' => 20,
            'status' => 'Pending',
        ]);

        // Wadek 1 verifies
        $resp = $this->actingAs($wadek1)->post(route('skp.verify', $akademik->id), [
            'status' => 'Disetujui',
            'poin_skp' => 20,
        ]);
        $resp->assertRedirect();

        $akademik->refresh();
        $this->assertEquals('Disetujui', $akademik->status);
        $this->assertEquals($wadek1->id, $akademik->verified_by);

        // Clean up
        $akademik->delete();
    }

    public function test_transkrip_and_live_preview_api()
    {
        $student = User::firstOrCreate(
            ['email' => 'mhs_skp_test@uis.ac.id'],
            [
                'name' => 'Mahasiswa Test SKP',
                'password' => bcrypt('password'),
                'role' => 'mahasiswa',
                'nim' => '210101999',
            ]
        );

        $res = $this->actingAs($student)->get(route('skp.calculate-preview', [
            'kategori_id' => 'perlombaan',
            'lingkup_tingkat' => 'Nasional',
            'peringkat_juara' => 'Juara I',
        ]));

        $res->assertStatus(200);
        $res->assertJson([
            'success' => true,
            'points' => 90,
        ]);

        $transkripRes = $this->actingAs($student)->get(route('skp.transkrip'));
        $transkripRes->assertStatus(200);
    }
}
