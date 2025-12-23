<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// NEW:
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // было: use HasFactory, Notifiable;
    use HasApiTokens, HasFactory, Notifiable, HasRoles; // NEW

    protected $fillable = [
        'name',
        'email',
        'password',
        // 'phone', // добавим поле позже вместе с миграцией
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client()
    {
        return $this->hasOne(\App\Models\Client::class);
    }
}
