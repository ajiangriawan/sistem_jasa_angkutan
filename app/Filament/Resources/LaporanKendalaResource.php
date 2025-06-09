<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKendalaResource\Pages;
use App\Filament\Resources\LaporanKendalaResource\RelationManagers;
use App\Models\LaporanKendala;
use App\Models\JadwalCekKendaraan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;


class LaporanKendalaResource extends Resource
{
    protected static ?string $model = LaporanKendala::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Laporan Kendala';
    protected static ?string $navigationGroup = 'Operasional';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['operasional_sopir', 'operasional_transportasi', 'operasional_pengiriman']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function form(Form $form): Form
    {
        $user = Auth::user();
        return $form
            ->schema([
                Hidden::make('sopir_id')->default($user->id),

                Select::make('kategori')
                    ->label('Kategori Kendala')
                    ->options([
                        'kerusakan_kendaraan' => 'Kerusakan Kendaraan',
                        'umum' => 'Lainnya'
                    ])
                    ->required(),

                Textarea::make('deskripsi')
                    ->label('Deskripsi Kendala')
                    ->required(),

                Textarea::make('alamat')
                    ->label('Alamat / Tempat')
                    ->required(),

                FileUpload::make('foto_kendala')
                    ->label('Foto Kendala')
                    ->disk('public')
                    ->directory('kendala')
                    ->multiple()
                    ->image()
                    ->preserveFilenames()
                    ->downloadable()
                    ->reorderable()
                    ->nullable(),

                Select::make('status')
                    ->options([
                        'dilaporkan' => 'Dilaporkan',
                        'ditindaklanjuti' => 'Ditindak Lanjuti',
                        'selesai' => 'Selesai',
                    ])
                    ->default('dilaporkan')
                    ->disabled(fn() => $user->role === 'operasional_sopir'),

                Textarea::make('tanggapan')
                    ->label('Tanggapan/Tindakan')
                    ->maxLength(500)
                    ->visible(fn() => in_array($user->role, ['operasional_transportasi']))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sopir.name')->label('Pelapor')->searchable(),
                TextColumn::make('kategori')->label('Kategori'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'dilaporkan' => 'gray',
                        'ditindaklanjuti' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->label('Tanggal Lapor')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(
                        fn($record) =>
                        $record->status === 'dilaporkan' &&
                            (
                                Auth::user()->id === $record->user_id ||
                                in_array(Auth::user()->role, ['customer'])
                            )
                    ),

                Action::make('preview_files')
                    ->label('Dokumen')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Bukti')
                    ->modalContent(fn($record) => view('filament.resources.permintaan-resource.preview', [
                        'files' => $record->foto_kendala,
                    ]))
                    ->visible(fn($record) => !empty($record->foto_kendala)),

                Action::make('lihat_tindak_lanjut')
                    ->label('Lihat Tindak Lanjut')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->modalHeading('Riwayat Tindak Lanjut')
                    ->modalContent(function ($record) {
                        $tindakLanjutList = $record->tindakLanjut()->with('user')->latest()->get();

                        return view('filament.resources.laporan-kendala.tindak-lanjut-modal', compact('tindakLanjutList'));
                    })
                    ->visible(fn($record) => $record->tindakLanjut()->exists())
                    ->color('gray'),

                Action::make('lihat_detail')
                    ->label('Detail Penugasan')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detail Jadwal Pengecekan')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->form(fn(LaporanKendala $record) => [
                        Grid::make(2)->schema([
                            Placeholder::make('nama_sopir')
                                ->label('Nama Sopir')
                                ->content($record->sopir->name ?? '-'),

                            Placeholder::make('telepon')
                                ->label('Telepon Sopir')
                                ->content($record->sopir->telepon ?? '-'),

                            Placeholder::make('deskripsi')
                                ->label('Deskripsi Kendala')
                                ->content($record->deskripsi ?? '-'),

                            Placeholder::make('alamat')
                                ->label('Alamat / Posisi')
                                ->content($record->alamat ?? '-'),

                            Placeholder::make('kendaraan')
                                ->label('Kendaraan')
                                ->content(function () use ($record) {
                                    $kendaraan = optional(
                                        \App\Models\PasanganSopirKendaraan::with('kendaraan')
                                            ->where('driver_id', $record->sopir_id)
                                            ->first()
                                    )->kendaraan;

                                    return $kendaraan ? "{$kendaraan->merk} - {$kendaraan->no_polisi}" : '-';
                                }),

                            Placeholder::make('jadwal')
                                ->label('Jadwal Pengecekan')
                                ->content(optional($record->jadwalCek?->jadwal)->format('d M Y H:i') ?? '-'),

                            Placeholder::make('teknisi')
                                ->label('Teknisi')
                                ->content($record->jadwalCek?->teknisi->name ?? '-'),

                            Placeholder::make('status')
                                ->label('Status')
                                ->content(ucfirst($record->jadwalCek->status ?? '-')),

                            Placeholder::make('hasil_cek')
                                ->label('Hasil Cek')
                                ->content($record->jadwalCek->hasil_cek ?? '-'),
                        ])
                    ]),

                Action::make('tindaklanjuti')
                    ->label('Tindak Lanjuti')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->visible(function ($record) {
                        $user = Auth::user();
                        return $record->status === 'dilaporkan' && (
                            ($record->kategori === 'kerusakan_kendaraan' && $user->role === 'operasional_transportasi') ||
                            ($record->kategori === 'umum' && $user->role === 'operasional_pengiriman')
                        );
                    })
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Tindak Lanjut')
                            ->maxLength(500)
                            ->required(),
                    ])
                    ->action(function (array $data, LaporanKendala $record) {
                        $user = Auth::user();

                        // Simpan catatan tindak lanjut
                        \App\Models\TindakLanjutKendala::create([
                            'laporan_id' => $record->id,
                            'user_id' => $user->id,
                            'catatan' => $data['catatan'] ?? '',
                        ]);

                        // Ubah status sesuai kategori
                        $newStatus = $record->kategori === 'umum' ? 'selesai' : 'ditindaklanjuti';

                        $record->update([
                            'status' => $newStatus,
                        ]);
                    })

            ])
            ->recordUrl(fn($record) => static::getUrl('view', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKendalas::route('/'),
            'create' => Pages\CreateLaporanKendala::route('/create'),
            'edit' => Pages\EditLaporanKendala::route('/{record}/edit'),
            'view' => Pages\ViewLaporanKendala::route('/{record}'),
        ];
    }
}
