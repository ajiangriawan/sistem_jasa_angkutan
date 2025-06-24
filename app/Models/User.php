<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\DatabaseNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telepon',
        'alamat',
        'role',
        'bank',
        'no_rekening',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    // App\Models\User
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function pasanganSopir()
    {
        return $this->hasMany(PasanganSopirKendaraan::class, 'sopir_id');
    }

    // 2. Melalui pasangan sopir ke detail jadwal
    public function detailJadwal()
    {
        return $this->hasManyThrough(
            DetailJadwalPengiriman::class,
            PasanganSopirKendaraan::class,
            'sopir_id', // Foreign key di PasanganSopirKendaraan
            'pasangan_sopir_kendaraan_id', // Foreign key di DetailJadwalPengiriman
            'id', // Local key di User
            'id'  // Local key di PasanganSopirKendaraan
        );
    }

    // 3. Jadwal pengiriman melalui detail jadwal
    public function jadwalPengiriman()
    {
        return $this->hasManyThrough(
            JadwalPengiriman::class,
            DetailJadwalPengiriman::class,
            'pasangan_sopir_kendaraan_id', // FK ke PasanganSopirKendaraan
            'id', // FK di JadwalPengiriman (dihubungkan melalui relasi manual)
            'id', // User.id
            'jadwal_pengiriman_id' // DetailJadwalPengiriman.jadwal_pengiriman_id
        );
    }
}
