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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    protected $casts = [
        'dokumen_pendukung' => 'array',
    ];

    public function rute()
    {
        return $this->belongsTo(Rute::class);
    }

    // App\Models\Permintaan.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
