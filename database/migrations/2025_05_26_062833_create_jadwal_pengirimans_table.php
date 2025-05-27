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

            // Foreign key ke permintaan pengiriman
            $table->foreignId('permintaan_id')->constrained('permintaans')->onDelete('cascade');

            // Tanggal dan jam keberangkatan & kedatangan
            $table->date('tanggal_berangkat');
            $table->time('jam_berangkat')->nullable();
            $table->date('tanggal_tiba')->nullable();
            $table->time('jam_tiba')->nullable();

            // Sopir dan kendaraan
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('sopirs')
                ->onDelete('set null');

            $table->foreignId('kendaraan_id')
                ->nullable()
                ->constrained('kendaraans')
                ->onDelete('set null');

            // Status pengiriman: dijadwalkan, dalam perjalanan, selesai, dibatalkan
            $table->enum('status', ['dijadwalkan', 'dalam_perjalanan', 'selesai', 'dibatalkan'])->default('dijadwalkan');

            // Catatan opsional
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
