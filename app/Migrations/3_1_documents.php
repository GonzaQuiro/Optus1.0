<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('documents');
Capsule::schema()->create('documents',function ($table)
{
    $table->increments('id');
    $table->string('gcg_code', 50);
    $table->string('name', 250);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});