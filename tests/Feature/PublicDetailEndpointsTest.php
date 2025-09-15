<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Perusahaan;
use App\Models\Sekolah;
use App\Models\LembagaPelatihan;
use App\Models\LowonganMagang;
use App\Models\Pelatihan;

class PublicDetailEndpointsTest extends TestCase
{
    use RefreshDatabase; // depends on proper test DB config

    /** @test */
    public function it_returns_404_for_non_existing_perusahaan()
    {
        $this->getJson('/api/perusahaan/detail/999')->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_non_existing_sekolah()
    {
        $this->getJson('/api/sekolah/detail/999')->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_non_existing_lpk()
    {
        $this->getJson('/api/lpk/detail/999')->assertStatus(404);
    }

    /** @test */
    public function it_returns_404_for_non_existing_lowongan()
    {
        $this->getJson('/api/lowongan/show-lowongan/999')->assertStatus(404);
    }
}
