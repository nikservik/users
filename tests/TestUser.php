<?php


namespace Nikservik\Users\Tests;

use Illuminate\Foundation\Auth\User;
use Nikservik\Users\Admin\AdminRoles;
use Nikservik\Users\Blessings\Blessings;
use Nikservik\Users\Cohorts\Cohort;
use Nikservik\Users\Cohorts\Cohorts;

/**
 * Класс пользователя только для тестирования пакета
 * @property string $email
 * @property string $blessings
 * @property-read array<Cohort> $cohorts
 * @property int $admin_role
 */
class TestUser extends User
{
    use Blessings;
    use Cohorts;
    use AdminRoles;

    protected static array $blesserContainers = ['cohorts'];

    protected $table = 'users';

    protected $fillable = ['name', 'password', 'email', 'blessings', 'cohorts', 'admin_role'];
}
