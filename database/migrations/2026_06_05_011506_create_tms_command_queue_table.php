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
        Schema::create('tms_command_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tms_command_id');
            $table->string('type', 50);
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'executing', 'done', 'failed'])->default('pending');
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('executed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tms_command_queue');
    }
};
