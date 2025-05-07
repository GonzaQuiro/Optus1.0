<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('areas');
Capsule::schema()->create('areas', function ($table)
{
    $table->increments('id');
    $table->string('name');
    $table->unsignedInteger('category_id');
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('category_id')->references('id')->on('categories');
});