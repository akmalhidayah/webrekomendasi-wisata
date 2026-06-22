<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWisataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_wisata_page_requires_authentication(): void
    {
        $this->get(route('admin.wisata.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_wisata_index_after_login(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.wisata.index'))
            ->assertOk()
            ->assertSee('Data Wisata')
            ->assertSee('Pantai Losari');

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard');
    }
}
