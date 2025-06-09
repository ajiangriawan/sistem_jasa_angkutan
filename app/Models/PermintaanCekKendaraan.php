<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanCekKendaraan extends Model
{
    use HasFactory;
    protected $fillable = ['laporan_id', 'kendaraan_id', 'supervisor_id', 'status', 'catatan'];

    public function laporan()
    {
        return $this->belongsTo(LaporanKendala::class, 'laporan_id');
    }
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
    public function jadwal()
    {
        return $this->hasOne(JadwalCekKendaraan::class, 'permintaan_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }
}
