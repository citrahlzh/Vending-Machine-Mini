<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->char('code', 3)->unique();
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();
            $table->string('operator_name')->nullable();
            $table->string('category')->nullable();
            $table->string('size')->nullable();
            $table->string('photo_url')->nullable();
            $table->boolean('is_android')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('condition_status', ['good', 'maintenance', 'damaged'])->default('good');
            $table->timestamps();
            $table->softDeletes();
        });

        $rawCode = (string) (DB::table('site_settings')->where('key', 'machine_code')->value('value') ?? 'AAA');
        $letters = strtoupper((string) preg_replace('/[^A-Za-z]/', '', $rawCode));
        $code = substr(str_pad($letters, 3, 'A'), 0, 3);

        DB::table('machines')->insert([
            'name' => DB::table('site_settings')->where('key', 'machine_name')->value('value') ?? 'Vending Machine',
            'code' => $code,
            'serial_number' => DB::table('site_settings')->where('key', 'machine_serial_number')->value('value'),
            'location' => DB::table('site_settings')->where('key', 'machine_location')->value('value'),
            'operator_name' => DB::table('site_settings')->where('key', 'machine_operator_name')->value('value'),
            'category' => 'Vending Machine',
            'size' => null,
            'photo_url' => null,
            'is_android' => true,
            'status' => 'active',
            'condition_status' => 'good',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
