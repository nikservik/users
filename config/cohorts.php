<?php

return [
    // Когорта для тестов
    'test' => [
        'class' => \Nikservik\Users\Tests\TestCohortByCohort::class,
        'blessings' => [
            'blessing1',
        ],
    ],
    'test-by-role' => [
        'class' => \Nikservik\Users\Tests\TestCohortByAdminRole::class,
        'blessings' => [
            'blessing2',
        ],
    ],
    'bad-class' => [
        'class' => '\\Nikservik\\Users\\Tests\\BadCohort',
        'blessings' => [
            'blessing1',
        ],
    ],
    'mixed-cohort-1' => [
        'class' => \Nikservik\Users\Tests\TestCohortByCohort::class,
        'blessings' => [
            'mixed1', 'mixed2',
        ],
    ],
    'mixed-cohort-2' => [
        'class' => \Nikservik\Users\Tests\TestCohortByCohort::class,
        'blessings' => [
            'mixed2', 'mixed3',
        ],
    ],
];
