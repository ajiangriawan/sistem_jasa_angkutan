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
        // Ambil data nested dari form
        $customerData = $data['customer'] ?? [];
        $sopirData = $data['sopir'] ?? [];

        // Hapus nested data dari array utama agar tidak error saat create user
        unset($data['customer'], $data['sopir']);

        // Buat user
        $user = static::getModel()::create($data);

        // Jika role customer dan data tersedia, buat relasi customer
        if ($user->role === 'customer' && !empty($customerData)) {
            Customer::create([
                'user_id' => $user->id,
                'nama_perusahaan' => $customerData['nama_perusahaan'] ?? '',
                'alamat' => $customerData['alamat'] ?? '',
            ]);
        }

        // Jika role driver dan data tersedia, buat relasi sopir
        if ($user->role === 'driver' && !empty($sopirData)) {
            Sopir::create([
                'user_id' => $user->id,
                'no_sim' => $sopirData['no_sim'] ?? '',
                'telepon' => $sopirData['telepon'] ?? '',
            ]);
        }

        return $user;
    }
}
