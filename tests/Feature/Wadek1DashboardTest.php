<?php

namespace Tests\Feature;

use App\Models\SkpKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Wadek1DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_wadek1_dashboard_renders_custom_view_with_faculty_data(): void
    {
        $wadek = User::factory()->create([
            'name'     => 'Assoc. Prof. Dr. Okta veza, M.Kom.',
            'email'    => 'okta.veza@uis.ac.id',
            'role'     => 'wadek1',
            'fakultas' => 'Fakultas Sains dan Teknologi',
        ]);

        $mhs = User::factory()->create([
            'name'          => 'Mahasiswa FST UIS',
            'email'         => 'mhs.fst@student.uis.ac.id',
            'role'          => 'mahasiswa',
            'nim'           => '201055201001',
            'program_studi' => 'S1 - Teknik Informatika',
            'fakultas'      => 'Fakultas Sains dan Teknologi',
        ]);

        SkpKegiatan::create([
            'user_id'          => $mhs->id,
            'nama_mahasiswa'   => $mhs->name,
            'nim'              => $mhs->nim,
            'program_studi'    => $mhs->program_studi,
            'fakultas'         => $mhs->fakultas,
            'kategori_id'      => 'rekognisi',
            'jenis_kegiatan'   => 'Publikasi Jurnal SINTA 2',
            'nama_kegiatan'    => 'Rancang Bangun Sistem Cerdas Berbasis AI',
            'sifat_kegiatan'   => 'Akademik',
            'target_role'      => 'wadek1',
            'poin_skp'         => 30,
            'status'           => 'Pending',
            'tanggal_kegiatan' => '2026-11-20',
        ]);

        $this->actingAs($wadek);

        $response = $this->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.dashboard.wadek1');
        $response->assertSee('Dashboard Wakil Dekan 1');
        $response->assertSee('Assoc. Prof. Dr. Okta veza, M.Kom.');
        $response->assertSee('Fakultas Sains dan Teknologi');
        $response->assertSee('Rancang Bangun Sistem Cerdas Berbasis AI');

        // Verify that irrelevant menus are NOT present in the sidebar for Wadek 1
        $response->assertDontSee('Rekapitulasi & Laporan');
        $response->assertDontSee('Prestasi Belmawa');
        $response->assertDontSee('Kejuaraan / Ajang / Lomba');
        $response->assertDontSee('Data Dosen');
    }

    public function test_admin_dashboard_renders_main_dashboard_view(): void
    {
        $admin = User::factory()->create([
            'name'  => 'Admin BKAK Utama',
            'email' => 'admin.bkak@uis.ac.id',
            'role'  => 'adminbkak',
        ]);

        $this->actingAs($admin);

        $response = $this->get(route('dashboard.index'));
        $response->assertStatus(200);
        $response->assertViewIs('layouts.dashboard.index');
        $response->assertSee('Rekapitulasi', false);
        $response->assertSee('Prestasi');
    }
}
