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
            $table->string('alert_type'); // ph_high, ph_low, device_offline, dll.
            $table->text('message'); // Isi pesan peringatan dari Node-RED
            $table->boolean('is_read')->default(false); // Penanda apakah sudah dibaca di web
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_logs');
    }
};