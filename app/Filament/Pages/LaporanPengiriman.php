<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

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

                Forms\Components\Select::make('customer_id')
                    ->label('Pilih Customer')
                    ->options(User::where('role', 'customer')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
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
                ->action(function () {
                    $state = $this->form->getState();

                    if (empty($state['tanggal_awal']) || empty($state['tanggal_akhir'])) {
                        Notification::make()
                            ->title('Tanggal belum lengkap')
                            ->danger()
                            ->body('Silakan isi tanggal mulai dan tanggal akhir terlebih dahulu.')
                            ->send();

                        return;
                    }

                    $url = route('laporan.export', [
                        'tanggal_awal' => $state['tanggal_awal'],
                        'tanggal_akhir' => $state['tanggal_akhir'],
                        'customer_id' => $state['customer_id'] ?? null,
                    ]);

                    return redirect($url);
                }),
        ];
    }
}
