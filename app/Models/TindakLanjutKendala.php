<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakLanjutKendala extends Model
{
    use HasFactory;
    protected $fillable = ['laporan_id', 'user_id', 'catatan'];
    public function laporan()
    {
        return $this->belongsTo(LaporanKendala::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
