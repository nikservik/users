<?php

namespace Nikservik\Users;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Nikservik\Users\Blessings\UserBlessingsChanged;
use Nikservik\Users\Blessings\UserBlessingsChangedListener;

class UsersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        self::registerListener();

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/users.php' => config_path('users.php'),
        ], 'users-config');

        if (! class_exists('UpdateUsersTableWithRights')) {
            $this->publishes([
                __DIR__.'/../database/migrations/update_users_table_with_blessings.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_update_users_table_with_blessings.php'),
            ], 'users-migration');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/users.php', 'users');
    }

    public static function registerListener()
    {
        Event::listen(
            UserBlessingsChanged::class,
            UserBlessingsChangedListener::class
        );
    }

    public static function registerBladeBlessed()
    {
        Blade::if('blessed', function ($blessing) {
            return Auth::check() && Auth::user()->blessedTo($blessing);
        });
    }
}
