<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin_hr']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn($state) => filled($state))
                ->maxLength(255)
                ->autocomplete('new-password'),

            Forms\Components\TextInput::make('telepon')
                ->label('Telepon')
                ->tel()
                ->maxLength(15)
                ->nullable(),

            Forms\Components\Textarea::make('alamat')
                ->label('Alamat')
                ->maxLength(65535)
                ->nullable(),

            Forms\Components\Select::make('role')
                ->label('Peran / Role')
                ->options([
                    'admin_direksi' => 'Direksi',
                    'admin_hr' => 'HR',
                    'operasional_pengiriman' => 'Pengiriman',
                    'operasional_transportasi' => 'Transportasi',
                    'operasional_bengkel' => 'Bengkel',
                    'operasional_teknisi' => 'Teknisi',
                    'operasional_sopir' => 'Sopir',
                    'akuntan' => 'Akuntan',
                    'pemasaran_cs' => 'CS',
                    'customer' => 'Customer',
                ])
                ->required()
                ->searchable()
                ->live(), // Penting agar perubahan bisa dideteksi oleh `->visible()`

            Forms\Components\TextInput::make('bank')
                ->label('Nama Bank')
                ->maxLength(100)
                ->visible(fn(Get $get) => $get('role') === 'operasional_sopir','admin_hr'),

            Forms\Components\TextInput::make('no_rekening')
                ->label('No Rekening')
                ->maxLength(100)
                ->visible(fn(Get $get) => $get('role') === 'operasional_sopir','admin_hr'),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'aktif' => 'Aktif',
                    'dijadwalkan' => 'Dijadwalkan',
                    'bertugas' => 'Bertugas',
                    'tidak aktif' => 'Tidak Aktif',
                ])
                ->default('aktif')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('role')
                    ->label('Role')
                    ->color(fn(string $state): string => match ($state) {
                        'admin_direksi', 'admin_hr' => 'danger',
                        'operasional_pengiriman',
                        'operasional_transportasi',
                        'operasional_bengkel',
                        'operasional_teknisi',
                        'operasional_sopir' => 'warning',
                        'akuntan' => 'info',
                        'pemasaran_cs' => 'gray',
                        'customer' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'dijadwalkan',
                        'info' => 'bertugas',
                        'danger' => 'tidak aktif',
                    ])
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()->role === 'admin_hr'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
