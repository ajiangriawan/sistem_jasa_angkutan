<?php

namespace App\Filament\Resources;

use App\Models\{PasanganSopirKendaraan, User, Kendaraan};
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\PasanganSopirKendaraanResource\Pages;

class PasanganSopirKendaraanResource extends Resource
{
    protected static ?string $model = PasanganSopirKendaraan::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Pasangan Sopir & Kendaraan';
    protected static ?string $navigationGroup = 'Manajemen Pengiriman';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_transportasi']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Pilih sopir aktif dan belum dipakai, kecuali yang sedang diedit
            Forms\Components\Select::make('driver_id')
                ->label('Sopir')
                ->options(function (Get $get, ?Model $record) {
                    $usedDriverIds = PasanganSopirKendaraan::query()
                        ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                        ->pluck('driver_id')
                        ->toArray();

                    return User::where('role', 'operasional_sopir')
                        ->where('status', 'aktif')
                        ->whereNotIn('id', $usedDriverIds)
                        ->pluck('name', 'id');
                })
                ->searchable()
                ->preload()
                ->required(),

            // Pilih kendaraan siap dan belum dipakai, kecuali yang sedang diedit
            Forms\Components\Select::make('kendaraan_id')
                ->label('Kendaraan')
                ->options(function (Get $get, ?Model $record) {
                    $usedKendaraanIds = PasanganSopirKendaraan::query()
                        ->when($record, fn($query) => $query->where('id', '!=', $record->id))
                        ->pluck('kendaraan_id')
                        ->toArray();

                    return Kendaraan::where('status', 'siap')
                        ->whereNotIn('id', $usedKendaraanIds)
                        ->pluck('no_polisi', 'id');
                })
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('sopir.name')->label('Sopir'),
            Tables\Columns\TextColumn::make('kendaraan.no_polisi')->label('No Polisi'),
            Tables\Columns\TextColumn::make('kendaraan.type')->label('Tipe'),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ])
        ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPasanganSopirKendaraans::route('/'),
            'create' => Pages\CreatePasanganSopirKendaraan::route('/create'),
            'edit' => Pages\EditPasanganSopirKendaraan::route('/{record}/edit'),
            'view' => Pages\ViewPasanganSopirKendaraan::route('/{record}'),
        ];
    }
}
