<?php

namespace Nikservik\Users\Tests\Cohorts;

use App\Models\User;
use Nikservik\Users\Cohorts\Cohort;
use Nikservik\Users\Cohorts\Exceptions\BadCohortNameException;
use Nikservik\Users\Cohorts\Exceptions\NonExistentCohortClassException;
use Nikservik\Users\Tests\TestCase;

class CohortTest extends TestCase
{
    public function test_make_loads_name_and_blessings()
    {
        $cohort = Cohort::make('test');

        $this->assertNotNull($cohort);
        $this->assertEquals('test', $cohort->getName());
        $this->assertEquals(['blessing1'], $cohort->getBlessings());
    }

    public function test_make_returns_null_for_nonexistent()
    {
        $this->expectException(BadCohortNameException::class);
        Cohort::make('nonexistent');
    }

    public function test_make_returns_null_for_bad_class_in_config()
    {
        $this->expectException(NonExistentCohortClassException::class);
        Cohort::make('bad-class');
    }

    public function test_counts_users()
    {
        // Один пользователь с когортой 'test' создается в setup
        User::factory()->count(2)->create(['cohorts' => '["test"]']);
        // Пользователи, которые не должны попасть в подсчет
        User::factory()->count(5)->create(['cohorts' => '["to-add"]']);
        User::factory()->count(5)->create(['cohorts' => '[]']);

        $cohort = Cohort::make('test');

        $this->assertEquals(3, $cohort->getUsersCount());
    }

    public function test_counts_qualifying_users()
    {
        // Один пользователь с когортой 'test' создается в setup
        // Пользователи, которые подходят для когорты
        User::factory()->count(5)->create(['cohorts' => '["to-add"]']);
        // Пользователи, которые не должны попасть в подсчет
        User::factory()->count(5)->create(['cohorts' => '[]']);

        $cohort = Cohort::make('test');

        $this->assertEquals(5, $cohort->getQualifyingUsersCount());

    }

    public function test_adds_qualifying_users()
    {
        // Один пользователь с когортой 'test' создается в setup
        // Пользователи, которые должны попасть в когорту
        User::factory()->count(5)->create(['cohorts' => '["to-add"]']);
        // Пользователи, которые не должны попасть в когорту
        User::factory()->count(5)->create(['cohorts' => '[]']);

        $cohort = Cohort::make('test');
        $cohort->addQualifyingUsers();

        $this->assertEquals(6, $cohort->getUsersCount());
    }

    public function test_adds_qualifying_users_by_role()
    {
        // Пользователи, которые должны попасть в когорту
        User::factory()->count(3)->create(['admin_role' => 4]);
        // Пользователи, которые не должны попасть в когорту
        User::factory()->count(3)->create(['admin_role' => 1]);

        $cohort = Cohort::make('test-by-role');
        $cohort->addQualifyingUsers();

        $this->assertEquals(3, $cohort->getUsersCount());
    }

    public function test_adds_qualifying_users_by_role_doesnt_duplicate_cohort()
    {
        $user = User::factory()->create(['admin_role' => 4]);

        $cohort = Cohort::make('test-by-role');
        $cohort->addQualifyingUsers();
        $cohort->addQualifyingUsers();

        $this->assertTrue($user->refresh()->inCohort('test-by-role'));
        $this->assertCount(1, $user->cohorts);
    }

    public function test_doesnt_add_not_qualified_user()
    {
        $user = User::factory()->create(['cohorts' => '["not-qualified"]']);

        Cohort::make('test')->addQualifyingUsers();

        $this->assertFalse($user->refresh()->inCohort('test'));
    }

    public function test_doesnt_add_not_qualified_user_by_role()
    {
        $user = User::factory()->create(['admin_role' => 1]);

        Cohort::make('test-by-role')->addQualifyingUsers();

        $this->assertFalse($user->refresh()->inCohort('test-by-role'));
    }

    public function test_updates_blessings_for_users_in_cohort()
    {
        $user = User::factory()->create([
            'cohorts' => json_encode(["to-add"]),
            'blessings' => json_encode(['old-blessing']),
        ]);

        Cohort::make('test')->addQualifyingUsers();
        $user->refresh();

        $this->assertTrue($user->blessedTo('old-blessing'));
        $this->assertTrue($user->blessedTo('blessing1'));
    }

    public function test_doesnt_update_blessings_for_user_not_in_cohort()
    {
        $user = User::factory()->create([
            'cohorts' => json_encode(["not-qualified"]),
            'blessings' => json_encode(['old-blessing']),
        ]);

        Cohort::make('test')->addQualifyingUsers();
        $user->refresh();

        $this->assertTrue($user->blessedTo('old-blessing'));
        $this->assertFalse($user->blessedTo('blessing1'));
    }

    public function test_doesnt_duplicate_blessings_on_update()
    {
        $user = User::factory()->create([
            'cohorts' => json_encode(["to-add"]),
            'blessings' => json_encode(['blessing1']),
        ]);

        Cohort::make('test')->addQualifyingUsers();
        $user->refresh();

        $this->assertTrue($user->blessedTo('blessing1'));
        $this->assertCount(1, json_decode($user->blessings, true));
    }

    public function test_updates_blessings_for_users_in_cohort_with_empty_blessings()
    {
        $user = User::factory()->create(['admin_role' => 4]);

        Cohort::make('test-by-role')->addQualifyingUsers();
        $user->refresh();

        $this->assertTrue($user->blessedTo('blessing2'));
    }
}
