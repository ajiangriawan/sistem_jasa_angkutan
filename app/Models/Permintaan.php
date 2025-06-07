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
}
