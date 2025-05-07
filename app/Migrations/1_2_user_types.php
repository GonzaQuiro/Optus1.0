<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('user_types');
Capsule::schema()->create('user_types',function ($table)
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