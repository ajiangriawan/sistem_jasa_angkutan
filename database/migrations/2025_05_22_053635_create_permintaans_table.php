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
        Schema::create('permintaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rute_id')->constrained('rutes')->onDelete('cascade');
            $table->date('tanggal_permintaan');
            $table->double('estimasi_tonase', 8, 2); // misalnya max 999999.99
             $table->integer('jumlah_unit')->default(1); 

            $table->enum('status_verifikasi', ['pending', 'disetujui', 'dijadwalkan', 'Dalam Proses', 'Sebagian Berjalan', 'Belum Ada Detail','selesai', 'ditolak'])
                ->default('pending');

            $table->json('dokumen_pendukung')->nullable();
            $table->text('komentar_verifikasi')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaans');
    }
};
