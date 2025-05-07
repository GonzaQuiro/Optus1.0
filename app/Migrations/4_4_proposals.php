<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('proposals');
Capsule::schema()->create('proposals', function ($table)
{
    $table->increments('id');
    $table->integer('participante_id');
    $table->unsignedInteger('type_id');
    $table->longText('comment')->nullable();
    $table->mediumText('values')->nullable();
    $table->unsignedInteger('status_id')->default(1);
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('participante_id')->references('id')->on('concursos_x_oferentes');
    $table->foreign('type_id')->references('id')->on('proposal_types');
    $table->foreign('status_id')->references('id')->on('proposal_status');
});