<?php

namespace Nikservik\Users\Tests\Blessings;

use Nikservik\Users\Contracts\BlesserInterface;
use Nikservik\Users\Tests\TestCase;
use Nikservik\Users\Tests\TestUser;

class BlessingsTest extends TestCase
{
    public function testBlessedTo()
    {
        $this->assertTrue($this->user->blessedTo('blessing1'));
        $this->assertFalse($this->user->blessedTo('unexistant'));
    }

    public function testSetBlessings()
    {
        $this->user->setBlessings(['new1', 'new2']);

        $this->assertTrue($this->user->blessedTo('new1'));
        $this->assertTrue($this->user->blessedTo('new2'));
        $this->assertFalse($this->user->blessedTo('blessing1'));
    }

    public function testGetBlessers()
    {
        $this->assertCount(1, $this->user->getBlessers());

        foreach ($this->user->getBlessers() as $blesser) {
            $this->assertInstanceOf(BlesserInterface::class, $blesser);
        }
    }

    public function test_where_blessed_to_scope()
    {
        $this->user->setBlessings(['new1', 'new2']);
        $this->user->save();

        $this->assertCount(0, TestUser::whereBlessedTo('unexistent')->get());
        $this->assertCount(1, TestUser::whereBlessedTo('new2')->get());
    }

    public function test_or_where_blessed_to_scope()
    {
        $this->user->setBlessings(['new1', 'new2']);
        $this->user->save();

        $this->assertCount(0, TestUser::orWhereBlessedTo('unexistent')->get());
        $this->assertCount(1, TestUser::whereBlessedTo('unexistent')->orWhereBlessedTo('new2')->get());
    }
}
