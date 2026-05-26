<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_settings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique()->default('alat1');
            $table->float('ph_min_limit')->default(6.5);
            $table->float('ph_max_limit')->default(8.0);
            $table->integer('drain_duration_seconds')->default(60);
            $table->enum('mode', ['auto', 'manual'])->default('auto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_settings');
    }
};