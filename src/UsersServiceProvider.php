<?php

namespace Nikservik\Users;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Nikservik\Commons\Has;
use Nikservik\Users\Blessings\UserBlessingsChanged;
use Nikservik\Users\Blessings\UserBlessingsChangedListener;
use Nikservik\Users\Middleware\HasAdminRole;
use Nikservik\Users\Middleware\UserBlessedTo;

class UsersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        self::registerListener();
        self::registerBladeBlessed();
        $this->registerMiddleware();

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/cohorts.php' => config_path('cohorts.php'),
            __DIR__ . '/../config/users.php' => config_path('users.php'),
        ], 'users-config');
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'users-migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/users.php', 'users');
        $this->mergeConfigFrom(__DIR__ . '/../config/cohorts.php', 'cohorts');
    }

    public static function registerListener()
    {
        if (Has::feature('users', 'register-event-listener')) {
            Event::listen(
                UserBlessingsChanged::class,
                UserBlessingsChangedListener::class
            );
        }
    }

    public static function registerBladeBlessed()
    {
        if (Has::feature('users', 'register-blade-directives')) {
            Blade::if('blessed', function ($blessing) {
                return Auth::check() && Auth::user()->blessedTo($blessing);
            });
        }
    }

    protected function registerMiddleware()
    {
        if (Has::feature('users', 'register-middleware')) {
            $router = $this->app->make(Router::class);
            $router->aliasMiddleware('blessed', UserBlessedTo::class);
            $router->aliasMiddleware('admin', HasAdminRole::class);
        }
    }
}
