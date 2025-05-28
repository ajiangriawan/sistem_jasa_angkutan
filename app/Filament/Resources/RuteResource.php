<?php


namespace App\Filament\Resources;

use App\Filament\Resources\RuteResource\Pages;
use App\Filament\Resources\RuteResource\RelationManagers;
use App\Models\Rute;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RuteResource extends Resource
{
    protected static ?string $model = Rute::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Manajemen Pengiriman';

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
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'nama_perusahaan')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\TextInput::make('nama_rute')->label('Nama Rute')->required(),
                Forms\Components\TextInput::make('jarak_km')->label('Jarak (km)')->numeric()->required(),
                Forms\Components\TextInput::make('harga')->label('Harga')->prefix('Rp')->numeric()->required(),
                Forms\Components\TextInput::make('uang_jalan')->label('Uang Jalan')->prefix('Rp')->numeric()->required(),
                Forms\Components\TextInput::make('bonus')->label('Bonus')->prefix('Rp')->numeric()->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama_perusahaan')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_rute')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jarak_km')->label('Jarak (km)')->sortable(),
                Tables\Columns\TextColumn::make('harga')->money('IDR', true),
                Tables\Columns\TextColumn::make('uang_jalan')->money('IDR', true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        !in_array($record->status_verifikasi, ['disetujui', 'ditolak']) &&
                            in_array(auth()->user()->role, ['admin', 'operational'])
                    ),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operational'])),

            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRutes::route('/'),
            'create' => Pages\CreateRute::route('/create'),
            'edit' => Pages\EditRute::route('/{record}/edit'),
            'view' => Pages\ViewRute::route('/{record}'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Rute::count();
    }
}
