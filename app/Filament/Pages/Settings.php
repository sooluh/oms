<?php

namespace App\Filament\Pages;

use App\Settings\AppSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;

class Settings extends SettingsPage
{
    protected static string $settings = AppSettings::class;

    protected static ?string $slug = 'settings';

    protected static ?string $navigationGroup = 'Kelola';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Pengaturan';

    protected static ?int $navigationSort = 2;

    public function getFormActions(): array
    {
        return [];
    }

    public function getHeaderActions(): array
    {
        return [
            $this->getSaveFormAction()->formId('form'),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Umum')
                    ->description('Sesuaikan informasi dasar aplikasi Anda dengan mengisi form di bawah.')
                    ->aside()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama aplikasi')
                            ->required(),

                        Forms\Components\TextInput::make('holder')
                            ->label('Nama sekolah')
                            ->required(),

                        Forms\Components\TextInput::make('region')
                            ->label('Kabupaten/Kota')
                            ->required(),

                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo aplikasi')
                            ->required()
                            ->image()
                            ->optimize('webp'),
                    ]),

                Forms\Components\Section::make('Kontak')
                    ->description('Masukkan detail kontak untuk mempermudah pengguna menghubungi Anda.')
                    ->aside()
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat sekolah')
                            ->required()
                            ->columnSpan('full'),

                        Forms\Components\Repeater::make('phone')
                            ->label('Nomor kontak')
                            ->required()
                            ->minItems(1)
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label('Nomor telepon')
                                    ->helperText('Harus di awali dengan 62')
                                    ->tel()
                                    ->telRegex('/^62(2|8)[1-9][0-9]{6,9}$/')
                                    ->placeholder('62xxxxxxxxxxx')
                                    ->required(),

                                Forms\Components\TextInput::make('note')
                                    ->label('Catatan')
                                    ->placeholder('Misal: WhatsApp'),
                            ])
                            ->columns(),

                        Forms\Components\Repeater::make('email')
                            ->label('Alamat surel')
                            ->required()
                            ->minItems(1)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->hiddenLabel()
                                    ->required()
                                    ->placeholder('surel@contoh.com'),
                            ]),

                        Forms\Components\Repeater::make('social')
                            ->label('Sosial media')
                            ->cloneable()
                            ->defaultItems(1)
                            ->schema([
                                Forms\Components\Select::make('platform')
                                    ->label('Platform')
                                    ->options([
                                        'facebook' => 'Facebook',
                                        'twitter' => 'Twitter',
                                        'instagram' => 'Instagram',
                                        'youtube' => 'YouTube',
                                        'tiktok' => 'TikTok',
                                        'threads' => 'Threads',
                                    ])
                                    ->live()
                                    ->native(false)
                                    ->required(),

                                Forms\Components\TextInput::make('url')
                                    ->label('URL')
                                    ->required()
                                    ->placeholder(function (Get $get) {
                                        $platform = $get('platform');

                                        return match ($platform) {
                                            'facebook' => 'https://www.facebook.com/xxx',
                                            'twitter' => 'https://www.twitter.com/xxx',
                                            'instagram' => 'https://www.instagram.com/xxx',
                                            'youtube' => 'https://www.youtube.com/@xxx',
                                            'tiktok' => 'https://www.tiktok.com/@xxx',
                                            'threads' => 'https://www.threads.net/@xxx',
                                            default => '',
                                        };
                                    })
                                    ->columnSpan(2),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
