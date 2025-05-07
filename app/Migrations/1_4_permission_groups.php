<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('permission_groups');
Capsule::schema()->create('permission_groups',function ($table)
{
    $table->increments('id');
    $table->string('code');
    $table->string('description');
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});