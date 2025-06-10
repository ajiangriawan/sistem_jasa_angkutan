<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalPengirimanResource\Pages;
use App\Models\{JadwalPengiriman, Permintaan, Pengiriman, PasanganSopirKendaraan, DetailJadwalPengiriman};
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\ComponentContainer;


class JadwalPengirimanResource extends Resource
{
    protected static ?string $model = JadwalPengiriman::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Jadwal Pengiriman';
    protected static ?string $pluralLabel = 'Jadwal Pengiriman';
    protected static ?string $navigationGroup = 'Manajemen Pengiriman';

    // --- Otorisasi Resource Level (tidak berubah) ---
    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_pengiriman', 'operasional_sopir', 'customer']);
    }

    public static function canCreate(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_pengiriman']);
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'operasional_pengiriman']);
    }

    public static function canView($record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_pengiriman', 'operasional_sopir', 'customer']);
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['admin', 'operasional_pengiriman']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }
    // --- Akhir Otorisasi Resource Level ---


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('permintaan_id')
                ->label('Pilih Permintaan')
                ->options(function () {
                    $today = Carbon::today();
                    return Permintaan::where('status_verifikasi', 'disetujui')
                        ->with(['customer', 'rute'])
                        ->orderByRaw('ABS(DATEDIFF(tanggal_permintaan, ?))', [$today])
                        ->get()
                        ->mapWithKeys(fn($p) => [
                            $p->id => "{$p->customer->name} - {$p->rute->nama_rute} - " . Carbon::parse($p->tanggal_permintaan)->format('d-m-Y')
                        ]);
                })
                ->getOptionLabelUsing(fn($value) => optional(Permintaan::with(['customer', 'rute'])->find($value))->customer->name ?? null)
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($state) {
                        $permintaan = Permintaan::with(['customer', 'rute'])->find($state);
                        if ($permintaan) {
                            $set('permintaan_customer_name', $permintaan->customer->name);
                            $set('permintaan_rute', $permintaan->rute->nama_rute);
                            $set('permintaan_tanggal_permintaan', Carbon::parse($permintaan->tanggal_permintaan)->format('d-m-Y'));
                            $set('permintaan_jumlah_unit', $permintaan->jumlah_unit);
                            $set('permintaan_estimasi', $permintaan->estimasi_tonase);
                            $set('permintaan_komentar_verifikasi', $permintaan->komentar_verifikasi);
                        } else {
                            $set('permintaan_customer_name', null);
                            $set('permintaan_rute', null);
                            $set('permintaan_tanggal_permintaan', null);
                            $set('permintaan_jumlah_unit', null);
                            $set('permintaan_estimasi', null);
                            $set('permintaan_komentar_verifikasi', null);
                        }
                    } else {
                        $set('permintaan_customer_name', null);
                        $set('permintaan_rute', null);
                        $set('permintaan_tanggal_permintaan', null);
                        $set('permintaan_jumlah_unit', null);
                        $set('permintaan_estimasi', null);
                        $set('permintaan_komentar_verifikasi', null);
                    }
                })
                ->afterStateHydrated(function ($state, Set $set) {
                    if ($state) {
                        $permintaan = Permintaan::with(['customer', 'rute'])->find($state);
                        if ($permintaan) {
                            $set('permintaan_customer_name', $permintaan->customer->name);
                            $set('permintaan_rute', $permintaan->rute->nama_rute);
                            $set('permintaan_tanggal_permintaan', Carbon::parse($permintaan->tanggal_permintaan)->format('d-m-Y'));
                            $set('permintaan_jumlah_unit', $permintaan->jumlah_unit);
                            $set('permintaan_estimasi', $permintaan->estimasi_tonase);
                            $set('permintaan_komentar_verifikasi', $permintaan->komentar_verifikasi);
                        }
                    }
                })

                ->columnSpanFull(),

            Section::make('Detail Permintaan')
                ->description('Informasi terkait permintaan yang dipilih.')
                ->visible(fn(Get $get) => (bool) $get('permintaan_id'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Placeholder::make('permintaan_customer_name')
                                ->label('Customer')
                                ->content(fn(Get $get) => $get('permintaan_customer_name') ?? '-'),
                            Placeholder::make('permintaan_tanggal_permintaan')
                                ->label('Tanggal Permintaan')
                                ->content(fn(Get $get) => $get('permintaan_tanggal_permintaan') ?? '-'),
                            Placeholder::make('permintaan_rute')
                                ->label('Rute')
                                ->content(fn(Get $get) => $get('permintaan_rute') ?? '-'),
                            Placeholder::make('permintaan_jumlah_unit')
                                ->label('Jumlah Unit')
                                ->content(fn(Get $get) => $get('permintaan_jumlah_unit') ?? '-'),
                            Placeholder::make('permintaan_estimasi')
                                ->label('Estimasi Tonase')
                                ->content(fn(Get $get) => $get('permintaan_estimasi') ?? '-'),

                            Placeholder::make('permintaan_komentar_verifikasi')
                                ->label('Komentar Verifikasi')
                                ->content(fn(Get $get) => $get('permintaan_komentar_verifikasi') ?? '-'),
                            //->columnSpanFull(),
                        ]),
                ])
                ->columns(1),


            Forms\Components\DatePicker::make('tanggal_berangkat')->required()->label('Tanggal Berangkat'),
            Forms\Components\TimePicker::make('jam_berangkat')->label('Jam Berangkat'),
            Forms\Components\DatePicker::make('tanggal_tiba')->label('Tanggal Tiba')
                ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operasional_pengiriman'])),
            Forms\Components\TimePicker::make('jam_tiba')->label('Jam Tiba')
                ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operasional_pengiriman'])),
            Forms\Components\Textarea::make('catatan')->label('Catatan'),
            Forms\Components\Repeater::make('detailJadwal')
                ->label('Pasangan Sopir & Kendaraan')
                ->relationship('detailJadwal')
                ->schema([
                    Forms\Components\Select::make('pasangan_sopir_kendaraan_id')
                        ->label('Sopir & Kendaraan')
                        ->options(function () {
                            return PasanganSopirKendaraan::with(['sopir', 'kendaraan'])
                                ->whereHas('sopir', fn($q) => $q->where('role', 'operasional_sopir'))
                                ->whereHas('kendaraan', fn($q) => $q->where('status', 'siap'))
                                ->get()
                                ->mapWithKeys(fn($p) => [
                                    $p->id => "{$p->sopir->name} - {$p->kendaraan->no_polisi} ({$p->kendaraan->type})"
                                ]);
                        })
                        ->searchable()
                        ->preload()
                        ->disableOptionWhen(function ($value, $state, callable $get) {
                            $selectedPasanganIds = collect($get('../../detailJadwal'))
                                ->pluck('pasangan_sopir_kendaraan_id')
                                ->filter()
                                ->values();
                            return $selectedPasanganIds->contains($value);
                        })
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status Detail')
                        ->options([
                            'dijadwalkan' => 'Dijadwalkan',
                            'pengambilan' => 'Pengambilan',
                            'pengantaran' => 'Pengantaran',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                        ])
                        ->default('dijadwalkan')
                        ->required()
                        ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operasional_pengiriman'])),
                ])
                ->createItemButtonLabel('Tambah Pasangan')
                ->columns(1)
                ->required()
                ->visible(fn() => in_array(Auth::user()?->role, ['operasional_pengiriman'])),
        ]);
    }

    public static function table(Table $table): Table
    {
        $currentUser = Auth::user();

        return $table
            ->columns([
                TextColumn::make('permintaan.customer.name')->label('Customer'),
                TextColumn::make('permintaan.rute.nama_rute')->label('Rute'),
                TextColumn::make('tanggal_berangkat')->date(),
                TextColumn::make('jam_berangkat'),
                TextColumn::make('detailJadwal')
                    ->label('Sopir & Status')
                    ->formatStateUsing(fn($record) => $record->detailJadwal->map(
                        fn($d) => "{$d->pasangan->sopir->name} - {$d->pasangan->kendaraan->no_polisi} (<span style='color: " . match ($d->status) {
                            'dijadwalkan' => 'gray',
                            'pengambilan', 'pengantaran' => 'blue',
                            'selesai' => 'green',
                            'dibatalkan' => 'red',
                            default => 'gray',
                        } . "'>" . ucfirst($d->status) . "</span>)"
                    )->implode('<br>'))
                    ->html()
                    ->wrap(),

                BadgeColumn::make('status') // Menggunakan kolom 'status' yang sudah ada
                    ->label('Status')
                    ->formatStateUsing(fn(string $state) => ucfirst($state))
                    ->color(fn(string $state) => match ($state) {
                        'dijadwalkan' => 'gray',
                        'Dalam Proses' => 'info',
                        'selesai' => 'success',
                        'Sebagian Berjalan' => 'warning',
                        'Belum Ada Detail' => 'danger',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('tanggal_tiba')->date()
                    ->visible(fn() => in_array($currentUser?->role, ['admin', 'operasional_pengiriman'])),
                TextColumn::make('jam_tiba')
                    ->visible(fn() => in_array($currentUser?->role, ['admin', 'operasional_pengiriman'])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => static::canEdit($record)),

                Action::make('berangkat')
                    ->label('Berangkat')
                    ->visible(function (JadwalPengiriman $record) {
                        $currentUser = auth()->user();
                        if ($currentUser?->role !== 'operasional_sopir') return false;

                        return $record->detailJadwal
                            ->where('pasangan.sopir.id', $currentUser->id)
                            ->where('status', 'dijadwalkan')
                            ->isNotEmpty();
                    })
                    ->requiresConfirmation()
                    ->action(function (JadwalPengiriman $record) {
                        $currentUser = auth()->user();

                        if ($currentUser?->role !== 'operasional_sopir') {
                            Notification::make()
                                ->title('Akses Ditolak')
                                ->body('Anda bukan sopir.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $detail = $record->detailJadwal
                            ->where('pasangan.sopir.id', $currentUser->id)
                            ->where('status', 'dijadwalkan')
                            ->first();

                        if ($detail) {
                            // Update status detail jadwal
                            $detail->update(['status' => 'pengambilan']);

                            // Update status kendaraan menjadi "beroperasi"
                            $kendaraan = $detail->pasangan->kendaraan ?? null;
                            if ($kendaraan) {
                                $kendaraan->update(['status' => 'beroperasi']);
                            }

                            // Update status jadwal utama (berdasarkan detail jadwal)
                            static::updateJadwalPengirimanStatus($record);

                            // Update status permintaan (opsional)
                            $record->permintaan?->update(['status_verifikasi' => $record->status]);

                            Notification::make()
                                ->title('Status Diubah')
                                ->body('Status diubah menjadi pengambilan. Kendaraan ditandai beroperasi.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal')
                                ->body('Tidak ada jadwal yang dapat diperbarui.')
                                ->warning()
                                ->send();
                        }
                    }),


                // Action Pengantaran
                Action::make('pengantaran')
                    ->label('Pengantaran')
                    ->visible(function (JadwalPengiriman $record) {
                        $currentUser = auth()->user();
                        if ($currentUser?->role !== 'operasional_sopir') return false;
                        return $record->detailJadwal
                            ->where('pasangan.sopir.id', $currentUser->id)
                            ->where('status', 'pengambilan')
                            ->isNotEmpty();
                    })
                    ->form([
                        Forms\Components\TextInput::make('tonase')->numeric()->required()->label('Tonase (Ton)'),
                        Forms\Components\FileUpload::make('surat_jalan')->required()->disk('public')->directory('pengiriman/surat-jalan')->label('Surat Jalan'),
                        Forms\Components\FileUpload::make('do_muat')->required()->disk('public')->directory('pengiriman/do-muat')->label('DO Muat'),
                    ])
                    ->modalHeading('Input Data Pengantaran')
                    ->modalSubmitActionLabel('Kirim')
                    ->modalCancelActionLabel('Batal')
                    ->requiresConfirmation()
                    ->action(function (JadwalPengiriman $record, array $data) {
                        $currentUser = auth()->user();
                        if ($currentUser?->role !== 'operasional_sopir') {
                            Notification::make()->title('Akses Ditolak')->body('Anda bukan sopir.')->danger()->send();
                            return;
                        }

                        $detail = $record->detailJadwal
                            ->where('pasangan.sopir.id', $currentUser->id)
                            ->where('status', 'pengambilan')
                            ->first();

                        if ($detail) {
                            $detail->update([
                                'status' => 'pengantaran',
                                'surat_jalan' => $data['surat_jalan'],
                                'do_muat' => $data['do_muat'],
                            ]);

                            Pengiriman::create([
                                'jadwal_id' => $record->id,
                                'tonase' => $data['tonase'],
                                'tanggal' => now()->toDateString(),
                            ]);

                            static::updateJadwalPengirimanStatus($record);
                            Notification::make()->title('Pengantaran Berhasil')->body('Data pengantaran dicatat.')->success()->send();
                        } else {
                            Notification::make()->title('Gagal')->body('Tidak ada data yang sesuai.')->warning()->send();
                        }
                    }),

                // Action Selesai
                Action::make('selesai')
                    ->label('Selesai')
                    ->visible(function (JadwalPengiriman $record) {
                        $currentUser = auth()->user();
                        if ($currentUser?->role !== 'operasional_sopir') return false;
                        return $record->detailJadwal
                            ->where('pasangan.sopir.id', $currentUser->id)
                            ->where('status', 'pengantaran')
                            ->isNotEmpty();
                    })
                    ->form([
                        Forms\Components\FileUpload::make('do_bongkar')->required()->label('DO Bongkar')->disk('public')->directory('pengiriman/do-bongkar'),
                        Forms\Components\Hidden::make('tanggal_tiba'),
                        Forms\Components\Hidden::make('jam_tiba'),
                    ])
                    ->mountUsing(function (ComponentContainer $form) {
                        $now = now()->setTimezone('Asia/Jakarta');
                        $form->fill([
                            'tanggal_tiba' => $now->toDateString(),
                            'jam_tiba' => $now->format('H:i'),
                        ]);
                    })
                    ->modalHeading('Konfirmasi Penyelesaian')
                    ->modalSubmitActionLabel('Selesaikan')
                    ->modalCancelActionLabel('Batal')
                    ->requiresConfirmation()
                    ->action(function (JadwalPengiriman $record, array $data) {
                        $currentUser = auth()->user();
                        if ($currentUser?->role !== 'operasional_sopir') {
                            Notification::make()->title('Akses Ditolak')->body('Anda bukan sopir.')->danger()->send();
                            return;
                        }

                        $detail = $record->detailJadwal
                            ->where('pasangan.sopir.id', $currentUser->id)
                            ->where('status', 'pengantaran')
                            ->first();

                        if ($detail) {
                            $detail->update([
                                'status' => 'selesai',
                                'do_bongkar' => $data['do_bongkar'],
                            ]);

                             // Update status kendaraan menjadi "siap"
                            $kendaraan = $detail->pasangan->kendaraan ?? null;
                            if ($kendaraan) {
                                $kendaraan->update(['status' => 'siap']);
                            }

                            $record->update([
                                'tanggal_tiba' => $data['tanggal_tiba'],
                                'jam_tiba' => $data['jam_tiba'],
                            ]);

                            static::updateJadwalPengirimanStatus($record);
                            $record->permintaan?->update(['status_verifikasi' => $record->status]);

                            Notification::make()->title('Selesai')->body('Pengiriman telah diselesaikan.')->success()->send();
                        } else {
                            Notification::make()->title('Gagal')->body('Tidak ada pengantaran aktif.')->warning()->send();
                        }
                    }),
            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => in_array($currentUser?->role, ['admin', 'operasional_pengiriman'])),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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

    /**
     * Memperbarui status jadwal pengiriman utama berdasarkan status detail jadwal.
     * Metode ini sekarang statis.
     *
     * @param \App\Models\JadwalPengiriman $record
     * @return void
     */
    protected static function updateJadwalPengirimanStatus(JadwalPengiriman $record): void // <-- Tambah 'static' di sini
    {
        // Muat ulang relasi detailJadwal untuk mendapatkan status terbaru
        // Penting: Gunakan fresh() untuk memastikan data terbaru dari DB
        $record->load('detailJadwal');

        if ($record->detailJadwal->isEmpty()) {
            // Periksa apakah status memang berbeda sebelum update untuk menghindari trigger loop jika observer ada
            if ($record->status !== 'Belum Ada Detail') {
                $record->update(['status' => 'Belum Ada Detail']);
            }
            return;
        }

        $allDetailsCompleted = $record->detailJadwal->every(fn($detail) => $detail->status === 'selesai');
        $allDetailsCancelled = $record->detailJadwal->every(fn($detail) => $detail->status === 'dibatalkan');
        $anyInProgress = $record->detailJadwal->contains(fn($detail) => in_array($detail->status, ['pengambilan', 'pengantaran']));
        $anyScheduled = $record->detailJadwal->contains(fn($detail) => $detail->status === 'dijadwalkan');

        $newStatus = 'Tidak Diketahui'; // Default fallback

        if ($allDetailsCompleted) {
            $newStatus = 'selesai';
        } elseif ($allDetailsCancelled) {
            $newStatus = 'dibatalkan';
        } elseif ($anyInProgress) {
            // Jika ada yang dalam proses, dan ada juga yang masih dijadwalkan, itu berarti sebagian berjalan
            if ($anyScheduled) {
                $newStatus = 'Sebagian Berjalan';
            } else {
                $newStatus = 'Dalam Proses';
            }
        } elseif ($anyScheduled) {
            $newStatus = 'dijadwalkan'; // Hanya ada yang dijadwalkan, belum ada yang mulai
        }

        // Perbarui hanya jika status berubah untuk menghindari pembaruan yang tidak perlu
        if ($record->status !== $newStatus) {
            $record->update(['status' => $newStatus]);
        }
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $currentUser = Auth::user();

        if ($currentUser?->role === 'operasional_sopir') {
            $query->whereHas('detailJadwal.pasangan.sopir', function (Builder $q) use ($currentUser) {
                $q->where('id', $currentUser->id);
            });
        } elseif ($currentUser?->role === 'customer') {
            $query->whereHas('permintaan.customer', function (Builder $q) use ($currentUser) {
                $q->where('id', $currentUser->id);
            });
        }

        $query->orderByRaw("CASE
            WHEN status = 'selesai' THEN 2
            WHEN status = 'dibatalkan' THEN 3
            ELSE 1
        END");
        $query->orderBy('tanggal_berangkat', 'desc');

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        $loggedInUserId = Auth::id();

        $jumlah = DetailJadwalPengiriman::where('status', 'dijadwalkan')
            ->whereHas('pasangan', function ($query) use ($loggedInUserId) {
                $query->where('driver_id', $loggedInUserId);
            })
            ->count();

        return $jumlah > 0 ? (string) $jumlah : null;
    }
}
