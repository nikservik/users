<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableWithCohortsAttribute extends Migration
{
    public function up()
    {
        if (in_array('create-cohorts-attribute', Config::get('users.features'))
            && ! Schema::hasColumn('users', 'cohorts')) {
            Schema::table('users', function (Blueprint $table) {
                $table->json('cohorts')->nullable();
            });
        }
    }

    public function down()
    {
        if (in_array('create-cohorts-attribute', Config::get('users.features'))
            && Schema::hasColumn('users', 'cohorts')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('cohorts');
            });
        }
    }
}
