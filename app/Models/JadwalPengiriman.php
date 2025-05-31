<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPengiriman extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pengirimans';

    protected $fillable = [
        'permintaan_id',
        'tanggal_berangkat',
        'jam_berangkat',
        'tanggal_tiba',
        'jam_tiba',
        'driver_id',
        'kendaraan_id',
        'status',
        'catatan',
    ];

    /**
     * Relasi ke permintaan pengiriman
     */
    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class, 'permintaan_id');
    }

    /**
     * Relasi ke user (sopir)
     */

    public function sopir()
    {
        return $this->belongsTo(Sopir::class, 'driver_id');
    }

    /**
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'jadwal_id');
    }
}
