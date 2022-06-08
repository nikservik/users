<?php

namespace Nikservik\Users\Tests\Middleware;

use Illuminate\Support\Facades\Route;
use Nikservik\Users\Tests\TestCase;

class HasAdminRoleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/editor', function () { return 'ok'; })
            ->middleware('admin:editor');
        Route::get('/admin3', function () { return 'ok'; })
            ->middleware('admin:3');
    }

    public function test_rejects_editor_without_admin_role()
    {
        $this->actingAs($this->user)
            ->get('/editor')
            ->assertStatus(403);
    }

    public function test_rejects_admin_without_admin_role()
    {
        $this->actingAs($this->user)
            ->get('/admin3')
            ->assertStatus(403);
    }

    public function test_gets_editor_with_role()
    {
        $this->user->admin_role = 3;

        $this->actingAs($this->user)
            ->get('/editor')
            ->assertStatus(200);
    }

    public function test_gets_admin_with_role()
    {
        $this->user->admin_role = 3;

        $this->actingAs($this->user)
            ->get('/admin3')
            ->assertStatus(200);
    }

    public function test_rejects_admin_with_low_admin_role()
    {
        $this->user->admin_role = 2;

        $this->actingAs($this->user)
            ->get('/admin3')
            ->assertStatus(403);
    }

}
