<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengirimans';

    protected $fillable = [
        'jadwal_id',
        'tonase',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi ke jadwal pengiriman
    public function jadwal()
    {
        return $this->belongsTo(JadwalPengiriman::class, 'jadwal_id');
    }

    // Accessor untuk file URL (optional)
    public function getSuratJalanUrlAttribute()
    {
        return $this->surat_jalan ? asset('storage/' . $this->surat_jalan) : null;
    }

    public function getDoMuatUrlAttribute()
    {
        return $this->do_muat ? asset('storage/' . $this->do_muat) : null;
    }

    public function getDoBongkarUrlAttribute()
    {
        return $this->do_bongkar ? asset('storage/' . $this->do_bongkar) : null;
    }
}
