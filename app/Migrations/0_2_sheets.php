<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('sheets');
Capsule::schema()->create('sheets', function ($table)
{
    $table->increments('id');
    $table->integer('concurso_id');
    $table->unsignedInteger('type_id');
    $table->string('filename', 255);
    $table->timestamps();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('concurso_id')->references('id')->on('concursos');
    $table->foreign('type_id')->references('id')->on('sheet_types');
});