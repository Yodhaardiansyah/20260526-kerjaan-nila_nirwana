<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_logs', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type'); // ph_high, ph_low, device_offline
            $table->string('message'); // Contoh: "Peringatan! pH air melebihi batas (8.5)"
            $table->boolean('is_read')->default(false); // Untuk fitur notifikasi web
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_logs');
    }
};