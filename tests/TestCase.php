<?php

namespace Nikservik\Users\Tests;

use Nikservik\Users\Cohorts\Cohort;
use Nikservik\Users\UsersServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected TestUser $user;
    protected Cohort $cohort;

    protected function getPackageProviders($app)
    {
        return [
            UsersServiceProvider::class,
            TestServiceProvider::class,
        ];
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->user = TestUser::create([
            'name' => 'bob',
            'password' => 'password',
            'email' => 'test@example.com',
            'blessings' => '["blessing1","blessing2"]',
            'cohorts' => '["testing"]',
        ]);

        $this->cohort = Cohort::create([
            'name' => 'testing',
            'blessings' => ["blessing1"],
        ]);
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function defineRoutes($router)
    {
        $router->get('', function () {
            return view('users::blessed');
        });
    }
}
