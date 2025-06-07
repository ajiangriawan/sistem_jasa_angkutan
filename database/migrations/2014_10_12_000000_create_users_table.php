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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telepon')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->enum('role', ['admin_hr', 'admin_direksi', 'operasional_pengiriman', 'operasional_transportasi', 'operasional_bengkel', 'operasional_teknisi', 'operasional_sopir', 'akuntan',  'pemasaran_cs','customer',])->default('customer');
            $table->enum('status', ['aktif', 'dijadwalkan', 'bertugas', 'tidak aktif',])->default('aktif');
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
