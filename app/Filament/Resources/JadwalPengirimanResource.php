<?php

namespace App\Filament\Resources;


use App\Filament\Resources\JadwalPengirimanResource\Pages;
use App\Models\JadwalPengiriman;
use App\Models\Permintaan;
use App\Models\User;
use App\Models\Kendaraan;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Models\Sopir;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class JadwalPengirimanResource extends Resource
{
    protected static ?string $model = JadwalPengiriman::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Jadwal Pengiriman';
    protected static ?string $pluralLabel = 'Jadwal Pengiriman';
    protected static ?string $navigationGroup = 'Manajemen Pengiriman';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'operational', 'driver']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('permintaan_id')
                    ->label('Permintaan')
                    ->options(function () {
                        return Permintaan::where('status_verifikasi', 'disetujui')->with('customer', 'rute')->get()->mapWithKeys(function ($permintaan) {
                            return [
                                $permintaan->id => $permintaan->customer->nama_perusahaan . ' - ' . $permintaan->rute->nama_rute,
                            ];
                        });
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal_berangkat')->label('Tanggal Berangkat')->required(),
                TimePicker::make('jam_berangkat')->label('Jam Berangkat'),
                DatePicker::make('tanggal_tiba')->label('Tanggal Tiba'),
                TimePicker::make('jam_tiba')->label('Jam Tiba'),

                Select::make('driver_id')
                    ->label('Sopir')
                    ->options(Sopir::where('status', 'aktif')->with('user')->get()->mapWithKeys(fn($s) => [$s->id => $s->user->name]))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->options(Kendaraan::where('status', 'siap')->get()->mapWithKeys(fn($k) => [$k->id => $k->no_polisi . ' - ' . $k->type]))
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'dijadwalkan' => 'Dijadwalkan',
                        'dalam_perjalanan' => 'Dalam Perjalanan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])->default('dijadwalkan'),

                Textarea::make('catatan')->label('Catatan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('permintaan.customer.nama_perusahaan')->label('Customer'),
                TextColumn::make('permintaan.rute.nama')->label('Rute'),
                TextColumn::make('tanggal_berangkat')->date(),
                TextColumn::make('jam_berangkat'),
                TextColumn::make('tanggal_tiba')->date(),
                TextColumn::make('jam_tiba'),
                TextColumn::make('sopir.sopir.nama')->label('Sopir'),
                TextColumn::make('kendaraan.no_polisi')->label('Kendaraan'),
                TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                // Bisa ditambahkan filter status atau tanggal jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operational'])),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operational'])),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalPengirimen::route('/'),
            'create' => Pages\CreateJadwalPengiriman::route('/create'),
            'edit' => Pages\EditJadwalPengiriman::route('/{record}/edit'),
            'view' => Pages\ViewJadwalPengirimen::route('/{record}'),
        ];
    }
}
