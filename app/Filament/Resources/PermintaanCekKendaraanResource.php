<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanCekKendaraanResource\Pages;
use App\Models\Kendaraan;
use App\Models\LaporanKendala;
use App\Models\PasanganSopirKendaraan;
use App\Models\PermintaanCekKendaraan;
use App\Models\TindakLanjutKendala;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class PermintaanCekKendaraanResource extends Resource
{
    protected static ?string $model = PermintaanCekKendaraan::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Permintaan Pengecekan';
    protected static ?string $navigationGroup = 'Bengkel';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_transportasi', 'operasional_bengkel']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();

        return $form->schema([
            Select::make('laporan_id')
                ->label('Laporan Kendala')
                ->options(function () {
                    return LaporanKendala::where('status', 'ditindaklanjuti')
                        ->get()
                        ->mapWithKeys(function ($record) {
                            return [
                                $record->id => optional($record->sopir)->name . ' - ' . $record->created_at->format('d M Y H:i'),
                            ];
                        });
                })
                ->getOptionLabelUsing(function ($value): ?string {
                    // Ambil record LaporanKendala berdasarkan $value (ID)
                    $laporanKendala = LaporanKendala::find($value);

                    // Jika record ditemukan, format tampilannya
                    if ($laporanKendala) {
                        return optional($laporanKendala->sopir)->name . ' - ' . $laporanKendala->created_at->format('d M Y H:i');
                    }

                    return null; // Atau string kosong jika record tidak ditemukan
                })
                ->searchable()
                ->required()
                ->preload()
                ->live()
                ->disabled(Auth::user()->role !== 'operasional_transportasi'),

            Section::make('Laporan Kendala')
                ->description('Detail laporan kendala yang dipilih')
                ->schema([
                    Grid::make(2)->schema([
                        Placeholder::make('deskripsi')
                            ->label('Deskripsi Kendala')
                            ->content(
                                fn($get) =>
                                optional(LaporanKendala::find($get('laporan_id')))->deskripsi ?? '-'
                            ),

                        Placeholder::make('alamat')
                            ->label('Posisi / Alamat')
                            ->content(
                                fn($get) =>
                                optional(LaporanKendala::find($get('laporan_id')))->alamat ?? '-'
                            ),

                        Placeholder::make('merk_kendaraan')
                            ->label('Merk Kendaraan')
                            ->content(function ($get) {
                                $laporan = LaporanKendala::find($get('laporan_id'));
                                if (!$laporan || !$laporan->sopir_id) return '-';

                                $kendaraan = optional(
                                    PasanganSopirKendaraan::with('kendaraan')
                                        ->where('driver_id', $laporan->sopir_id)
                                        ->first()
                                )->kendaraan;

                                return $kendaraan->merk ?? 'Tidak ditemukan kendaraan terkait.';
                            }),

                        Placeholder::make('no_polisi')
                            ->label('Nomor Polisi')
                            ->content(function ($get) {
                                $laporan = LaporanKendala::find($get('laporan_id'));
                                if (!$laporan || !$laporan->sopir_id) return '-';

                                $kendaraan = optional(
                                    PasanganSopirKendaraan::with('kendaraan')
                                        ->where('driver_id', $laporan->sopir_id)
                                        ->first()
                                )->kendaraan;

                                return $kendaraan->no_polisi ?? '-';
                            }),
                    ])
                ])
                ->columns(1)
                ->collapsible(),

            Select::make('status')
                ->label('Status Permintaan')
                ->options([
                    'diajukan' => 'Diajukan',
                    'disetujui' => 'Disetujui',
                    'ditolak' => 'Ditolak',
                ])
                ->default('diajukan')
                ->disabled($user->role !== 'operasional_bengkel'),

            Textarea::make('catatan')
                ->label('Catatan Tambahan')
                ->maxLength(500)
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = PermintaanCekKendaraan::query()
                    ->orderByRaw("
        FIELD(status, 'diajukan', 'dikonfirmasi', 'dijadwalkan', 'selesai')
    ")
                    ->orderBy('created_at', 'desc');


                return $query;
            })
            ->columns([
                TextColumn::make('laporan.created_at')
                    ->label('Tanggal Laporan')
                    ->dateTime('d M Y H:i'),

                TextColumn::make('kendaraan.no_polisi')
                    ->label('Kendaraan'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'dikonfirmasi' => 'success',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Tanggal Permintaan')
                    ->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        $record->status === 'diajukan' &&
                            in_array(Auth::user()->role, ['supervisor_bengkel', 'operasional_transportasi'])
                    ),

                Action::make('preview_files')
                    ->label('Bukti')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Bukti')
                    ->modalContent(fn($record) => view('filament.resources.permintaan-resource.preview', [
                        'files' => optional($record->laporan)->foto_kendala,
                    ]))
                    ->visible(
                        fn($record) =>
                        !empty(optional($record->laporan)->foto_kendala)
                    ),

                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(
                        fn($record) =>
                        $record->status === 'diajukan' &&
                            Auth::user()->role === 'operasional_bengkel'
                    )
                    ->action(function (array $data, PermintaanCekKendaraan $record) {
                        // Update status permintaan
                        $record->update(['status' => 'dikonfirmasi']);

                        // (Opsional) juga update status laporan
                        $record->laporan?->update(['status' => 'ditindaklanjuti']);
                    }),
                Action::make('buat_jadwal')
                    ->label('Buat Jadwal')
                    ->icon('heroicon-o-calendar-days')
                    ->color('primary')
                    ->visible(
                        fn($record) =>
                        $record->status === 'dikonfirmasi' &&
                            Auth::user()->role === 'operasional_bengkel'
                    )
                    ->url(
                        fn($record) =>
                        JadwalCekKendaraanResource::getUrl('create', ['permintaan_id' => $record->id])
                    ),
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
            'index' => Pages\ListPermintaanCekKendaraans::route('/'),
            'create' => Pages\CreatePermintaanCekKendaraan::route('/create'),
            'edit' => Pages\EditPermintaanCekKendaraan::route('/{record}/edit'),
        ];
    }
}
