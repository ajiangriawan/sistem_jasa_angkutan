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
        Schema::create('jadwal_pengirimans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('pasangan_sopir_kendaraan_id')->nullable()->constrained('pasangan_sopir_kendaraans');
            $table->date('tanggal_berangkat');
            $table->time('jam_berangkat')->nullable();
            $table->date('tanggal_tiba')->nullable();
            $table->time('jam_tiba')->nullable();
            $table->enum('status', ['dijadwalkan', 'Dalam Proses', 'Sebagian Berjalan', 'Belum Ada Detail', 'selesai', 'dibatalkan'])->default('dijadwalkan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pengirimans');
    }
};
