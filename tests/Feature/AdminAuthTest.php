<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_login_page(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Login Admin');
    }

    public function test_admin_can_login_with_seeded_account(): void
    {
        $this->seed(AdminSeeder::class);

        $this->post(route('admin.login.process'), [
            'email' => 'admin@gmail.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@gmail.com')->first());
        $this->assertDatabaseHas('log_aktivitas', ['aktivitas' => 'Admin Login']);
    }

    public function test_invalid_credentials_cannot_login(): void
    {
        $this->seed(AdminSeeder::class);

        $this->from(route('admin.login'))->post(route('admin.login.process'), [
            'email' => 'admin@gmail.com',
            'password' => 'salah',
        ])->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
