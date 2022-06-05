<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlessingsTable extends Migration
{
    public function up()
    {
        Schema::create('blessings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->integer('position');
            $table->text('description')->nullable();
            $table->foreignId('folder_id')->constrained('blessings_folders');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blessings');
    }
}
