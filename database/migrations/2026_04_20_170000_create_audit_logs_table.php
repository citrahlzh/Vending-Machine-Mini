<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->default('app');
            $table->string('event');
            $table->string('action')->nullable();
            $table->string('description')->nullable();
            $table->nullableMorphs('actor');
            $table->string('actor_name')->nullable();
            $table->nullableMorphs('subject');
            $table->string('subject_label')->nullable();
            $table->string('route_name')->nullable()->index();
            $table->string('method', 10)->nullable()->index();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable()->index();
            $table->json('tags')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['channel', 'event']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
