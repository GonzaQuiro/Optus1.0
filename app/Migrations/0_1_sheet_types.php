<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('sheet_types');
Capsule::schema()->create('sheet_types', function ($table)
{
    $table->increments('id');
    $table->string('code');
    $table->string('description');
    /**
     *  foreign key for relationship tables
     **/
});