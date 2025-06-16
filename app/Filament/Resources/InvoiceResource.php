<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Permintaan;
use App\Models\Deposit;
use App\Models\User;
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
        return Auth::check() && in_array(Auth::user()->role, ['akuntan']);
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
                ->default(fn() => request()->get('permintaan_id')) // <-- Ini penting
                ->options(function (Forms\Get $get) {
                    $selectedId = $get('permintaan_id');
                    $today = Carbon::today();

                    $permintaanQuery = Permintaan::query()
                        ->where('status_verifikasi', 'selesai')
                        ->whereDoesntHave('invoice')
                        ->with(['customer', 'rute']);

                    if ($selectedId) {
                        $permintaanQuery->orWhere('id', $selectedId); // biar tetap muncul
                    }

                    return $permintaanQuery->get()->mapWithKeys(function ($p) {
                        $label = "{$p->customer->name} - {$p->rute->nama_rute} - " . Carbon::parse($p->tanggal_permintaan)->format('d-m-Y');
                        return [$p->id => $label];
                    })->toArray();
                })
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, Set $set) {
                    $permintaan = Permintaan::with([
                        'rute',
                        'jadwalPengiriman.detailJadwal.pasangan.sopir'
                    ])->find($state);

                    $uangJalan = $permintaan?->rute?->uang_jalan ?? 0;
                    $jumlahUnitTerkirim = $permintaan?->jadwalPengiriman
                        ?->flatMap(fn($jadwal) => $jadwal->detailJadwal)->count() ?? 0;

                    $total = $uangJalan * $jumlahUnitTerkirim;

                    $set('total_uang_jalan', $total);
                    $set('customer_id', $permintaan?->customer_id);
                }),

            Forms\Components\Hidden::make('customer_id')
                ->default(function () {
                    $permintaan = \App\Models\Permintaan::find(request()->get('permintaan_id'));
                    return $permintaan?->customer_id;
                }),

            // Rincian Sopir
            Forms\Components\Group::make([
                Forms\Components\Fieldset::make('Rincian Sopir dan Uang Jalan')
                    ->visible(fn(Get $get) => !empty($get('permintaan_id')))
                    ->schema(function (Get $get) {
                        $permintaan = Permintaan::with([
                            'jadwalPengiriman.detailJadwal.pasangan.sopir',
                            'rute'
                        ])->find($get('permintaan_id'));

                        if (!$permintaan) {
                            return [
                                Forms\Components\Placeholder::make('no_request')
                                    ->label('')
                                    ->content('Pilih permintaan terlebih dahulu.')
                            ];
                        }

                        $uangJalan = $permintaan->rute->uang_jalan ?? 0;

                        $sopirList = collect();

                        foreach ($permintaan->jadwalPengiriman as $jadwal) {
                            foreach ($jadwal->detailJadwal as $detail) {
                                $sopir = $detail->pasangan->sopir ?? null;
                                if ($sopir) {
                                    $sopirList->push($sopir);
                                }
                            }
                        }

                        if ($sopirList->isEmpty()) {
                            return [
                                Forms\Components\Placeholder::make('no_drivers')
                                    ->label('')
                                    ->content('Tidak ada sopir yang dijadwalkan.')
                                    ->columnSpanFull()
                            ];
                        }

                        return $sopirList->unique('id')->map(function ($sopir) use ($uangJalan) {
                            return Grid::make(2)->schema([
                                Placeholder::make('nama')
                                    ->label('Nama Sopir')
                                    ->content($sopir->name),
                                Placeholder::make('bank')
                                    ->label('Bank')
                                    ->content($sopir->bank),
                                Placeholder::make('no_rekening')
                                    ->label('No Rekening')
                                    ->content($sopir->no_rekening),
                                Placeholder::make('uang_jalan')
                                    ->label('Uang Jalan')
                                    ->content('Rp ' . number_format((float) $uangJalan, 0, ',', '.')),
                            ])->columns(2);
                        })->toArray();
                    })
                    ->columns(1)
                    ->reactive()
            ]),

            Forms\Components\TextInput::make('total_uang_jalan')
                ->label('Total Uang Jalan')
                ->readOnly()
                ->prefix('Rp')
                ->formatStateUsing(fn($state) => number_format((int) $state, 0, ',', '.'))
                ->default(function () {
                    $permintaan = \App\Models\Permintaan::with('rute', 'jadwalPengiriman.detailJadwal')->find(request()->get('permintaan_id'));
                    if (!$permintaan) return 0;

                    $uangJalan = $permintaan->rute->uang_jalan ?? 0;
                    $jumlah = $permintaan->jadwalPengiriman?->flatMap(fn($j) => $j->detailJadwal)->count() ?? 0;

                    return $uangJalan * $jumlah;
                }),

            Forms\Components\FileUpload::make('bukti_pembayaran')
                ->label('Bukti Pembayaran')
                ->directory('bukti-invoice')
                ->acceptedFileTypes(['image/*', 'application/pdf'])
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
                //Tables\Columns\TextColumn::make('permintaan.id')->label('ID Permintaan'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('permintaan.rute.nama_rute')->label('Nama Rute')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('total_uang_jalan')->money('IDR'),
                Tables\Columns\TextColumn::make('sisa_deposit_setelah_invoice')->label('Sisa Deposit')->money('IDR'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'menunggu_persetujuan',
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                    ])
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->dateTime('d M Y'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('preview_files')
                    ->label('Bukti Transfer')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Bukti')
                    ->modalContent(fn($record) => view('filament.modals.bukti-viewer', [
                        'files' => $record->bukti_pembayaran,
                    ]))
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

        if ($user && $user->role === 'customer') {
            return parent::getEloquentQuery()->where('customer_id', $user->id);
        }

        return parent::getEloquentQuery();
    }
}
