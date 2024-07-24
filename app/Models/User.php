<?php

namespace App\Models;

use App\Traits\Avatarable;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasAvatar
{
    use Avatarable;
    use HasApiTokens;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'login',
        'email',
        'password',
        'name',
        'enhancer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'enhancer_id',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
