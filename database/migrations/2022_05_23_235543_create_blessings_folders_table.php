<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlessingsFoldersTable extends Migration
{
    public function up()
    {
        Schema::create('blessings_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blessings_folders');
    }
}
