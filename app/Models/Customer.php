<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['user_id', 'nama_perusahaan', 'alamat'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function rutes()
    {
        return $this->hasMany(Rute::class);
    }

}
