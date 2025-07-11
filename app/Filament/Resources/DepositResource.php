<?php

namespace App\Filament\Resources;

use App\Models\Deposit;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Resources\DepositResource\Pages;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Deposit';
    protected static ?string $navigationGroup = 'Keuangan';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['akuntan', 'customer']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        $user = Auth::user();

        return $form->schema([
            $user->role === 'akuntan'
                ? Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Customer')
                    ->searchable()
                    ->required()
                : Hidden::make('user_id')->default($user->id),

            TextInput::make('jumlah')
                ->label('Jumlah Deposit')
                ->numeric()
                ->required(),

            FileUpload::make('bukti_transfer')
                ->label('Bukti Transfer')
                ->disk('public')
                ->directory('bukti-transfer')
                ->preserveFilenames()
                ->image()
                ->downloadable()
                ->nullable(),

            Select::make('status')
                ->options([
                    'menunggu' => 'Menunggu',
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                ])
                ->default('menunggu')
                ->visible($user->role === 'akuntan')
                ->disabled($user->role === 'customer'),

            Textarea::make('catatan')
                ->label('Catatan Akuntan')
                ->maxLength(500)
                ->visible($user->role === 'akuntan')
                ->disabled($user->role === 'customer'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Customer')->sortable()->searchable(),
                TextColumn::make('jumlah')->money('IDR', true),
                BadgeColumn::make('status')
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                    }),
                TextColumn::make('created_at')->label('Tanggal')->dateTime()->sortable()->searchable(),
            ])
            ->defaultSort('status', 'desc')
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('preview_files')
                    ->label('Bukti Transfer')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Bukti')
                    ->modalContent(fn($record) => view('filament.modals.bukti-viewer', [
                        'files' => $record->bukti_transfer,
                    ]))
                    ->visible(fn($record) => !empty($record->bukti_transfer)),

                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn($record) =>
                        $record->status === 'menunggu' && Auth::user()->role === 'akuntan'
                    )
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan (Opsional)')
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->action(function (array $data, Deposit $record) {
                        $record->update([
                            'status' => 'diterima',
                            'catatan' => $data['catatan'] ?? null,
                        ]);
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) =>
                        $record->status === 'menunggu' && Auth::user()->role === 'akuntan'
                    )
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan (Opsional)')
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->action(function (array $data, Deposit $record) {
                        $record->update([
                            'status' => 'ditolak',
                            'catatan' => $data['catatan'] ?? null,
                        ]);
                    }),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeposits::route('/'),
            'create' => Pages\CreateDeposit::route('/create'),
            'edit' => Pages\EditDeposit::route('/{record}/edit'),
            'view' => Pages\ViewDeposit::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return match ($user->role) {
            'customer' => parent::getEloquentQuery()->where('user_id', $user->id),
            default => parent::getEloquentQuery(),
        };
    }
}
