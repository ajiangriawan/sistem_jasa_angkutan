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
        Schema::create('laporan_kendalas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sopir_id')->constrained('users')->cascadeOnDelete();
            $table->text('deskripsi');
            $table->string('alamat', 255);
            $table->enum('kategori', ['umum', 'kerusakan_kendaraan']);
            $table->json('foto_kendala')->nullable();
            $table->enum('status', ['dilaporkan', 'ditindaklanjuti', 'selesai'])->default('dilaporkan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_kendalas');
    }
};
