<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $slug = 'pages';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $modelLabel = 'Halaman';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Page::class, 'slug', ignoreRecord: true),
                            ]),
                    ])
                    ->columnSpan(['default' => 'full', 'xl' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo')
                                    ->helperText('Jika kosong, maka menggunakan logo situs.')
                                    ->image()
                                    ->optimize('webp')
                                    ->directory('pages'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Dipublikasikan')
                                    ->default(true),
                            ]),
                    ]),

                Forms\Components\Section::make('Kolom Tabel')
                    ->description('Kolom diurutan pertama akan digunakan sebagai identifikasi pencarian.')
                    ->schema([
                        Forms\Components\Repeater::make('columns')
                            ->hiddenLabel()
                            ->required()
                            ->cloneable()
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Label kolom')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }

                                        $set('key', Str::slug($state));
                                    }),

                                Forms\Components\Hidden::make('key')
                                    ->label('Kunci')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->label('Tipe')
                                    ->required()
                                    ->native(false)
                                    ->options([
                                        'number' => 'Numerik',
                                        'text' => 'Teks Biasa',
                                        'password' => 'Teks Terenkripsi',
                                        'url' => 'Tautan',
                                        'attachment' => 'Lampiran (Tautan)',
                                        'boolean' => 'Boolean',
                                    ]),

                                Forms\Components\Radio::make('is_required')
                                    ->label('Wajib Diisi')
                                    ->boolean()
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->default(true),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpan('full'),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()->native(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }
}
