<?php


namespace Nikservik\Users\Tests\Blessings;

use Nikservik\Users\Tests\TestCase;
use Nikservik\Users\Tests\TestUser;

class BlessedDirectiveTest extends TestCase
{
    public function test_blessed_without_user()
    {
        $this->withoutExceptionHandling();
        $this
            ->get('')
            ->assertOk()
            ->assertSee('Nor')
            ->assertDontSee('Blessed');
    }

    public function test_blessed_with_blessed_user()
    {
        $this->withoutExceptionHandling();
        $this
            ->actingAs($this->user)
            ->get('')
            ->assertOk()
            ->assertSee('Blessed')
            ->assertDontSee('Nor');
    }

    public function test_blessed_with_not_blessed_user()
    {
        $user = TestUser::create([
            'email' => 'test@example.com',
            'blessings' => '["other"]',
        ]);
        $this
            ->actingAs($user)
            ->get('')
            ->assertOk()
            ->assertSee('Nor')
            ->assertDontSee('Blessed');
    }
}
