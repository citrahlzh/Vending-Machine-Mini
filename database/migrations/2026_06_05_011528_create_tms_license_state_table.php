<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tms_license_state', function (Blueprint $table) {
            $table->id();
            $table->string('file_hash', 64);
            $table->enum('status', ['valid', 'revoked', 'expired', 'unknown'])->default('unknown');
            $table->timestamp('verified_at')->nullable();
            $table->date('license_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tms_license_state');
    }
};
