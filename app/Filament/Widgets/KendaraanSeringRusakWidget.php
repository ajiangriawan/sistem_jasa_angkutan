<?php

namespace App\Filament\Widgets;

use App\Models\Kendaraan;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;


class KendaraanSeringRusakWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Kendaraan::query()
            ->select('kendaraans.*')
            ->withCount([
                'laporanKendala as total' => function ($query) {
                    $query->join('permintaan_cek_kendaraans', 'laporan_kendalas.id', '=', 'permintaan_cek_kendaraans.laporan_id')
                        ->whereColumn('permintaan_cek_kendaraans.kendaraan_id', 'kendaraans.id');
                }
            ])
            ->orderByDesc('total')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('no_polisi')->label('No Polisi'),
            TextColumn::make('total')->label('Jumlah Laporan')->sortable(),
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_direksi', 'operasional_transportasi']);
    
    }
}
