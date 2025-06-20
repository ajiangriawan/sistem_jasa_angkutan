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
        Schema::create('kendaraans', function (Blueprint $table) {
            $table->id();
            $table->string('no_polisi', 15)->unique();
            $table->string('merk', 50);
            $table->string('type', 50);
            $table->string('jenis', 30);
            $table->year('tahun');
            $table->string('warna', 30);
            $table->string('no_rangka', 50)->unique();
            $table->string('no_mesin', 50)->unique();
            $table->enum('status', ['siap', 'dijadwalkan', 'beroperasi', 'perbaikan', 'rusak'])->default('siap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraans');
    }
};
