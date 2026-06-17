<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum FieldType: int implements HasColor, HasIcon, HasLabel
{
    case Text = 1;
    case Date = 2;
    case Link = 3;
    case Attachment = 4;
    case Boolean = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Date => 'Date',
            self::Link => 'Link',
            self::Attachment => 'Attachment',
            self::Boolean => 'Boolean',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Text => 'gray',
            self::Date => 'warning',
            self::Link => 'info',
            self::Attachment => 'primary',
            self::Boolean => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Text => 'heroicon-o-document-text',
            self::Date => 'heroicon-o-calendar',
            self::Link => 'heroicon-o-link',
            self::Attachment => 'heroicon-o-paper-clip',
            self::Boolean => 'heroicon-o-check-circle',
        };
    }
}
