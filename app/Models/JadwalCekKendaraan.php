<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalCekKendaraan extends Model
{
    use HasFactory;

    protected $fillable = ['permintaan_id', 'teknisi_id', 'jadwal', 'hasil_cek', 'status'];
    protected $casts = [
        'jadwal' => 'datetime',
    ];

    public function permintaan()
    {
        return $this->belongsTo(PermintaanCekKendaraan::class);
    }
    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }
    public function laporan()
    {
        return $this->belongsTo(LaporanKendala::class, 'laporan_kendala_id'); // Pastikan ini benar
    }
}
