<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('go_load_types');
Capsule::schema()->create('go_load_types',function ($table)
{
    $table->increments('id');
    $table->string('name');
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});