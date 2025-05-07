<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('go_types');
Capsule::schema()->create('go_types',function ($table)
{
    $table->increments('id');
    $table->string('name', 100);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});
