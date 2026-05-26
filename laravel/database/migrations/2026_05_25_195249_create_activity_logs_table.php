<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('info'); // info, success, warning
            $table->string('description'); // Contoh: "Sistem menyala dan semua perangkat terhubung"
            $table->timestamps(); // Created_at akan menjadi waktu aktivitas
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};