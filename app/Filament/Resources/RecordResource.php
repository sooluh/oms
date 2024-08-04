<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecordResource\Pages;
use App\Models\Record;
use App\Services\RecordService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecordResource extends Resource
{
    protected static ?string $model = Record::class;

    protected static ?string $slug = 'records';

    protected static ?string $recordTitleAttribute = 'identifier';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        $slug = request()->query('slug');
        $record = RecordService::getInstance($slug);

        return $record->getForm($form);
    }

    public static function table(Table $table): Table
    {
        $slug = request()->query('slug');
        $page = RecordService::getInstance($slug)->getPage();

        return $table
            ->columns([
                // TODO:
            ])
            ->filters([
                // TODO:
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecords::route('/'),
            'create' => Pages\CreateRecord::route('/create'),
            'edit' => Pages\EditRecord::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $slug = request()->query('slug');
        $page = RecordService::getInstance($slug)->getPage();

        return parent::getEloquentQuery()
            ->where('page_id', $page->id)
            ->withoutGlobalScope(SoftDeletingScope::class);
    }
}
