<?php

namespace Nikservik\Users\Tests\Blessings;


use Nikservik\Users\Contracts\BlesserInterface;
use Nikservik\Users\Tests\TestCase;

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
}
