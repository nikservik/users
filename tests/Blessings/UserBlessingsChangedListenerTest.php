<?php

namespace Nikservik\Users\Tests\Blessings;

use Illuminate\Support\Facades\Queue;
use Nikservik\Users\Blessings\UpdateBlessings;
use Nikservik\Users\Blessings\UserBlessingsChanged;
use Nikservik\Users\Blessings\UserBlessingsChangedListener;
use Nikservik\Users\Tests\TestCase;

class UserBlessingsChangedListenerTest extends TestCase
{
    public function testHandle()
    {
        Queue::fake();

        (new UserBlessingsChangedListener)
            ->handle(new UserBlessingsChanged($this->user));

        Queue::assertPushed(UpdateBlessings::class);
    }

    public function testListenerRegistered()
    {
        Queue::fake();

        $this->user->addToCohort('new');

        Queue::assertPushed(UpdateBlessings::class);
    }
}
