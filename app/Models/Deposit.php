<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'jumlah', 'bukti_transfer', 'status', 'catatan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
