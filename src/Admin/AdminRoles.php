<?php


namespace Nikservik\Users\Admin;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

trait AdminRoles
{
    public function hasEditorRole(): bool
    {
        return $this->adminRole >= 2;
    }

    public function hasAdminRole(): bool
    {
        return $this->adminRole >= 3;
    }

    public function hasSuperAdminRole(): bool
    {
        return $this->adminRole >= 4;
    }

    public function getAdminRoleAttribute(): int
    {
        return Arr::get($this->attributes, Config::get('users.admin-role-attribute'), 1);
    }
}
