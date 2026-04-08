<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = Role::where('slug', 'admin')->value('id');
        $staffRoleId = Role::where('slug', 'staff')->value('id');

        User::updateOrCreate(['username' => 'admin'], [
            'name' => 'Admin',
            'phone_number' => '089654802563',
            'whatsapp_number' => '6289654802563',
            'is_active' => true,
            'username' => 'admin',
            'password' => 'adminadmin',
            'role_id' => $adminRoleId,
        ]);

        User::updateOrCreate(['username' => 'staff'], [
            'name' => 'Staff',
            'phone_number' => '081234567890',
            'whatsapp_number' => '6281234567890',
            'is_active' => true,
            'username' => 'staff',
            'password' => 'staffstaff',
            'role_id' => $staffRoleId,
        ]);
    }
}
