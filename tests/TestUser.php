<?php


namespace Nikservik\Users\Tests;

use Illuminate\Foundation\Auth\User;

/**
 * Класс пользователя только для тестирования пакета
 * @property string $user_settings
 */
class TestUser extends User
{
    protected $table = 'users';

    protected $fillable = ['user_settings'];
}
