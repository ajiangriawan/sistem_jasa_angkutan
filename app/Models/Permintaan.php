<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Permintaan extends Model
{
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'dokumen_pendukung' => 'array',
    ];

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    public function jadwalPengiriman()
    {
        return $this->hasMany(JadwalPengiriman::class);
    }

    public function detailJadwalPengirimans()
    {
        return $this->hasManyThrough(
            DetailJadwalPengiriman::class,
            JadwalPengiriman::class,
            'permintaan_id', // foreign key di tabel 'jadwal_pengirimans'
            'jadwal_pengiriman_id', // foreign key di tabel 'detail_jadwal_pengirimans'
            'id', // local key di tabel 'permintaans'
            'id' // local key di tabel 'jadwal_pengirimans'
        );
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    // App\Models\Permintaan.php
    public function pengirimanTonase()
    {
        return $this->hasManyThrough(
            \App\Models\Pengiriman::class,           // model tujuan
            \App\Models\JadwalPengiriman::class,     // model perantara
            'permintaan_id',                         // foreign key di JadwalPengiriman
            'jadwal_id',                             // foreign key di Pengiriman
            'id',                                    // local key di Permintaan
            'id'                                     // local key di JadwalPengiriman
        );
    }
}
