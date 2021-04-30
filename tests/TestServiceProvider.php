<?php

namespace Nikservik\Users\Tests;

use Illuminate\Support\ServiceProvider;
use Nikservik\Users\UsersServiceProvider;

class TestServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'users');

        UsersServiceProvider::registerBladeBlessed();
    }
}
