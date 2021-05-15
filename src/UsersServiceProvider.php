<?php

namespace Nikservik\Users;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Nikservik\Users\Blessings\UserBlessingsChanged;
use Nikservik\Users\Blessings\UserBlessingsChangedListener;

class UsersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        self::registerListener();
        self::registerBladeBlessed();

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/users.php' => config_path('users.php'),
        ], 'users-config');
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'users-migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/users.php', 'users');
    }

    public static function registerListener()
    {
        if (in_array('register-event-listener', Config::get('users.features'))) {
            Event::listen(
                UserBlessingsChanged::class,
                UserBlessingsChangedListener::class
            );
        }
    }

    public static function registerBladeBlessed()
    {
        if (in_array('register-blade-directives', Config::get('users.features'))) {
            Blade::if('blessed', function ($blessing) {
                return Auth::check() && Auth::user()->blessedTo($blessing);
            });
        }
    }
}
