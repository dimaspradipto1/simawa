<?php

namespace Tests\Feature;

use App\Models\Kejuaraan;
use App\Models\PrestasiBelmawa;
use App\Models\PrestasiMandiri;
use App\Models\Rekognisi;
use App\Models\Sertifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KabidRoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $kabid;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kabid = User::factory()->create([
            'name'     => 'Kabid Kemahasiswaan UIS',
            'email'    => 'kabid.test@uis.ac.id',
            'role'     => 'kabid',
            'fakultas' => 'Fakultas Sains dan Teknologi',
        ]);

        $this->admin = User::factory()->create([
            'name'     => 'Admin BKAK UIS',
            'email'    => 'admin.test@uis.ac.id',
            'role'     => 'adminbkak',
            'fakultas' => 'Fakultas Sains dan Teknologi',
        ]);
    }

    public function test_kabid_can_view_rekapitulasi_and_lists(): void
    {
        $this->actingAs($this->kabid);

        $response = $this->get(route('rekapitulasi.index'));
        $response->assertStatus(200);

        $response = $this->get(route('kejuaraan.index'));
        $response->assertStatus(200);

        $response = $this->get(route('prestasi-mandiri.index'));
        $response->assertStatus(200);

        $response = $this->get(route('prestasi-belmawa.index'));
        $response->assertStatus(200);

        $response = $this->get(route('rekognisi.index'));
        $response->assertStatus(200);

        $response = $this->get(route('sertifikasi.index'));
        $response->assertStatus(200);
    }

    public function test_kabid_cannot_access_create_forms_on_penyelenggara_and_prestasi(): void
    {
        $this->actingAs($this->kabid);

        $this->get(route('kejuaraan.create'))->assertStatus(403);
        $this->get(route('prestasi-mandiri.create'))->assertStatus(403);
        $this->get(route('prestasi-belmawa.create'))->assertStatus(403);
        $this->get(route('rekognisi.create'))->assertStatus(403);
        $this->get(route('sertifikasi.create'))->assertStatus(403);
    }

    public function test_admin_can_still_access_create_forms(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('kejuaraan.create'))->assertStatus(200);
        $this->get(route('prestasi-mandiri.create'))->assertStatus(200);
        $this->get(route('prestasi-belmawa.create'))->assertStatus(200);
        $this->get(route('rekognisi.create'))->assertStatus(200);
        $this->get(route('sertifikasi.create'))->assertStatus(200);
    }
}
