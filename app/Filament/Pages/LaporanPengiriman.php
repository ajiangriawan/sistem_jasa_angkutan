<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanPengirimanExport;
use Filament\Support\Facades\Browser;


class LaporanPengiriman extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Laporan Pengiriman';
    protected static string $view = 'filament.pages.laporan-pengiriman';

    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(Auth::user()->role, ['admin', 'akuntan', 'operasional_pengiriman']);
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_awal')
                    ->label('Tanggal Mulai')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->required(),
            ])
            ->statePath('data');
    }



    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export ke Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(function () {
                    if (empty($this->data['tanggal_awal']) || empty($this->data['tanggal_akhir'])) {
                        Notification::make()
                            ->title('Tanggal belum lengkap')
                            ->danger()
                            ->body('Silakan isi tanggal mulai dan tanggal akhir terlebih dahulu.')
                            ->send();

                        return null;
                    }

                    return route('laporan.export', [
                        'tanggal_awal' => $this->data['tanggal_awal'],
                        'tanggal_akhir' => $this->data['tanggal_akhir'],
                    ]);
                })
                ->openUrlInNewTab(),
        ];
    }
}
