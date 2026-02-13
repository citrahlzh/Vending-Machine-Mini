<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'phone_number' => '089654802563',
            'whatsapp_number' => '6289654802563',
            'is_active' => true,
            'username' => 'admin',
            'password' => 'adminadmin'
        ]);
    }
}
