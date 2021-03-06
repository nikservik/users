<?php

namespace Nikservik\Users\Tests\Blessings;

use Nikservik\Users\Blessings\UpdateBlessings;
use Nikservik\Users\Cohorts\Cohort;
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
        Cohort::create([
            'name' => 'new1',
            'blessings' => ["bnew1", "bnew2"],
        ]);
        Cohort::create([
            'name' => 'new2',
            'blessings' => ["bnew2", "bnew3"],
        ]);
        $user = TestUser::create([
            'name' => 'bob',
            'password' => 'password',
            'email' => 'test2@example.com',
            'cohorts' => '["new1","new2"]',
        ]);

        (new UpdateBlessings($user))->handle();

        $this->assertTrue($user->blessedTo('bnew1'));
        $this->assertTrue($user->blessedTo('bnew2'));
        $this->assertTrue($user->blessedTo('bnew3'));
    }
}
