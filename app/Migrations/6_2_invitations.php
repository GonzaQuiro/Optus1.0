<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('invitations');
Capsule::schema()->create('invitations', function ($table)
{
    $table->increments('id');
    $table->integer('concurso_id');
    $table->integer('participante_id');
    $table->unsignedInteger('status_id')->default(1);
    $table->boolean('reminder')->default(false);
    $table->dateTime('reminder_date')->nullable();
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('concurso_id')->references('id')->on('concursos');
    $table->foreign('participante_id')->references('id')->on('concursos_x_oferentes');
    $table->foreign('status_id')->references('id')->on('invitation_status');
});