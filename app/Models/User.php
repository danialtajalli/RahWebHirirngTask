<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = ["name", "email", "password", "role"];
    protected $casts = [
        'role' => UserRole::class,
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
