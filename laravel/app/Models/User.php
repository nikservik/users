<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Auth;
use Nikservik\Users\Admin\AdminRoles;
use Nikservik\Users\Blessings\Blessings;

class User extends Auth
{
    use HasFactory;
    use AdminRoles;
    use Blessings;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
