<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SearchMode: int implements HasColor, HasIcon, HasLabel
{
    case Or = 1;
    case And = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::Or => 'OR',
            self::And => 'AND',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Or => 'info',
            self::And => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Or => 'heroicon-o-arrows-right-left',
            self::And => 'heroicon-o-equals',
        };
    }
}
