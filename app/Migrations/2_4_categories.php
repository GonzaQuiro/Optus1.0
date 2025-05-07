<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('categories');
Capsule::schema()->create('categories', function ($table)
{
    $table->increments('id');
    $table->string('name');
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});