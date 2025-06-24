<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Permintaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Actions\Action;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Invoice Pengiriman';
    protected static ?string $modelLabel = 'Invoice';

    public static function canViewAny(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['akuntan', 'operasional_sopir', 'customer']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::canViewAny();
    }

    public static function canCreate(): bool
    {
        return Auth::check() && Auth::user()->role === 'akuntan';
    }

    public static function canEdit($record): bool
    {
        return Auth::check() && Auth::user()->role === 'akuntan';
    }

    public static function canDelete($record): bool
    {
        return Auth::check() && Auth::user()->role === 'akuntan';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('permintaan_id')
                ->label('Permintaan Pengiriman')
                ->default(fn() => request()->get('permintaan_id'))
                ->options(function (Forms\Get $get) {
                    $selectedId = $get('permintaan_id');
                    $query = Permintaan::query()
                        ->where('status_verifikasi', 'selesai')
                        ->whereDoesntHave('invoice')
                        ->with(['customer', 'rute']);

                    if ($selectedId) {
                        $query->orWhere('id', $selectedId);
                    }

                    return $query->get()->mapWithKeys(function ($p) {
                        return [
                            $p->id => "{$p->customer->name} - {$p->rute->nama_rute} - " . Carbon::parse($p->tanggal_permintaan)->format('d-m-Y')
                        ];
                    })->toArray();
                })
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Set $set) {
                    $permintaan = Permintaan::with(['rute', 'jadwalPengiriman.detailJadwal.pasangan.sopir', 'jadwalPengiriman.detailJadwal.pengiriman'])->find($state);

                    $uangJalan = $permintaan?->rute?->uang_jalan ?? 0;
                    $bonusPerTon = $permintaan?->rute?->bonus ?? 0;

                    $totalUangJalan = 0;

                    foreach ($permintaan->jadwalPengiriman as $jadwal) {
                        foreach ($jadwal->detailJadwal as $detail) {
                            $tonase = $detail->pengiriman->tonase ?? 0;
                            $bonus = max(0, $tonase - 30) * $bonusPerTon;
                            $totalUangJalan += $uangJalan + $bonus;
                        }
                    }

                    $set('total_uang_jalan', $totalUangJalan);
                    $set('customer_id', $permintaan?->customer_id);
                })
                ->columnSpanFull(),

            Forms\Components\Hidden::make('customer_id')
                ->default(fn() => Permintaan::find(request()->get('permintaan_id'))?->customer_id)
                ->columnSpanFull(),

            Forms\Components\Group::make([
                Forms\Components\Fieldset::make('Rincian Sopir dan Pengiriman')
                    ->visible(fn(Get $get) => filled($get('permintaan_id')))
                    ->schema(function (Get $get) {
                        $permintaan = Permintaan::with([
                            'rute',
                            'jadwalPengiriman.detailJadwal.pasangan.sopir',
                        ])->find($get('permintaan_id'));

                        if (!$permintaan) {
                            return [Placeholder::make('no_request')->content('Pilih permintaan terlebih dahulu.')];
                        }

                        $uangJalan = $permintaan->rute->uang_jalan ?? 0;
                        $bonusPerTon = $permintaan->rute->bonus ?? 0;

                        $grouped = collect();

                        foreach ($permintaan->jadwalPengiriman as $jadwal) {
                            foreach ($jadwal->detailJadwal as $detail) {
                                $sopir = optional(optional($detail->pasangan)->sopir);

                                $pengiriman = $detail->pengiriman;

                                if (!$sopir->id || !$pengiriman?->tanggal) {
                                    continue;
                                }

                                $grouped->push([
                                    'sopir_id' => $sopir->id,
                                    'sopir_nama' => $sopir->name,
                                    'bank' => $sopir->bank,
                                    'rekening' => $sopir->no_rekening,
                                    'tanggal' => $pengiriman->tanggal,
                                    'tonase' => $pengiriman->tonase ?? 0,
                                ]);
                            }
                        }

                        if ($grouped->isEmpty()) {
                            return [Placeholder::make('no_data')->content('Tidak ada data sopir atau pengiriman yang valid.')];
                        }

                        $schemas = [];

                        foreach ($grouped->groupBy('sopir_id') as $sopirId => $items) {
                            $info = $items->first();

                            $schemas[] = Forms\Components\Section::make("Sopir: {$info['sopir_nama']}")
                                ->description("Bank: {$info['bank']} | Rekening: {$info['rekening']}")
                                ->schema(
                                    collect($items)->map(function ($item) use ($uangJalan, $bonusPerTon, $sopirId) {
                                        $bonus = max(0, $item['tonase'] - 30) * $bonusPerTon;
                                        $total = $uangJalan + $bonus;

                                        return Grid::make(5)->schema([
                                            Placeholder::make('tanggal')
                                                ->label('Tanggal')
                                                ->content(Carbon::parse($item['tanggal'])->format('d-m-Y')),
                                            Placeholder::make('tonase')
                                                ->label('Tonase (ton)')
                                                ->content(number_format($item['tonase'], 2, ',', '.')),
                                            Placeholder::make("uang_jalan_{$sopirId}")
                                                ->label('Uang Jalan')
                                                ->content('Rp ' . number_format($uangJalan, 0, ',', '.')),
                                            Placeholder::make('bonus')
                                                ->label('Bonus (jika tonase > 30)')
                                                ->content('Rp ' . number_format($bonus, 0, ',', '.')),
                                            Placeholder::make('total')
                                                ->label('Total')
                                                ->content('Rp ' . number_format($total, 0, ',', '.')),
                                        ]);
                                    })->toArray()
                                );
                        }

                        return $schemas;
                    })
                    ->columns(1)
                    ->reactive(),
            ])->columnSpanFull(),

            Forms\Components\TextInput::make('total_uang_jalan')
                ->label('Total yang Harus Dibayarkan')
                ->readOnly()
                ->prefix('Rp')
                ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.'))
                ->default(function () {
                    $permintaan = \App\Models\Permintaan::with(['rute', 'jadwalPengiriman.detailJadwal.pengiriman'])->find(request()->get('permintaan_id'));
                    if (!$permintaan) return 0;

                    $uangJalan = $permintaan->rute->uang_jalan ?? 0;
                    $bonusPerTon = $permintaan->rute->bonus ?? 0;

                    $totalUangJalan = 0;

                    foreach ($permintaan->jadwalPengiriman as $jadwal) {
                        foreach ($jadwal->detailJadwal as $detail) {
                            $tonase = $detail->pengiriman->tonase ?? 0;
                            $bonus = max(0, $tonase - 30) * $bonusPerTon;
                            $totalUangJalan += $uangJalan + $bonus;
                        }
                    }

                    return $totalUangJalan;
                }),

            Forms\Components\FileUpload::make('bukti_pembayaran')
                ->label('Bukti Pembayaran')
                ->directory('bukti-invoice')
                ->acceptedFileTypes(['image/*', 'application/pdf'])
                ->multiple()
                ->downloadable()
                ->reorderable()
                ->maxSize(5120),

            Forms\Components\Textarea::make('catatan')
                ->label('Catatan Tambahan')
                ->nullable()
                ->rows(3),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('No. Invoice')->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('permintaan.rute.nama_rute')->label('Nama Rute')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('total_uang_jalan')->money('IDR'),
                Tables\Columns\TextColumn::make('sisa_deposit_setelah_invoice')
                    ->label('Sisa Deposit')
                    ->money('IDR')
                    ->visible(fn() => in_array(Auth::user()->role ?? null, ['akuntan', 'customer'])),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'menunggu_persetujuan',
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('preview_files')
                    ->label('Bukti Transfer')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Bukti')
                    ->modalContent(fn($record) => view('filament.modals.bukti-viewer', ['files' => $record->bukti_pembayaran]))
                    ->visible(fn($record) => !empty($record->bukti_pembayaran)),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'cetak-periode' => Pages\CetakInvoicePeriode::route('/cetak-periode'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        // Customer hanya melihat invoice miliknya
        if ($user && $user->role === 'customer') {
            return parent::getEloquentQuery()->where('customer_id', $user->id);
        }

        // Sopir hanya melihat invoice yang terkait dengan pengiriman yang dia lakukan
        if ($user && $user->role === 'operasional_sopir') {
            return parent::getEloquentQuery()
                ->whereHas('permintaan.jadwalPengiriman.detailJadwal.pasangan', function ($query) use ($user) {
                    $query->where('sopir_id', $user->id);
                });
        }

        // Akuntan dan lainnya melihat semua
        return parent::getEloquentQuery();
    }
}
