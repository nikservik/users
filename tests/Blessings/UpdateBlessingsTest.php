<?php

namespace Nikservik\Users\Tests\Blessings;

use Nikservik\Users\Blessings\UpdateBlessings;
use Nikservik\Users\Tests\TestCase;
use Nikservik\Users\Tests\TestUser;

class UpdateBlessingsTest extends TestCase
{
    public function testUpdateBlessingsOnSampleUser()
    {
        (new UpdateBlessings($this->user))->handle();

        $this->assertTrue($this->user->blessedTo('blessing1'));
        $this->assertFalse($this->user->blessedTo('blessing2'));
    }

    public function testMixedBlessings()
    {
        $user = TestUser::create([
            'name' => 'bob',
            'password' => 'password',
            'email' => 'test2@example.com',
            'cohorts' => '["mixed-cohort-1","mixed-cohort-2"]',
            'blessings' => json_encode([]),
        ]);

        (new UpdateBlessings($user))->handle();

        $this->assertTrue($user->blessedTo('mixed1'));
        $this->assertTrue($user->blessedTo('mixed2'));
        $this->assertTrue($user->blessedTo('mixed3'));
        $this->assertCount(3, json_decode($user->blessings, true));
    }
}
