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
        Schema::create('tms_push_queue', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['transaction', 'issue']);
            $table->json('payload');
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamp('last_tried_at')->nullable();
            $table->timestamp('pushed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tms_push_queue');
    }
};
