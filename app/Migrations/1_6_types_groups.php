<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('types_groups');
Capsule::schema()->create('types_groups',function ($table)
{
    $table->increments('id');
    $table->integer('type_id')->unsigned();
    $table->integer('group_id')->unsigned();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('type_id')->references('id')->on('user_types')
        ->onDelete('cascade');
    $table->foreign('group_id')->references('id')->on('permission_groups')
        ->onDelete('cascade');
});