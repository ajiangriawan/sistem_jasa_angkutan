<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontrakKerja extends Model
{
    protected $fillable = [
        'customer_id',
        'tanggal_mulai',
        'tanggal_akhir',
        'status',
        'files',
    ];

    protected $casts = [
        'files' => 'array',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
