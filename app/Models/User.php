<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_activity',
        'is_online',
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
            'last_activity' => 'datetime',
            'is_online' => 'boolean',
        ];
    }

    /**
     * Override remember_token set
     */
    /**
     * Override remember_token set
     */
    public function setRememberToken($value)
    {
        // Eğer boş geldiyse biz kendimiz base16 token üretelim
        if (empty($value)) {
            $value = bin2hex(random_bytes(16)); // 32 karakter hex token
        }

        $this->attributes[$this->getRememberTokenName()] = $value;
    }
}
