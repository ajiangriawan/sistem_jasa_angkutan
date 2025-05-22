<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Customer;
use App\Models\Sopir;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $namaPerusahaan = $data['nama_perusahaan'] ?? null;
        $alamat = $data['alamat'] ?? null;
        $no_sim = $data['no_sim'] ?? null;
        $telepon = $data['telepon'] ?? null;

        // Hapus dari array sebelum insert user
        unset($data['nama_perusahaan'], $data['alamat']);

        // Membuat pengguna
        $user = static::getModel()::create($data);

        // Memeriksa apakah role adalah customer dan jika data customer ada
        if ($user->role === 'customer' && $namaPerusahaan && $alamat) {
            try {
                // Menyimpan data customer jika role adalah customer
                Customer::create([
                    'user_id' => $user->id,
                    'nama_perusahaan' => $namaPerusahaan,
                    'alamat' => $alamat,
                ]);
            } catch (\Exception $e) {
                // Penanganan error jika terjadi kegagalan saat menyimpan customer
                session()->flash('error', 'Gagal membuat data customer');
                throw $e;
            }
        }

        // Memeriksa apakah role adalah sopir dan jika data sopir ada
        if ($user->role === 'driver' && $no_sim && $telepon) {
            try {
                // Menyimpan data sopir jika role adalah sopir
                Sopir::create([
                    'user_id' => $user->id,
                    'no_sim' => $no_sim,
                    'telepon' => $telepon,
                ]);
            } catch (\Exception $e) {
                // Penanganan error jika terjadi kegagalan saat menyimpan sopir
                session()->flash('error', 'Gagal membuat data sopir');
                throw $e;
            }
        }

        return $user;
    }
}
