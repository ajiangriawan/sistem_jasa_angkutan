<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// app/Models/Invoice.php

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'permintaan_id',
        'customer_id',
        'total_uang_jalan',
        'sisa_deposit_setelah_invoice',
        'bukti_pembayaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'bukti_pembayaran' => 'array',
    ];

    public function permintaan()
    {
        return $this->belongsTo(Permintaan::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
