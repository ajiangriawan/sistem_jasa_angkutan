<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanResource\Pages;
use App\Models\Permintaan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use App\Models\Rute;
use App\Filament\Resources\JadwalPengirimanResource;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Facades\Filament;


class PermintaanResource extends Resource
{
    protected static ?string $model = Permintaan::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Permintaan Customer';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['customer', 'pemasaran_cs', 'operasional_pengiriman', 'akuntan']);
    }


    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Hidden::make('customer_id')
                ->default(fn() => Auth::id())
                ->required(),

            Placeholder::make('name')
                ->label('Nama Perusahaan')
                ->content(function ($get, $record = null) {
                    return $record?->customer?->name
                        ?? Auth::user()?->name
                        ?? '-';
                }),

            Select::make('rute_id')
                ->label('Rute Pengiriman')
                ->relationship(
                    'rute',
                    'nama_rute',
                    fn($query) => Auth::user()?->role === 'customer'
                        ? $query->where('customer_id', Auth::id())
                        : $query
                )
                ->searchable()
                ->preload()
                ->required()
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $rute = Rute::find($state);
                    $set('jarak_km', $rute?->jarak_km ? "{$rute->jarak_km} km" : null);
                    $set('harga_rute', $rute ? 'Rp ' . number_format($rute->harga, 0, ',', '.') : null);
                    $set('uang_jalan_rute', $rute ? 'Rp ' . number_format($rute->uang_jalan, 0, ',', '.') : null);
                    $set('bonus_rute', $rute ? 'Rp ' . number_format($rute->bonus, 0, ',', '.') : null);
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

            Forms\Components\TextInput::make('jumlah_unit')
                ->label('Jumlah Unit')
                ->numeric()
                ->minValue(1)
                ->default(1)
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
                ->visible(fn() => in_array(Auth::user()?->role, ['pemasaran_cs'])),

            Placeholder::make('komentar')
                ->label('Komentar Verifikasi')
                ->content(fn($record) => $record?->komentar_verifikasi ?? '-')


        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Permintaan::query()
                    ->orderByRaw("
            CASE 
                WHEN status_verifikasi = 'selesai' THEN 1
                ELSE 0
            END ASC
        ")
                    ->orderBy('tanggal_permintaan', 'desc');

                $user = Auth::user();

                if ($user->role === 'customer') {
                    $query->where('customer_id', $user->id);
                }

                if ($user->role === 'akuntan') {
                    $query->whereDoesntHave('invoice'); // ✅ hanya tampilkan yang belum punya invoice
                }

                return $query;
            })

            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('tanggal_permintaan')
                    ->label('Tanggal')
                    ->date(),

                Tables\Columns\TextColumn::make('rute.nama_rute')
                    ->label('Rute')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_unit')
                    ->label('Jumlah'),

                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->label('Status')
                    ->formatStateUsing(function (string $state): string {
                        return ucfirst($state); // Tampilkan status dengan huruf besar depan
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            'pending' => 'warning',
                            'disetujui' => 'primary',
                            'dijadwalkan' => 'gray',
                            'Dalam Proses' => 'info',
                            'Sebagian Berjalan' => 'info',
                            'selesai' => 'success',
                            'ditolak' => 'danger',
                            'Belum Ada Detail' => 'danger',
                            default => 'gray',
                        };
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        in_array($record->status_verifikasi, ['pending']) &&
                            in_array(auth()->user()->role, ['pemasaran_cs', 'operasional_pengiriman', 'customer'])
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
                            in_array(Auth::user()->role, ['pemasaran_cs', 'operasional_pengiriman'])
                    )
                    ->form([
                        Forms\Components\Textarea::make('komentar_verifikasi')
                            ->label('Komentar (Opsional)')
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->action(function (array $data, Permintaan $record) {
                        // Update status permintaan
                        $record->update([
                            'status_verifikasi' => 'disetujui',
                            'komentar_verifikasi' => $data['komentar_verifikasi'] ?? null,
                        ]);

                        // Kirim notifikasi ke operasional_pengiriman
                        $users = User::where('role', 'operasional_pengiriman')->get();
                        foreach ($users as $user) {
                            Notification::make()
                                ->title('Permintaan Disetujui')
                                ->success()
                                ->icon('heroicon-o-check-circle')
                                ->body("Permintaan dari {$record->customer->name} telah disetujui. Silakan atur jadwal pengiriman.")
                                ->actions([
                                    NotificationAction::make('Lihat')
                                        ->url(self::getUrl('view', ['record' => $record]))
                                        ->button(),
                                ])
                                ->sendToDatabase($user);
                        }
                    }),

                Action::make('buat_jadwal')
                    ->label('Buat Jadwal')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->visible(
                        fn($record) =>
                        $record->status_verifikasi === 'disetujui' &&
                            in_array(Auth::user()->role, ['operasional_pengiriman'])
                    )
                    ->url(
                        fn($record) =>
                        JadwalPengirimanResource::getUrl('create', [
                            'permintaan_id' => $record->id
                        ])
                    ),

                Action::make('invoice')
                    ->label('+ Uang Jalan')
                    ->icon('heroicon-o-rectangle-stack')
                    ->color('primary')
                    ->visible(
                        fn($record) =>
                        $record->status_verifikasi === 'selesai' &&
                            in_array(Auth::user()->role, ['akuntan'])
                    )
                    ->url(
                        fn($record) =>
                        InvoiceResource::getUrl('create', [
                            'permintaan_id' => $record->id
                        ])
                    ),


                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(
                        fn($record) =>
                        $record->status_verifikasi === 'pending'
                            && in_array(request()->user()->role, ['pemasaran_cs', 'operasional_pengiriman'])
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

                        // Kirim notifikasi ke customer yang membuat permintaan
                        $customer = $record->customer;
                        if ($customer) {
                            Notification::make()
                                ->title('Permintaan Ditolak')
                                ->danger()
                                ->icon('heroicon-o-x-circle')
                                ->body("Permintaan pengiriman Anda telah ditolak oleh tim pemasaran. Silakan periksa detailnya.")
                                ->actions([
                                    NotificationAction::make('Lihat')
                                        ->url(self::getUrl('view', ['record' => $record]))
                                        ->button(),
                                ])
                                ->sendToDatabase($customer);
                        }
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

    public static function getNavigationBadge(): ?string
    {
        $user = Filament::auth()->user();

        if (!$user) return null;

        if (in_array($user->role, ['pemasaran_cs', 'operasional_pengiriman'])) {
            return (string) Permintaan::whereIn('status_verifikasi', ['pending', 'disetujui'])->count();
        }

        if ($user->role === 'akuntan') {
            return (string) Permintaan::where('status_verifikasi', 'selesai')
                ->whereDoesntHave('invoice')
                ->count();
        }

        return null;
    }
}
