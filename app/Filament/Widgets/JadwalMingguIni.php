<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;
use App\Models\JadwalPengiriman;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class JadwalMingguIni extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder|Relation|null
    {
        return JadwalPengiriman::with(['permintaan.rute', 'permintaan.customer'])
            ->whereBetween('tanggal_berangkat', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('tanggal_berangkat')
                ->label('Tanggal')
                ->date('d M Y'),

            TextColumn::make('jam_berangkat')
                ->label('Jam'),

            TextColumn::make('permintaan.customer.name')
                ->label('Customer'),

            TextColumn::make('permintaan.rute.nama_rute')
                ->label('Rute'),

            TextColumn::make('status')
                ->badge()
                ->color(fn(string $state) => match ($state) {
                    'dijadwalkan' => 'info',
                    'selesai' => 'success',
                    'dibatalkan' => 'danger',
                    default => 'gray',
                }),

            TextColumn::make('catatan')
                ->limit(30)
                ->tooltip(fn($record) => $record->catatan),
        ];
    }
/*
    public static function canView(): bool
    {
        return auth()->user()?->role === 'operasional_pengiriman';
    }
        */
}
