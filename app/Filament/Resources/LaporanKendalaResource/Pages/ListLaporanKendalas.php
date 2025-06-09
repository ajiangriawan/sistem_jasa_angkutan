<?php

namespace App\Filament\Resources\LaporanKendalaResource\Pages;

use App\Filament\Resources\LaporanKendalaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListLaporanKendalas extends ListRecords
{
    protected static string $resource = LaporanKendalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $user = Auth::user();

        // Sopir hanya melihat laporan miliknya
        if ($user->role === 'operasional_sopir') {
            return $query->where('sopir_id', $user->id);
        }

        // operasional_transportasi hanya melihat yang kategori kerusakan_kendaraan
        if ($user->role === 'operasional_transportasi') {
            return $query->where('kategori', 'kerusakan_kendaraan');
        }

        // operasional_pengiriman hanya melihat yang kategori umum
        if ($user->role === 'operasional_pengiriman') {
            return $query->where('kategori', 'umum');
        }

        // Role lain bisa melihat semua
        return $query;
    }
}
