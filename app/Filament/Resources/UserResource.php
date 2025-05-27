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

        return Auth::check() && in_array(Auth::user()->role, ['admin', 'operational']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            // Field Nama Lengkap
            Forms\Components\TextInput::make('name')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),

            // Field Email
            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            // Field Password
            Forms\Components\TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
                ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn($state) => filled($state)) // ⬅ hanya update jika diisi
                ->maxLength(255)
                ->autocomplete('new-password'),

            // Field Role
            Forms\Components\Select::make('role')
                ->label('Peran / Role')
                ->options([
                    'admin' => 'Admin',
                    'operational' => 'Operasional',
                    'driver' => 'Sopir',
                    'customer' => 'Customer',

                ])
                ->required()
                ->searchable()
                ->reactive(),

            // Field Nama Perusahaan, muncul jika role adalah 'customer'
            Forms\Components\TextInput::make('customer.nama_perusahaan')
                ->label('Nama Perusahaan')
                ->visible(fn(Get $get) => $get('role') === 'customer')
                ->requiredIf('role', 'customer')
                ->maxLength(255)
                // Isi nilai jika sudah ada (misalnya, dengan menggunakan relasi atau nilai dari customer)
                ->default(fn($get) => optional($get('customer'))->nama_perusahaan),

            // Field Alamat, muncul jika role adalah 'customer'
            Forms\Components\Textarea::make('customer.alamat')
                ->label('Alamat')
                ->visible(fn(Get $get) => $get('role') === 'customer')
                ->requiredIf('role', 'customer')
                ->maxLength(65535)
                // Isi nilai jika sudah ada (misalnya, dengan menggunakan relasi atau nilai dari customer)
                ->default(fn($get) => optional($get('customer'))->alamat),

            Forms\Components\TextInput::make('sopir.no_sim')
                ->label('Nomor SIM')
                ->visible(fn(Get $get) => $get('role') === 'driver')
                ->requiredIf('role', 'driver')
                ->maxLength(20),

            Forms\Components\TextInput::make('sopir.telepon')
                ->label('Nomor Telepon')
                ->visible(fn(Get $get) => $get('role') === 'driver')
                ->requiredIf('role', 'driver')
                ->maxLength(15)

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
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'customer',
                        'warning' => 'operational',
                        'info' => 'driver',
                    ]),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()->role === 'admin'),
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
