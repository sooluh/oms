<?php

namespace App\Filament\Pages;

use App\Settings\AppSettings;
use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageAppSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'App Settings';

    protected static string|BackedEnum|null $navigationIcon = Phosphor::GearSixBold;

    protected static ?int $navigationSort = 1;

    protected static string $settings = AppSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('General')
                    ->description('Manage your school information and homepage content.')
                    ->schema([
                        TextInput::make('school_name')
                            ->label('School Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('school_website')
                            ->label('School Website')
                            ->url()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title')
                            ->label('Homepage Title')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('subtitle')
                            ->label('Homepage Subtitle')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Security')
                    ->description('Manage encryption and security settings for public searches.')
                    ->schema([
                        Toggle::make('encryption_enabled')
                            ->label('Encrypt search payloads')
                            ->helperText('When enabled, search requests and responses on the public page will be encrypted using RSA + AES.')
                            ->inline(false),
                    ]),
            ]);
    }
}
