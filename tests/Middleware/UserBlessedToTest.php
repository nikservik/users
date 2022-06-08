<?php

namespace Nikservik\Users\Tests\Middleware;

use App\Models\User;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Nikservik\Users\Tests\TestCase;

class UserBlessedToTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/test', function () { return 'ok'; })
            ->middleware('blessed:test');
    }

    public function test_rejects_without_blessing()
    {
        $this->actingAs($this->user)
            ->get('/test')
            ->assertStatus(403);
    }

    public function test_gets_with_blessing()
    {
        $this->user->setBlessings(['test']);

        $this->actingAs($this->user)
            ->get('/test')
            ->assertStatus(200);
    }
}
