<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id'; // Khai báo khóa chính theo ERD

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'point'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}