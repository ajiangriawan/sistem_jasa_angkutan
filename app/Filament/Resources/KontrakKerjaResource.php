<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KontrakKerjaResource\Pages;
use App\Models\KontrakKerja;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class KontrakKerjaResource extends Resource
{
    protected static ?string $model = KontrakKerja::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'Manajemen Customer';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_pengiriman', 'admin_hr']);
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
                    ->options(
                        User::where('role', 'customer')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),


                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),

                DatePicker::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->required(),

                Select::make('status')
                    ->label('Status Kontrak')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak aktif' => 'Tidak Aktif',
                    ])
                    ->default('aktif')
                    ->required(),

                FileUpload::make('files')
                    ->label('File Kontrak')
                    ->multiple()
                    ->preserveFilenames()
                    ->directory('kontrak-kerja')
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull()
                    ->helperText('Upload file kontrak kerja (bisa lebih dari satu).'),
            ])
            ->columns([
                'sm' => 1,
                'md' => 2,
                'lg' => 2,
                'xl' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.name')->label('Customer')->sortable()->searchable(),
                TextColumn::make('tanggal_mulai')->label('Mulai')->date(),
                TextColumn::make('tanggal_akhir')->label('Akhir')->date(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Aktif',
                        'danger' => 'Berakhir',
                    ]),
            ])
            ->defaultSort('tanggal_mulai', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        in_array($record->status_verifikasi, ['menunggu']) &&
                            in_array(auth()->user()->role, ['operasional_hr'])
                    ),

                Action::make('preview_files')
                    ->label('Dokumen')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Dokumen')
                    ->modalContent(fn($record) => view('filament.resources.permintaan-resource.preview', [
                        'files' => $record->files,
                    ]))
                    ->visible(fn($record) => !empty($record->files)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKontrakKerjas::route('/'),
            'create' => Pages\CreateKontrakKerja::route('/create'),
            'edit' => Pages\EditKontrakKerja::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        return match ($user->role) {
            'customer' => parent::getEloquentQuery()->where('customer_id', $user->id),
            default => parent::getEloquentQuery(),
        };
    }
}
