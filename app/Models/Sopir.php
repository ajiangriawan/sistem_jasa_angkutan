<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sopir extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $fillable = ['user_id', 'no_sim', 'telepon', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
