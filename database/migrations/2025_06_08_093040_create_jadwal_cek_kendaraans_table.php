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
        Schema::create('jadwal_cek_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_cek_kendaraans')->cascadeOnDelete();
            $table->foreignId('teknisi_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('jadwal');
            $table->text('hasil_cek')->nullable();
            $table->enum('status', ['terjadwal', 'selesai'])->default('terjadwal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_cek_kendaraans');
    }
};
