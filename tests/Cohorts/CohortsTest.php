<?php


namespace Nikservik\Users\Tests\Cohorts;


use Illuminate\Support\Facades\Event;
use Nikservik\Users\Cohorts\Cohort;
use Nikservik\Users\Tests\TestCase;
use Nikservik\Users\Blessings\UserBlessingsChanged;

class CohortsTest extends TestCase
{

    public function testInCohort()
    {
        $this->assertTrue($this->user->inCohort('testing'));
        $this->assertFalse($this->user->inCohort('unexistant'));
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
        $this->user->addToCohort('testing');

        Event::assertNotDispatched(UserBlessingsChanged::class);
    }

    public function testRemoveFromCohort()
    {
        $this->user->removeFromCohort('testing');

        $this->assertFalse($this->user->inCohort('testing'));
    }

    public function testRemoveFromCohortEventFired()
    {
        Event::fake();
        $this->user->removeFromCohort('testing');

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
        ray($this->user->cohorts);
        $this->assertCount(1, $this->user->cohorts);
        $this->assertInstanceOf(Cohort::class, $this->user->cohorts[0]);
        $this->assertEquals('testing', $this->user->cohorts[0]->name);
    }

    public function testGetCohortsAttributeDontGetMore()
    {
        Cohort::create(['name' => 'onemore']);

        $this->assertCount(1, $this->user->cohorts);
    }

}
