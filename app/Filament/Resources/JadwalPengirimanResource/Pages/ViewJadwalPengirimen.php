<?php

namespace App\Filament\Resources\JadwalPengirimanResource\Pages;

use App\Filament\Resources\JadwalPengirimanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Contracts\View\View; // Penting untuk return type di modalContent
use App\Models\Pengiriman; // Pastikan model Pengiriman diimpor

class ViewJadwalPengirimen extends ViewRecord
{
    protected static string $resource = JadwalPengirimanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Bagian Informasi Jadwal
                Components\Section::make('Informasi Jadwal')
                    ->schema([
                        Components\TextEntry::make('permintaan.customer.name')
                            ->label('Customer'),
                        Components\TextEntry::make('permintaan.rute.nama_rute')
                            ->label('Rute'),
                        Components\TextEntry::make('tanggal_berangkat')
                            ->date()->label('Tanggal Berangkat'),
                        Components\TextEntry::make('jam_berangkat')
                            ->label('Jam Berangkat'),
                        Components\TextEntry::make('tanggal_tiba')
                            ->date()->label('Tanggal Tiba'),
                        Components\TextEntry::make('jam_tiba')
                            ->label('Jam Tiba'),
                        Components\TextEntry::make('catatan')
                            ->label('Catatan'),
                    ])->columns(2),

                // Bagian Dokumen Pengiriman (dengan tombol yang memicu modal)
                // Bagian Dokumen Pengiriman
                Components\Section::make('Dokumen Pengiriman')
                    ->schema([
                        Components\Actions::make([
                            // SURAT JALAN
                            Components\Actions\Action::make('view_surat_jalan')
                                ->label('Lihat Surat Jalan')
                                ->icon('heroicon-o-document')
                                ->color('primary')
                                ->modalHeading('Surat Jalan')
                                ->modalContent(function ($record): View {
                                    $dokumen = $record->detailJadwal
                                        ->pluck('surat_jalan')
                                        ->filter()
                                        ->flatten()
                                        ->toArray();

                                    return view('filament.modals.document-viewer-list', [
                                        'dokumenList' => $dokumen,
                                        'documentName' => 'Surat Jalan',
                                    ]);
                                })
                                ->visible(
                                    fn($record) =>
                                    $record->detailJadwal
                                        ->pluck('surat_jalan')
                                        ->filter()
                                        ->flatten()
                                        ->isNotEmpty()
                                ),

                            // DO MUAT
                            Components\Actions\Action::make('view_do_muat')
                                ->label('Lihat DO Muat')
                                ->icon('heroicon-o-document-duplicate')
                                ->color('primary')
                                ->modalHeading('DO Muat')
                                ->modalContent(function ($record): View {
                                    $dokumen = $record->detailJadwal
                                        ->pluck('do_muat')
                                        ->filter()
                                        ->flatten()
                                        ->toArray();

                                    return view('filament.modals.document-viewer-list', [
                                        'dokumenList' => $dokumen,
                                        'documentName' => 'DO Muat',
                                    ]);
                                })
                                ->visible(
                                    fn($record) =>
                                    $record->detailJadwal
                                        ->pluck('do_muat')
                                        ->filter()
                                        ->flatten()
                                        ->isNotEmpty()
                                ),

                            // DO BONGKAR
                            Components\Actions\Action::make('view_do_bongkar')
                                ->label('Lihat DO Bongkar')
                                ->icon('heroicon-o-document-check')
                                ->color('primary')
                                ->modalHeading('DO Bongkar')
                                ->modalContent(function ($record): View {
                                    $dokumen = $record->detailJadwal
                                        ->pluck('do_bongkar')
                                        ->filter()
                                        ->flatten()
                                        ->toArray();

                                    return view('filament.modals.document-viewer-list', [
                                        'dokumenList' => $dokumen,
                                        'documentName' => 'DO Bongkar',
                                    ]);
                                })
                                ->visible(
                                    fn($record) =>
                                    $record->detailJadwal
                                        ->pluck('do_bongkar')
                                        ->filter()
                                        ->flatten()
                                        ->isNotEmpty()
                                ),
                        ])
                            ->columns(3),
                    ]),


                // Bagian Detail Sopir & Kendaraan
                Components\Section::make('Detail Sopir & Kendaraan')
                    ->schema([
                        Components\RepeatableEntry::make('detailJadwal')
                            ->schema([
                                Components\TextEntry::make('pasangan.sopir.name')
                                    ->label('Sopir'),
                                Components\TextEntry::make('pasangan.kendaraan.no_polisi')
                                    ->label('No. Polisi Kendaraan'),
                                Components\TextEntry::make('pasangan.kendaraan.type')
                                    ->label('Tipe Kendaraan'),
                                Components\TextEntry::make('status')
                                    ->label('Status Detail')
                                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                            ])
                            ->columns(2)
                            ->grid(2)
                            ->label('Detail Pasangan'),
                    ]),
            ]);
    }
}
