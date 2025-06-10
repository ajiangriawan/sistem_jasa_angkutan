<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalCekKendaraanResource\Pages;
use App\Models\JadwalCekKendaraan;
use App\Models\PermintaanCekKendaraan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanKendala;
use App\Models\PasanganSopirKendaraan;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;


class JadwalCekKendaraanResource extends Resource
{
    protected static ?string $model = JadwalCekKendaraan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Jadwal Pengecekan';
    protected static ?string $navigationGroup = 'Bengkel';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_bengkel', 'operasional_teknisi']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function canCreate(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_bengkel']);
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_bengkel']);
    }


    public static function canDelete($record): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_bengkel']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('permintaan_id')
                ->label('Permintaan Pengecekan')
                ->relationship('permintaan', 'id')
                ->getOptionLabelFromRecordUsing(
                    fn($record) => ($record->laporan?->sopir?->name ?? '-') . ' - ' . $record->created_at->format('d M Y')
                )
                ->default(fn() => request()->get('permintaan_id'))
                ->required()
                ->preload()
                ->searchable()
                ->live(),

            Section::make('Laporan Kendala')
                ->description('Detail laporan dari permintaan pengecekan')
                ->visible(fn($get) => filled($get('permintaan_id')))
                ->schema([
                    Grid::make(2)->schema([
                        Placeholder::make('nama_sopir')
                            ->label('Nama Sopir')
                            ->content(function ($get) {
                                $laporan = PermintaanCekKendaraan::with('laporan.sopir')->find($get('permintaan_id'))?->laporan;
                                return $laporan?->sopir?->name ?? '-';
                            }),

                        Placeholder::make('telepon_sopir')
                            ->label('Telepon Sopir')
                            ->content(function ($get) {
                                $laporan = PermintaanCekKendaraan::with('laporan.sopir')->find($get('permintaan_id'))?->laporan;
                                return $laporan?->sopir?->telepon ?? '-';
                            }),

                        Placeholder::make('deskripsi')
                            ->label('Deskripsi Kendala')
                            ->content(function ($get) {
                                $permintaan = PermintaanCekKendaraan::with('laporan')->find($get('permintaan_id'));
                                return $permintaan?->laporan?->deskripsi ?? '-';
                            }),

                        Placeholder::make('alamat')
                            ->label('Posisi / Alamat')
                            ->content(function ($get) {
                                $permintaan = PermintaanCekKendaraan::with('laporan')->find($get('permintaan_id'));
                                return $permintaan?->laporan?->alamat ?? '-';
                            }),

                        Placeholder::make('merk_kendaraan')
                            ->label('Merk Kendaraan')
                            ->content(function ($get) {
                                $laporan = PermintaanCekKendaraan::with('laporan')->find($get('permintaan_id'))?->laporan;
                                if (!$laporan || !$laporan->sopir_id) return '-';

                                $kendaraan = optional(
                                    PasanganSopirKendaraan::with('kendaraan')
                                        ->where('driver_id', $laporan->sopir_id)
                                        ->first()
                                )->kendaraan;

                                return $kendaraan?->merk ?? 'Tidak ditemukan kendaraan.';
                            }),

                        Placeholder::make('no_polisi')
                            ->label('Nomor Polisi')
                            ->content(function ($get) {
                                $laporan = PermintaanCekKendaraan::with('laporan')->find($get('permintaan_id'))?->laporan;
                                if (!$laporan || !$laporan->sopir_id) return '-';

                                $kendaraan = optional(
                                    PasanganSopirKendaraan::with('kendaraan')
                                        ->where('driver_id', $laporan->sopir_id)
                                        ->first()
                                )->kendaraan;

                                return $kendaraan?->no_polisi ?? '-';
                            }),
                    ])
                ])
                ->columns(1)
                ->collapsible(),

            Select::make('teknisi_id')
                ->label('Teknisi')
                ->relationship(
                    'teknisi',
                    'name',
                    modifyQueryUsing: fn($query) =>
                    $query->where('role', 'operasional_teknisi')->where('status', 'aktif')
                )
                ->searchable()
                ->preload()
                ->required(),

            DateTimePicker::make('jadwal')
                ->label('Tanggal & Waktu Pengecekan')
                ->required(),

            Select::make('status')
                ->label('Status Jadwal')
                ->options([
                    'terjadwal' => 'Jadwalkan',
                    'selesai' => 'Selesai',
                ])
                ->default('terjadwal')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = JadwalCekKendaraan::query()
                    ->orderByRaw("
        FIELD(status, 'terjadwal', 'selesai')
    ")
                    ->orderBy('created_at', 'desc');


                return $query;
            })
            ->columns([
                TextColumn::make('permintaan.laporan.created_at')
                    ->label('Tanggal Laporan')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('jadwal')
                    ->label('Jadwal Cek')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('teknisi.name')
                    ->label('Teknisi'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'terjadwal' => 'info',
                        'selesai' => 'success',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Action::make('lihat_detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Jadwal Pengecekan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->form(fn(JadwalCekKendaraan $record) => [
                        Grid::make(2)->schema([ // Kamu bisa ubah ke Grid::make(3) jika mau 3 kolom
                            Placeholder::make('nama_sopir')
                                ->label('Nama Sopir')
                                ->content($record->permintaan->laporan->sopir->name ?? '-'),

                            Placeholder::make('telepon')
                                ->label('Telepon Sopir')
                                ->content($record->permintaan->laporan->sopir->telepon ?? '-'),

                            Placeholder::make('deskripsi')
                                ->label('Deskripsi Kendala')
                                ->content($record->permintaan->laporan->deskripsi ?? '-'),

                            Placeholder::make('alamat')
                                ->label('Alamat / Posisi')
                                ->content($record->permintaan->laporan->alamat ?? '-'),

                            Placeholder::make('kendaraan')
                                ->label('Kendaraan')
                                ->content(function () use ($record) {
                                    $kendaraan = optional(optional(
                                        \App\Models\PasanganSopirKendaraan::with('kendaraan')
                                            ->where('driver_id', $record->permintaan->laporan->sopir_id)
                                            ->first()
                                    )->kendaraan);

                                    return $kendaraan ? "{$kendaraan->merk} - {$kendaraan->no_polisi}" : '-';
                                }),

                            Placeholder::make('jadwal')
                                ->label('Jadwal Pengecekan')
                                ->content($record->jadwal->format('d M Y H:i')),

                            Placeholder::make('teknisi')
                                ->label('Teknisi')
                                ->content($record->teknisi->name ?? '-'),

                            Placeholder::make('status')
                                ->label('Status')
                                ->content(ucfirst($record->status)),

                            Placeholder::make('hasil_cek')
                                ->label('Hasil Cek')
                                ->content($record->hasil_cek),
                        ]),
                    ]),

                Tables\Actions\EditAction::make(),

                Action::make('tandai_selesai')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function ($record) {
                        return Auth::check()
                            && Auth::user()->role === 'operasional_teknisi'
                            && $record->status === 'terjadwal';
                    })
                    ->form([
                        Forms\Components\Textarea::make('hasil_cek')
                            ->label('Hasil Cek')
                            ->required()
                            ->maxLength(500)
                            ->nullable(),
                    ])
                    ->action(function (JadwalCekKendaraan $record, array $data) {
                        // Update status dan hasil cek jadwal
                        $record->update([
                            'status' => 'selesai',
                            'hasil_cek' => $data['hasil_cek'],
                        ]);

                        // Ambil laporan kendala yang terkait melalui permintaan
                        $laporan = $record->permintaan?->laporan;
                        $permintaan = $record->permintaan;
                        $teknisi = $record->teknisi;


                        if ($laporan) {
                            $laporan->update([
                                'status' => 'selesai',
                            ]);
                        }
                        if ($permintaan) {
                            $permintaan->update([
                                'status' => 'selesai',
                            ]);
                        }
                        if ($teknisi) {
                            $teknisi->update([
                                'status' => 'aktif',
                            ]);
                        }
                    }),

                Action::make('batalkan')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn($record) => $record->status === 'terjadwal')
                    ->action(fn(JadwalCekKendaraan $record) => $record->update(['status' => 'dibatalkan'])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalCekKendaraans::route('/'),
            'create' => Pages\CreateJadwalCekKendaraan::route('/create'),
            'edit' => Pages\EditJadwalCekKendaraan::route('/{record}/edit'),
        ];
    }
}
