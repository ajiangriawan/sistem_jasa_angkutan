<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    /**
     * Nama tabel jika tidak mengikuti konvensi Laravel
     */
    protected $table = 'kendaraans';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'no_polisi',
        'merk',
        'type',
        'jenis',
        'tahun',
        'warna',
        'no_rangka',
        'no_mesin',
    ];

    /**
     * Accessors & Mutators (setter) untuk memastikan data huruf besar saat disimpan
     */
    public function setNoPolisiAttribute($value)
    {
        $this->attributes['no_polisi'] = strtoupper($value);
    }

    public function setMerkAttribute($value)
    {
        $this->attributes['merk'] = strtoupper($value);
    }

    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = strtoupper($value);
    }

    public function setJenisAttribute($value)
    {
        $this->attributes['jenis'] = strtoupper($value);
    }

    public function setWarnaAttribute($value)
    {
        $this->attributes['warna'] = strtoupper($value);
    }

    public function setNoRangkaAttribute($value)
    {
        $this->attributes['no_rangka'] = strtoupper($value);
    }

    public function setNoMesinAttribute($value)
    {
        $this->attributes['no_mesin'] = strtoupper($value);
    }
}
