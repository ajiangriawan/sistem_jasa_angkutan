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
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use App\Models\Pengiriman;
use Filament\Forms\Components\FileUpload;

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
                        $today = Carbon::today();
                        return Permintaan::where('status_verifikasi', 'disetujui')
                            ->with('customer', 'rute')
                            ->orderByRaw('ABS(DATEDIFF(tanggal_permintaan, ?))', [$today])
                            ->get()
                            ->mapWithKeys(function ($permintaan) {
                                return [
                                    $permintaan->id => $permintaan->customer->nama_perusahaan
                                        . ' - ' . $permintaan->rute->nama_rute
                                        . ' - ' . Carbon::parse($permintaan->tanggal_permintaan)->format('d-m-Y'),
                                ];
                            });
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $permintaan = Permintaan::with('customer', 'rute')->find($value);
                        if (!$permintaan) return null;

                        return $permintaan->customer->nama_perusahaan
                            . ' - ' . $permintaan->rute->nama_rute
                            . ' - ' . Carbon::parse($permintaan->tanggal_permintaan)->format('d-m-Y');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default(fn($record) => $record?->permintaan_id),


                DatePicker::make('tanggal_berangkat')->label('Tanggal Berangkat')->required(),
                TimePicker::make('jam_berangkat')->label('Jam Berangkat'),
                DatePicker::make('tanggal_tiba')->label('Tanggal Tiba'),
                TimePicker::make('jam_tiba')->label('Jam Tiba'),

                Select::make('driver_id')
                    ->label('Sopir')
                    ->options(function () {
                        return Sopir::with('user')
                            ->where('status', 'aktif')
                            ->get()
                            ->mapWithKeys(fn($s) => [$s->id => $s->user->name]);
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $sopir = Sopir::with('user')->find($value);
                        return $sopir?->user?->name;
                    })
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->default(fn($record) => $record?->driver_id),


                Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->options(function () {
                        return Kendaraan::where('status', 'siap')
                            ->get()
                            ->mapWithKeys(fn($k) => [$k->id => $k->no_polisi . ' - ' . $k->type]);
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $kendaraan = Kendaraan::find($value);
                        return $kendaraan ? $kendaraan->no_polisi . ' - ' . $kendaraan->type : null;
                    })
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->default(fn($record) => $record?->kendaraan_id),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'dijadwalkan' => 'Dijadwalkan',
                        'pengambilan' => 'Pengambillan',
                        'pengantaran' => 'Pengantaran',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('dijadwalkan'),

                Textarea::make('catatan')->label('Catatan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('permintaan.customer.nama_perusahaan')->label('Customer'),
                TextColumn::make('permintaan.rute.nama_rute')->label('Rute'),
                TextColumn::make('tanggal_berangkat')->date(),
                TextColumn::make('jam_berangkat'),
                TextColumn::make('tanggal_tiba')->date()
                    ->visible(fn() => in_array(Auth::user()->role, ['admin', 'operational'])),
                TextColumn::make('jam_tiba')
                    ->visible(fn() => in_array(Auth::user()->role, ['admin', 'operational'])),
                TextColumn::make('sopir.user.name')->label('Sopir')
                    ->visible(fn() => in_array(Auth::user()->role, ['admin', 'operational'])),
                TextColumn::make('kendaraan.no_polisi')->label('Kendaraan'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->visible(fn() => in_array(Auth::user()->role, ['admin', 'operational']))
                    ->color(fn(string $state): string => match ($state) {
                        'dijadwalkan' => 'gray',
                        'pengambilan' => 'info',
                        'pengantaran' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operational'])),

                Action::make('berangkat')
                    ->label('Berangkat')
                    ->visible(function ($record) {
                        return Auth::user()?->role === 'driver' && $record->status === 'dijadwalkan';
                    })
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'pengambilan',
                        ]);

                        // Update status permintaan juga
                        if ($record->permintaan) {
                            $record->permintaan->update(['status_verifikasi' => 'pengambilan']);
                        }
                    }),

                Action::make('pengantaran')
                    ->label('Pengantaran')
                    ->visible(function ($record) {
                        return Auth::user()?->role === 'driver' && $record->status === 'pengambilan';
                    })
                    ->form([
                        Forms\Components\TextInput::make('tonase')
                            ->label('Tonase (Ton)')
                            ->numeric()
                            ->required(),
                        FileUpload::make('surat_jalan')
                            ->label('Surat Jalan')
                            ->required()
                            ->disk('public') // Pastikan disk storage sudah diatur
                            ->directory('pengiriman/surat-jalan'),
                        FileUpload::make('do_muat')
                            ->label('DO Muat')
                            ->required()
                            ->disk('public')
                            ->directory('pengiriman/do-muat'),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->nullable(),
                    ])
                    ->modalHeading('Input Data Pengantaran')
                    ->modalSubmitActionLabel('Kirim')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record, array $data) {
                        // Update status jadwal
                        $record->update(['status' => 'pengantaran']);

                        // Simpan data pengiriman
                        Pengiriman::create([
                            'jadwal_id' => $record->id,
                            'tonase' => $data['tonase'],
                            'surat_jalan' => $data['surat_jalan'],
                            'do_muat' => $data['do_muat'],
                            'catatan' => $data['catatan'] ?? null,
                            'tanggal' => now()->toDateString(),
                        ]);

                        // Update status permintaan
                        if ($record->permintaan) {
                            $record->permintaan->update(['status_verifikasi' => 'pengantaran']);
                        }
                    })
                    ->requiresConfirmation(),


                Action::make('selesai')
                    ->label('Selesai')
                    ->visible(fn($record) => Auth::user()?->role === 'driver' && $record->status === 'pengantaran')
                    ->requiresConfirmation()
                    ->form([
                        FileUpload::make('do_bongkar')
                            ->label('DO Bongkar')
                            ->required()
                            ->disk('public')
                            ->directory('pengiriman/do-bongkar'),

                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->nullable(),

                        Forms\Components\Hidden::make('tanggal_tiba'),
                        Forms\Components\Hidden::make('jam_tiba'),
                    ])
                    ->mountUsing(function (Forms\ComponentContainer $form) {
                        $now = now()->setTimezone('Asia/Jakarta');
                        $form->fill([
                            'tanggal_tiba' => $now->toDateString(),
                            'jam_tiba' => $now->format('H:i'),
                        ]);
                    })
                    ->modalHeading('Konfirmasi Penyelesaian')
                    ->modalSubmitActionLabel('Selesaikan')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record, array $data) {
                        // Update jadwal
                        $record->update([
                            'status' => 'selesai',
                            'tanggal_tiba' => $data['tanggal_tiba'],
                            'jam_tiba' => $data['jam_tiba'],
                        ]);

                        // Update permintaan
                        if ($record->permintaan) {
                            $record->permintaan->update(['status_verifikasi' => 'selesai']);
                        }

                        // Update pengiriman
                        if ($record->pengiriman) {
                            $record->pengiriman->update([
                                'do_bongkar' => $data['do_bongkar'],
                                'catatan' => $data['catatan'] ?? null,
                            ]);
                        }

                        // Update sopir menjadi aktif kembali
                        if ($record->driver_id) {
                            $sopir = \App\Models\Sopir::find($record->driver_id);
                            if ($sopir) {
                                $sopir->update(['status' => 'aktif']);
                            }
                        }
                        if ($record->kendaraan_id) {
                            $kendaraan = \App\Models\Kendaraan::find($record->kendaraan_id);
                            if ($kendaraan) {
                                $kendaraan->update(['status' => 'siap']);
                            }
                        }
                    })
                    ->extraAttributes([
                        'x-init' => <<<JS
            const now = new Date();
            const tgl = now.toISOString().split('T')[0];
            const jam = now.toTimeString().split(':').slice(0,2).join(':');
            document.querySelector('[name="data[tanggal_tiba]"]').value = tgl;
            document.querySelector('[name="data[jam_tiba]"]').value = jam;
        JS,
                    ]),


            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn() => in_array(Auth::user()?->role, ['admin', 'operational'])),
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
}
