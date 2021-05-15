<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableWithBlessingsAttribute extends Migration
{
    public function up()
    {
        if (in_array('create-blessings-attribute', Config::get('users.features'))
            && ! Schema::hasColumn('users', 'blessings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('blessings')->nullable();
            });
        }
    }

    public function down()
    {
        if (in_array('create-blessings-attribute', Config::get('users.features'))
            && Schema::hasColumn('users', 'blessings')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('blessings');
            });
        }
    }
}
