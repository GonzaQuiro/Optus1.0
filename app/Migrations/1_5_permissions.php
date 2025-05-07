<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('permissions');
Capsule::schema()->create('permissions',function ($table)
{
    $table->increments('id');
    $table->string('code');
    $table->string('description');
    $table->unsignedInteger('group_id');
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('group_id')->references('id')->on('permission_groups');
});