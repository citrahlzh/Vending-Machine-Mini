<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrator',
                'description' => 'Akses penuh dashboard',
                'is_active' => true,
            ]
        );

        Role::updateOrCreate(
            ['slug' => 'staff'],
            [
                'name' => 'Staff',
                'description' => 'Akses monitoring transaksi dan laporan',
                'is_active' => true,
            ]
        );

        Role::updateOrCreate(
            ['slug' => 'operator'],
            [
                'name' => 'operator',
                'description' => 'Akses untuk mengelola mesin',
                'is_active' => true,
            ]
        );
    }
}
