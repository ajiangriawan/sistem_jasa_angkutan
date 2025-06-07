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
        'pasangan_sopir_kendaraan_id',
        'tanggal_berangkat',
        'jam_berangkat',
        'tanggal_tiba',
        'jam_tiba',
        /*
        'driver_id',
        'kendaraan_id',
        */
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
     * Relasi ke kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'jadwal_id', 'id');
    }

    public function detailJadwal()
    {
        return $this->hasMany(DetailJadwalPengiriman::class, 'jadwal_pengiriman_id');
    }

    protected static function booted()
    {
        static::deleting(function ($jadwal) {
            // Ubah status permintaan kembali ke 'disetujui'
            if ($jadwal->permintaan && $jadwal->permintaan->status_verifikasi !== 'selesai') {
                $jadwal->permintaan->update(['status_verifikasi' => 'disetujui']);
            }

            // Ubah status kendaraan kembali ke 'siap'
            if ($jadwal->kendaraan && $jadwal->kendaraan->status === 'dijadwalkan') {
                $jadwal->kendaraan->update(['status' => 'siap']);
            }

            // Ubah status semua sopir yang terlibat jadi 'aktif'
            foreach ($jadwal->detailJadwal as $detail) {
                $sopir = $detail->pasangan->sopir ?? null;
                if ($sopir && $sopir->status === 'dijadwalkan') {
                    $sopir->update(['status' => 'aktif']);
                }
            }
        });
    }

    // Metode untuk mendapatkan status ringkasan dari jadwal
    public function getOverallStatusAttribute(): string
    {
        $details = $this->detailJadwal;

        if ($details->isEmpty()) {
            return 'Belum Ada Detail';
        }

        // Ambil semua status dari detail jadwal
        $statuses = $details->pluck('status')->unique();

        // Jika semua sudah 'selesai'
        if ($statuses->count() === 1 && $statuses->contains('selesai')) {
            return 'Selesai';
        }

        // Jika ada yang 'pengantaran' atau 'pengambilan'
        if ($statuses->contains('pengantaran') || $statuses->contains('pengambilan')) {
            return 'Dalam Proses';
        }

        // Jika semua masih 'dijadwalkan'
        if ($statuses->count() === 1 && $statuses->contains('dijadwalkan')) {
            return 'Dijadwalkan';
        }

        // Kasus lain (misalnya campuran, atau sebagian selesai)
        return 'Sebagian Berjalan';
    }
}
