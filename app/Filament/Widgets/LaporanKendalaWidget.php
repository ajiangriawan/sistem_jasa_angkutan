<?php

namespace App\Filament\Widgets;

use App\Models\LaporanKendala;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LaporanKendalaWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return LaporanKendala::with(['sopir'])
            ->latest()
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('sopir.name')
                ->label('Sopir'),

            TextColumn::make('kategori')
                ->label('Kategori')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'kerusakan_kendaraan' => 'danger',
                    'umum' => 'warning',
                    default => 'gray',
                }),

            TextColumn::make('deskripsi')
                ->label('Deskripsi')
                ->limit(40)
                ->tooltip(fn ($record) => $record->deskripsi),

            TextColumn::make('alamat')
                ->label('Lokasi')
                ->limit(25),

            TextColumn::make('status')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'dilaporkan' => 'warning',
                    'ditindaklanjuti' => 'info',
                    'selesai' => 'success',
                    default => 'gray',
                }),

            TextColumn::make('created_at')
                ->label('Dilaporkan')
                ->dateTime('d M Y H:i'),
        ];
    }
/*
    public static function canView(): bool
    {
        return Auth::user()?->role === 'operasional_transportasi';
    }
        */
}
