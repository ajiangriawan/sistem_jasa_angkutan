<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanResource\Pages;
use App\Models\Permintaan;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use App\Models\Rute;
use Filament\Forms\Components\Placeholder;


class PermintaanResource extends Resource
{
    protected static ?string $model = Permintaan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Permintaan Customer';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'operational', 'customer']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Hidden::make('customer_id')
                ->default(fn() => Customer::where('user_id', Auth::id())->value('id'))
                ->required(),

            Placeholder::make('nama_perusahaan')
                ->label('Nama Perusahaan')
                ->content(function ($get, $record = null) {
                    if ($record !== null && $record->customer) {
                        return $record->customer->nama_perusahaan;
                    }
                    // On create, get nama_perusahaan from Customer related to logged in user
                    return Customer::where('user_id', Auth::id())->value('nama_perusahaan') ?? '-';
                }),

            // Pilihan Rute
            Select::make('rute_id')
                ->label('Rute Pengiriman')
                ->relationship(
                    'rute',
                    'nama_rute',
                    fn($query) => Auth::user()?->role === 'customer'
                        ? $query->where('customer_id', Customer::where('user_id', Auth::id())->value('id'))
                        : $query
                )
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $rute = Rute::find($state);

                    if ($rute) {
                        $set('jarak_km', $rute->jarak_km . ' km');
                        $set('harga_rute', 'Rp ' . number_format($rute->harga, 0, ',', '.'));
                        $set('uang_jalan_rute', 'Rp ' . number_format($rute->uang_jalan, 0, ',', '.'));
                        $set('bonus_rute', 'Rp ' . number_format($rute->bonus, 0, ',', '.'));
                    } else {
                        $set('jarak_km', null);
                        $set('harga_rute', null);
                        $set('uang_jalan_rute', null);
                        $set('bonus_rute', null);
                    }
                }),

            // Informasi Rute: Jarak, Harga, Uang Jalan, Bonus
            Forms\Components\Card::make()
                ->schema([
                    Placeholder::make('jarak_km')
                        ->label('Jarak')
                        ->content(function ($get, $record) {
                            if ($get('jarak_km')) return $get('jarak_km');
                            return $record?->rute?->jarak_km ? $record->rute->jarak_km . ' km' : '-';
                        }),

                    Placeholder::make('harga_rute')
                        ->label('Harga')
                        ->content(function ($get, $record) {
                            if ($get('harga_rute')) return $get('harga_rute');
                            return $record?->rute?->harga ? 'Rp ' . number_format($record->rute->harga, 0, ',', '.') : '-';
                        }),

                    Placeholder::make('uang_jalan_rute')
                        ->label('Uang Jalan')
                        ->content(function ($get, $record) {
                            if ($get('uang_jalan_rute')) return $get('uang_jalan_rute');
                            return $record?->rute?->uang_jalan ? 'Rp ' . number_format($record->rute->uang_jalan, 0, ',', '.') : '-';
                        }),

                    Placeholder::make('bonus_rute')
                        ->label('Bonus')
                        ->content(function ($get, $record) {
                            if ($get('bonus_rute')) return $get('bonus_rute');
                            return $record?->rute?->bonus ? 'Rp ' . number_format($record->rute->bonus, 0, ',', '.') : '-';
                        }),
                ])
                ->columns(2)
                ->visible(fn($get) => $get('rute_id') !== null)
                ->label('Informasi Rute')
                ->reactive(),

            Forms\Components\DatePicker::make('tanggal_permintaan')
                ->label('Tanggal Permintaan')
                ->required(),

            Forms\Components\TextInput::make('estimasi_tonase')
                ->label('Estimasi Tonase (ton)')
                ->numeric()
                ->required(),

            Forms\Components\FileUpload::make('dokumen_pendukung')
                ->label('Dokumen Pendukung (PDF atau Gambar)')
                ->multiple()
                ->directory('dokumen-permintaan')
                ->preserveFilenames()
                ->maxFiles(5)
                ->maxSize(10240)
                ->acceptedFileTypes(['application/pdf', 'image/*']),

            Forms\Components\Hidden::make('status_verifikasi')
                ->default('pending'),

            Forms\Components\Textarea::make('komentar_verifikasi')
                ->label('Komentar Verifikasi')
                ->maxLength(500)
                ->nullable()
                ->helperText('Opsional, hanya diisi oleh tim support.')
                ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operational'])),

            Placeholder::make('komentar')
                ->label('Komentar Verifikasi')
                ->content(fn($record) => $record?->komentar_verifikasi ?? '-')


        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama_perusahaan')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal_permintaan')
                    ->label('Tanggal')
                    ->date(),

                Tables\Columns\TextColumn::make('rute.nama_rute')
                    ->label('Rute')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->label('Status')
                    ->formatStateUsing(function (string $state): string {
                        return ucfirst($state); // Tampilkan status dengan huruf besar depan
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'pending' => 'warning',
                            'disetujui' => 'primary',
                            'dijadwalkan' => 'grey', 
                            'pengambilan' => 'info', 
                            'pengantaran' => 'info',
                            'selesai' => 'success',
                            'ditolak' => 'danger',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        !in_array($record->status_verifikasi, ['disetujui', 'ditolak']) &&
                            in_array(auth()->user()->role, ['admin', 'operational'])
                    ),

                Action::make('preview_files')
                    ->label('Dokumen')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Dokumen')
                    ->modalContent(fn($record) => view('filament.resources.permintaan-resource.preview', [
                        'files' => $record->dokumen_pendukung,
                    ]))
                    ->visible(fn($record) => !empty($record->dokumen_pendukung)),

                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(
                        fn($record) =>
                        $record->status_verifikasi === 'pending' &&
                            in_array(Auth::user()->role, ['admin', 'operational'])
                    )

                    ->form([
                        Forms\Components\Textarea::make('komentar_verifikasi')
                            ->label('Komentar (Opsional)')
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->action(function (array $data, Permintaan $record) {
                        $record->update([
                            'status_verifikasi' => 'disetujui',
                            'komentar_verifikasi' => $data['komentar_verifikasi'] ?? null,
                        ]);
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(
                        fn($record) =>
                        $record->status_verifikasi === 'pending'
                            && in_array(request()->user()->role, ['admin', 'operational'])
                    )
                    ->form([
                        Forms\Components\Textarea::make('komentar_verifikasi')
                            ->label('Komentar (Opsional)')
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->action(function (array $data, Permintaan $record) {
                        $record->update([
                            'status_verifikasi' => 'ditolak',
                            'komentar_verifikasi' => $data['komentar_verifikasi'] ?? null,
                        ]);
                    }),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => auth()->user()/*->role === 'admin'*/),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermintaans::route('/'),
            'create' => Pages\CreatePermintaan::route('/create'),
            'edit' => Pages\EditPermintaan::route('/{record}/edit'),
            'view' => Pages\ViewPermintaan::route('/{record}'),
        ];
    }
}
