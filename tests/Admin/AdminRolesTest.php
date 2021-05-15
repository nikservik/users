<?php

namespace Nikservik\Users\Tests\Admin;

use Nikservik\Users\Tests\TestCase;
use Nikservik\Users\Tests\TestUser;

class AdminRolesTest extends TestCase
{
    protected $editor;
    protected $admin;
    protected $superadmin;

    public function setUp(): void
    {
        parent::setUp();

        $this->editor = TestUser::create([
            'name' => 'bob',
            'password' => 'password',
            'email' => 'editor@example.com',
            'admin_role' => 2,
        ]);

        $this->admin = TestUser::create([
            'name' => 'bob',
            'password' => 'password',
            'email' => 'admin@example.com',
            'admin_role' => 3,
        ]);

        $this->superadmin = TestUser::create([
            'name' => 'bob',
            'password' => 'password',
            'email' => 'superadmin@example.com',
            'admin_role' => 4,
        ]);
    }

    public function testHasEditorRole()
    {
        $this->assertTrue($this->editor->hasEditorRole());
        $this->assertTrue($this->admin->hasEditorRole());
        $this->assertTrue($this->superadmin->hasEditorRole());
    }

    public function testHasAdminRole()
    {
        $this->assertFalse($this->editor->hasAdminRole());
        $this->assertTrue($this->admin->hasAdminRole());
        $this->assertTrue($this->superadmin->hasAdminRole());
    }

    public function testHasSuperAdminRole()
    {
        $this->assertFalse($this->editor->hasSuperAdminRole());
        $this->assertFalse($this->admin->hasSuperAdminRole());
        $this->assertTrue($this->superadmin->hasSuperAdminRole());
    }

    public function testGetAdminRoleAttribute()
    {
        $this->assertEquals(2, $this->editor->adminRole);
        $this->assertEquals(3, $this->admin->adminRole);
        $this->assertEquals(4, $this->superadmin->adminRole);
    }
}
