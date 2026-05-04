<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'peneliti']);
        Role::create(['name' => 'sekretariat']);
        Role::create(['name' => 'reviewer']);
        Role::create(['name' => 'ketua']);

        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'status'   => 'aktif',
        ]);

        $admin->assignRole('admin');
    }
}