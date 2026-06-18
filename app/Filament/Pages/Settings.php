<?php

namespace App\Filament\Pages;

use App\Enums\StorageDriver;
use App\Services\StorageService;
use App\Settings\AppSettings;
use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class Settings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Settings';

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

                Section::make('Storage')
                    ->description('Configure storage for file uploads (attachments).')
                    ->schema([
                        Select::make('upload_disk')
                            ->label('Upload Disk')
                            ->required()
                            ->live()
                            ->native(false)
                            ->allowHtml()
                            ->options(
                                fn (): array => collect(StorageDriver::cases())
                                    ->mapWithKeys(fn ($case) => [
                                        $case->value => '<div class="p-1">'.$case->getLabel().'<br><span class="text-xs text-gray-500 dark:text-gray-400">'.$case->getDescription().'</span></div>',
                                    ])
                                    ->toArray()
                            )
                            ->belowLabel(
                                'Choose where new file attachments will be stored. Existing attachments keep their original storage location.'
                            ),

                        Section::make('S3 Configuration')
                            ->description('Enter your Amazon S3 or compatible object storage credentials.')
                            ->visible(fn ($get): bool => $get('upload_disk') === StorageDriver::S3->value)
                            ->icon('phosphor-cloud-bold')
                            ->columns(2)
                            ->schema([
                                TextInput::make('s3_key')
                                    ->label('Access Key ID')
                                    ->required(fn ($get): bool => $get('upload_disk') === StorageDriver::S3->value)
                                    ->maxLength(255),

                                TextInput::make('s3_secret')
                                    ->label('Secret Access Key')
                                    ->required(fn ($get): bool => $get('upload_disk') === StorageDriver::S3->value)
                                    ->maxLength(255)
                                    ->password()
                                    ->revealable(),

                                TextInput::make('s3_region')
                                    ->label('Region')
                                    ->helperText('Defaults to us-east-1. Change only if your bucket is in a different region.')
                                    ->placeholder('us-east-1')
                                    ->maxLength(255),

                                TextInput::make('s3_bucket')
                                    ->label('Bucket')
                                    ->required(fn ($get): bool => $get('upload_disk') === StorageDriver::S3->value)
                                    ->placeholder('e.g., my-school-uploads')
                                    ->maxLength(255),

                                TextInput::make('s3_endpoint')
                                    ->label('Endpoint')
                                    ->placeholder('e.g., https://s3.ap-southeast-1.amazonaws.com')
                                    ->url()
                                    ->maxLength(255),

                                TextInput::make('s3_url')
                                    ->label('Bucket URL')
                                    ->placeholder('e.g., https://my-bucket.s3.amazonaws.com')
                                    ->url()
                                    ->maxLength(255),

                                Toggle::make('s3_use_path_style_endpoint')
                                    ->label('Use Path Style Endpoint')
                                    ->helperText('Enable for MinIO, DigitalOcean Spaces, or compatible S3 providers.')
                                    ->inline(false)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Test Connection')
                            ->visible(fn ($get): bool => $get('upload_disk') === StorageDriver::S3->value)
                            ->schema([
                                Actions::make([
                                    Action::make('test_s3_connection')
                                        ->label('Test S3 Connection')
                                        ->action(function (Get $get) {
                                            $settings = app(AppSettings::class);
                                            $settings->s3_key = $get('s3_key');
                                            $settings->s3_secret = $get('s3_secret');
                                            $settings->s3_region = $get('s3_region');
                                            $settings->s3_bucket = $get('s3_bucket');
                                            $settings->s3_endpoint = $get('s3_endpoint');
                                            $settings->s3_url = $get('s3_url');
                                            $settings->s3_use_path_style_endpoint = $get('s3_use_path_style_endpoint');

                                            try {
                                                $storage = app(StorageService::class);
                                                $storage->disk()->put('_test_connection.txt', 'test');
                                                $storage->disk()->delete('_test_connection.txt');

                                                Notification::make()
                                                    ->success()
                                                    ->title('Connection Successful')
                                                    ->body('Successfully connected to S3 and verified read/write access.')
                                                    ->send();
                                            } catch (\Throwable $e) {
                                                Notification::make()
                                                    ->danger()
                                                    ->title('Connection Failed')
                                                    ->body($e->getMessage())
                                                    ->send();
                                            }
                                        }),
                                ]),
                            ]),
                    ]),

                Section::make('Security')
                    ->description('Manage encryption and security settings for public searches.')
                    ->schema([
                        Toggle::make('encryption_enabled')
                            ->label('Encrypt search payloads')
                            ->inline(false)
                            ->helperText(
                                'When enabled, search requests and responses on the public page will be encrypted using RSA + AES.'
                            ),
                    ]),
            ]);
    }
}
