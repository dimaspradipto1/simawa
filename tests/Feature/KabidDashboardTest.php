<?php

namespace Tests\Feature;

use App\Models\SkpKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KabidDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_kabid_dashboard_renders_custom_view_with_university_data(): void
    {
        $kabid = User::factory()->create([
            'name'  => 'Dr. H. Kemahasiswaan UIS, M.Pd.',
            'email' => 'kabid.univ@uis.ac.id',
            'role'  => 'kabid',
        ]);

        $mhs = User::factory()->create([
            'name'          => 'Ahmad Organisasi',
            'email'         => 'ahmad.org@student.uis.ac.id',
            'role'          => 'mahasiswa',
            'nim'           => '201055201099',
            'program_studi' => 'S1 - Manajemen',
            'fakultas'      => 'Fakultas Ekonomi dan Bisnis',
            'organisasi'    => 'BEM UIS, HIMA Manajemen',
            'jabatan'       => 'Ketua Umum (BEM UIS)',
        ]);

        SkpKegiatan::create([
            'user_id'          => $mhs->id,
            'nama_mahasiswa'   => $mhs->name,
            'nim'              => $mhs->nim,
            'program_studi'    => $mhs->program_studi,
            'fakultas'         => $mhs->fakultas,
            'kategori_id'      => 'ormawa',
            'jenis_kegiatan'   => 'Kepengurusan Ormawa',
            'nama_kegiatan'    => 'Ketua Umum BEM Universitas Ibnu Sina Periode 2025/2026',
            'jabatan'          => 'Ketua',
            'sifat_kegiatan'   => 'Non-Akademik',
            'target_role'      => 'kabid',
            'poin_skp'         => 50,
            'status'           => 'Pending',
            'tanggal_kegiatan' => '2026-11-21',
        ]);

        $this->actingAs($kabid);

        $response = $this->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.dashboard.kabid');
        $response->assertSee('Dashboard Kemahasiswaan');
        $response->assertSee('Dr. H. Kemahasiswaan UIS, M.Pd.');
        $response->assertSee('Ketua Umum BEM Universitas Ibnu Sina');

        // Sidebar assertions:
        // Must see: Rekapitulasi, SKP, Verifikasi Non-Akademik
        $response->assertSee('Rekapitulasi', false);
        $response->assertSee('Verifikasi Non-Akademik (Kabid)');

        // Must NOT see technical master Tata Kelola or Template LPJ
        $response->assertDontSee('Tata Kelola');
        $response->assertDontSee('Data Dosen');
        $response->assertDontSee('Template LPJ');
    }
}
