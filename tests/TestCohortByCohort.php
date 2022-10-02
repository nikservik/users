<?php

namespace Nikservik\Users\Tests;

use Nikservik\Users\Cohorts\Cohort;

class TestCohortByCohort extends Cohort
{
    /**
     * В тестовый скоуп попадают пользователи, которые состоят в когорте to-add
     * @param $query
     * @return void
     */
    protected function scope($query): void
    {
        $query->whereJsonContains('cohorts', 'to-add');
    }
}
