<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('offerers_areas');
Capsule::schema()->create('offerers_areas',function ($table)
{
    $table->increments('id');
    $table->integer('offerer_id')->unsigned();
    $table->integer('area_id')->unsigned();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('offerer_id')->references('id')->on('offerer_companies')
        ->onDelete('cascade');
    $table->foreign('area_id')->references('id')->on('areas')
        ->onDelete('cascade');
});