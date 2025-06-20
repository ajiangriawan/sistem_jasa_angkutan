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
         Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->string('telepon', 20)->unique()->nullable();
            $table->string('alamat', 255)->nullable();
            $table->enum('role', [
                'admin_hr', 'admin_direksi', 'operasional_pengiriman',
                'operasional_transportasi', 'operasional_bengkel',
                'operasional_teknisi', 'operasional_sopir',
                'akuntan', 'pemasaran_cs', 'customer'
            ])->default('customer');
            $table->string('bank', 50)->nullable();
            $table->string('no_rekening', 30)->nullable();
            $table->enum('status', ['aktif', 'dijadwalkan', 'bertugas', 'tidak aktif'])->default('aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
