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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key')->unique();
            $table->string('qris_id');
            $table->datetime('transaction_date');
            $table->enum('status', ['pending', 'paid', 'failed', 'expired']);
            $table->enum('dispense_status', ['pending', 'success', 'failed', 'partial']);
            $table->integer('total_amount');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
