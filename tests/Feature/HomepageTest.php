<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_halaman_login_berhasil_dimuat(): void
    {
        // 1. Sistem bertindak sebagai user yang mencoba mengakses halaman login
        $response = $this->get('/');

        // 2. Sistem memvalidasi apakah status HTTP-nya 200 (Halaman berhasil dimuat)
        $response->assertStatus(200);
    }
}
