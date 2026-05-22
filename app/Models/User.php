<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['full_name', 'username', 'email', 'password', 'balance', 'country_code', 'phone', 'otp_code', 'otp_expires_at', 'otp_attempts', 'otp_last_sent_at', 'last_login', 'level', 'status', 'api_key', 'is_seller', 'koneksi'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
            'otp_last_sent_at' => 'datetime',
        ];
    }
}
