<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableWithAdminRoleAttribute extends Migration
{
    public function up()
    {
        if (in_array('create-admin-role-attribute', Config::get('users.features'))) {
            if (! Schema::hasColumn('users', 'admin_role')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->integer('admin_role')->default(1);
                });
            }

            if ($owner = Config::get('users.owner')) {
                DB::table('users')
                    ->where('email', $owner)
                    ->update(['admin_role' => 4]);
            }
        }
    }

    public function down()
    {
        if (in_array('create-admin-role-attribute', Config::get('users.features'))
            && Schema::hasColumn('users', 'admin_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('admin_role');
            });
        }
    }
}
