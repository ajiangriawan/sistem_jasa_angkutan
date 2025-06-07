<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rute extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'customer_id',
        'nama_rute',
        'jarak_km',
        'harga',
        'uang_jalan',
        'bonus',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

}
