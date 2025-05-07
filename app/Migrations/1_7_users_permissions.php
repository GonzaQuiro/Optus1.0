<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('users_permissions');
Capsule::schema()->create('users_permissions',function ($table)
{
    $table->increments('id');
    $table->integer('user_id')->unsigned();
    $table->integer('permission_id')->unsigned();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('user_id')->references('id')->on('users')
        ->onDelete('cascade');
    $table->foreign('permission_id')->references('id')->on('permissions')
        ->onDelete('cascade');
});