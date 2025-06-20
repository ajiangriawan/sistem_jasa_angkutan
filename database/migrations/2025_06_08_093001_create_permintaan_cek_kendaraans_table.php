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
        Schema::create('permintaan_cek_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_kendalas')->cascadeOnDelete();
            $table->foreignId('kendaraan_id')->constrained('kendaraans')->onDelete('cascade');
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['diajukan', 'dikonfirmasi', 'dijadwalkan', 'selesai'])->default('diajukan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_cek_kendaraans');
    }
};
