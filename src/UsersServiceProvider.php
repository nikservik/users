<?php

namespace Nikservik\Users;

use Illuminate\Support\ServiceProvider;

class UsersServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/users.php' => config_path('users.php'),
        ], 'users-config');

        if (! class_exists('UpdateUsersTableWithRights')) {
            $this->publishes([
                __DIR__.'/../database/migrations/update_users_table_with_rights.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_update_users_table_with_rights.php'),
            ], 'users-migration');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/users.php', 'users');
    }
}
