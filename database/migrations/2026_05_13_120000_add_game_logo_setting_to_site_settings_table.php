<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'game_logo_url'],
            [
                'label' => 'Logo Games',
                'value' => null,
                'type' => 'file',
                'group' => 'Umum',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('site_settings')->where('key', 'game_logo_url')->delete();
    }
};
