<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KendaraanResource\Pages;
use App\Models\Kendaraan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class KendaraanResource extends Resource
{
    protected static ?string $model = Kendaraan::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Data Kendaraan';
    protected static ?string $pluralModelLabel = 'Data Kendaraan';
    protected static ?string $navigationGroup = 'Manajemen Armada';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'operational']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('no_polisi')
                ->label('Nomor Polisi')
                ->required()
                ->maxLength(15)
                ->unique(ignoreRecord: true)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('no_polisi', strtoupper($state))),

            Forms\Components\TextInput::make('merk')
                ->label('Merk')
                ->required()
                ->maxLength(20)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('merk', strtoupper($state))),

            Forms\Components\TextInput::make('type')
                ->label('Tipe')
                ->required()
                ->maxLength(50)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('type', strtoupper($state))),

            Forms\Components\TextInput::make('jenis')
                ->label('Jenis')
                ->required()
                ->maxLength(20)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('jenis', strtoupper($state))),

            Forms\Components\TextInput::make('tahun')
                ->label('Tahun')
                ->required()
                ->numeric()
                ->minValue(1900)
                ->maxValue(date('Y')),

            Forms\Components\TextInput::make('warna')
                ->label('Warna')
                ->required()
                ->maxLength(20)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('warna', strtoupper($state))),

            Forms\Components\TextInput::make('no_rangka')
                ->label('Nomor Rangka')
                ->required()
                ->maxLength(50)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('no_rangka', strtoupper($state))),

            Forms\Components\TextInput::make('no_mesin')
                ->label('Nomor Mesin')
                ->required()
                ->maxLength(50)
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, Forms\Set $set) => $set('no_mesin', strtoupper($state))),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'siap' => 'SIAP',
                    'beroperasi' => 'BEROPERASI',
                    'perbaikan' => 'PERBAIKAN',
                    'rusak' => 'RUSAK'
                ])
                ->required()
                ->searchable()
                ->reactive(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_polisi')->label('Nomor Polisi')->searchable(),
                Tables\Columns\TextColumn::make('merk')->label('Merk')->searchable(),
                Tables\Columns\TextColumn::make('type')->label('Tipe'),
                Tables\Columns\TextColumn::make('jenis')->label('Jenis'),
                Tables\Columns\TextColumn::make('tahun')->label('Tahun'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'rusak',
                        'success' => 'siap',
                        'warning' => 'perbaikan',
                        'info' => 'beroperasi',
                    ]),
            ])
            ->filters([])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('ubah_status')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrows-up-down')
                    ->action(function (Kendaraan $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                    })
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status Baru')
                            ->required()
                            ->options([
                                'siap' => 'Siap',
                                'beroperasi' => 'Beroperasi',
                                'perbaikan' => 'Perbaikan',
                                'rusak' => 'Rusak',
                            ])
                            ->searchable(),
                    ])
                    ->visible(fn() => in_array(Auth::user()->role, ['admin', 'operational'])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => in_array(Auth::user()->role, ['admin', 'operational'])),
            ]);
    }


    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKendaraans::route('/'),
            'create' => Pages\CreateKendaraan::route('/create'),
            'edit' => Pages\EditKendaraan::route('/{record}/edit'),
            'view' => Pages\ViewKendaraan::route('/{record}'),
        ];
    }
}
