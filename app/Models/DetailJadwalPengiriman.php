<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJadwalPengiriman extends Model
{
    use HasFactory;

    protected $table = 'detail_jadwal_pengirimans';

    protected $fillable = [
        'jadwal_pengiriman_id',
        'pasangan_sopir_kendaraan_id',
        'surat_jalan',
        'do_muat',
        'do_bongkar',
        'status'
    ];

    protected $casts = [
        'surat_jalan' => 'array', // Casting ke array untuk multiple files
        'do_muat' => 'array', // Casting ke array untuk multiple files
        'do_bongkar' => 'array', // Casting ke array untuk multiple files
    ];

    // Relasi ke JadwalPengiriman
    public function jadwal()
    {
        return $this->belongsTo(JadwalPengiriman::class, 'jadwal_pengiriman_id');
    }

    // Relasi ke PasanganSopirKendaraan
    public function pasangan()
    {
        return $this->belongsTo(PasanganSopirKendaraan::class, 'pasangan_sopir_kendaraan_id');
    }

    
}
