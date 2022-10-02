<?php

namespace Nikservik\Users\Tests;

use Nikservik\Users\Cohorts\Cohort;

class TestCohortByAdminRole extends Cohort
{
    /**
     * В тестовый скоуп попадают пользователи, у которых admin_role = 4
     * @param $query
     * @return void
     */
    protected function scope($query): void
    {
        $query->where('admin_role', 4);
    }
}
