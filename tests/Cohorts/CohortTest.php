<?php

namespace Nikservik\Users\Tests\Cohorts;

use Nikservik\Users\Cohorts\Cohort;
use Nikservik\Users\Tests\TestCase;

class CohortTest extends TestCase
{

    public function testGetBlessings()
    {
        $cohort = Cohort::make([
            'name' => 'test',
            'blessings' => ['blessing1', 'blessing2'],
        ]);

        $this->assertCount(2, $cohort->getBlessings());
        $this->assertEquals(['blessing1', 'blessing2'], $cohort->getBlessings());
    }
}
