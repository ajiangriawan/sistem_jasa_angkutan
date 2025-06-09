<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class LaporanKendala extends Model
{
    use HasFactory;
    protected $fillable = ['sopir_id',  'deskripsi', 'alamat', 'kategori', 'status', 'foto_kendala'];

    protected $casts = [
        'foto_kendala' => 'array',
    ];

    public function sopir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sopir_id');
    }
    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class);
    }
    public function tindakLanjut(): HasMany
    {
        return $this->hasMany(TindakLanjutKendala::class, 'laporan_id');
    }
    public function permintaanCek()
    {
        return $this->hasOne(PermintaanCekKendaraan::class, 'laporan_id');
    }
    public function jadwalCek()
    {
        return $this->hasOneThrough(
            JadwalCekKendaraan::class,
            PermintaanCekKendaraan::class,
            'laporan_id', // Foreign key di PermintaanPengecekan
            'permintaan_id', // Foreign key di JadwalCekKendaraan
            'id', // Local key di LaporanKendala
            'id'  // Local key di PermintaanPengecekan
        );
    }
}
