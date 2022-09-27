<?php

namespace Nikservik\Users\Tests\Blessings;

use Illuminate\Support\Facades\Config;
use Nikservik\Users\Contracts\BlesserInterface;
use Nikservik\Users\Tests\TestCase;
use Nikservik\Users\Tests\TestUser;

class DefaultBlessingsTest extends TestCase
{
    protected int $userId;

    public function setUp(): void
    {
        parent::setUp();

        $user = TestUser::create([
            'name' => 'default',
            'password' => 'password',
            'email' => 'default@example.com',
        ]);

        $this->userId = $user->id;
    }

    public function test_loads_default_blessings()
    {
        $user = TestUser::findOrFail($this->userId);

        $this->assertTrue($user->blessedTo('default'));
    }

    /**
     * Чтобы этот тест проходил, нужно в конфиге выключить фичу загрузки благословений по умолчанию
     * @return void
     */
    public function __test_does_not_load_default_blessings_when_feature_off()
    {
        Config::set('users.features', ['create-blessings-attribute', 'create-admin-role-attribute', 'create-cohorts-attribute',]);

        $user = TestUser::findOrFail($this->userId);

        $this->assertFalse($user->blessedTo('default'));
    }
}
