<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_jadwal_pengirimans', function (Blueprint $table) {
            $table->id();

            // merujuk ke jadwal_pengirimans.id
            $table->foreignId('jadwal_pengiriman_id')
                ->constrained('jadwal_pengirimans')
                ->onDelete('cascade');
                
            $table->json('surat_jalan')->nullable();     // path file
            $table->json('do_muat')->nullable();         // path file
            $table->json('do_bongkar')->nullable();      // path file

            // merujuk ke pasangan_sopir_kendaraans.id
            $table->foreignId('pasangan_sopir_kendaraan_id')
                ->constrained('pasangan_sopir_kendaraans')
                ->onDelete('cascade');
            $table->enum('status', ['dijadwalkan', 'pengambilan', 'pengantaran', 'selesai', 'dibatalkan'])->default('dijadwalkan');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_jadwal_pengirimans');
    }
};
