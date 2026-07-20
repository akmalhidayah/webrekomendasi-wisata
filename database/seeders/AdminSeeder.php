<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('admin.password');

        if (! $password && app()->environment('production')) {
            throw new RuntimeException('DEFAULT_ADMIN_PASSWORD wajib diisi saat menjalankan seeder di production.');
        }

        $attributes = ['name' => config('admin.name'), 'role' => 'admin'];
        $existing = User::where('email', config('admin.email'))->first();

        if (! $existing || config('admin.force_password_reset')) {
            $attributes['password'] = Hash::make($password ?: 'local-testing-admin');
        }

        User::updateOrCreate(['email' => config('admin.email')], $attributes);
    }
}
