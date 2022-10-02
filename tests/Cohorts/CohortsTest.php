<?php


namespace Nikservik\Users\Tests\Cohorts;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Nikservik\Users\Blessings\UserBlessingsChanged;
use Nikservik\Users\Cohorts\Cohort;
use Nikservik\Users\Tests\TestCase;

class CohortsTest extends TestCase
{
    public function testInCohort()
    {
        $this->assertTrue($this->user->inCohort('test'));
        $this->assertFalse($this->user->inCohort('non-existent'));
    }

    public function test_in_cohort_with_empty_cohorts()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->inCohort('test'));
    }

    public function testAddToCohort()
    {
        $this->user->addToCohort('new');

        $this->assertTrue($this->user->inCohort('new'));
    }

    public function testAddToCohortEventFired()
    {
        Event::fake();
        $this->user->addToCohort('new');

        Event::assertDispatched(UserBlessingsChanged::class);
    }

    public function testAddToAlreadyAddedCohortEventNotFired()
    {
        Event::fake();
        $this->user->addToCohort('test');

        Event::assertNotDispatched(UserBlessingsChanged::class);
    }

    public function testRemoveFromCohort()
    {
        $this->user->removeFromCohort('test');

        $this->assertFalse($this->user->inCohort('test'));
    }

    public function testRemoveFromCohortEventFired()
    {
        Event::fake();
        $this->user->removeFromCohort('test');

        Event::assertDispatched(UserBlessingsChanged::class);
    }

    public function testRemoveFromCohortNotInListEventNotFired()
    {
        Event::fake();
        $this->user->removeFromCohort('new');

        Event::assertNotDispatched(UserBlessingsChanged::class);
    }

    public function testGetCohortsAttribute()
    {
        $this->assertCount(1, $this->user->cohorts);
        $this->assertInstanceOf(Cohort::class, $this->user->cohorts[0]);
        $this->assertEquals('test', $this->user->cohorts[0]->getName());
    }
}
