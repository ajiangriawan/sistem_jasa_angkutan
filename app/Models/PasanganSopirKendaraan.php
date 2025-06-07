<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasanganSopirKendaraan extends Model
{
    use HasFactory;

    protected $table = 'pasangan_sopir_kendaraans';

    protected $fillable = [
        'driver_id',
        'kendaraan_id',
    ];

    public function sopir()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'kendaraan_id');
    }
}
