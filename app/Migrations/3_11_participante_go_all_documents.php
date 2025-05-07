<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('participante_go_all_documents');
Capsule::schema()->create('participante_go_all_documents',function ($table)
{
    $table->increments('id');
    $table->integer('participante_id');
    $table->unsignedInteger('id_go_document_additional')->nullable();
    $table->unsignedInteger('id_go_document')->nullable();
    $table->string('filename', 255);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('participante_id')->references('id')->on('concursos_x_oferentes');
    $table->foreign('id_go_document')->references('id')->on('go_documents');
    $table->foreign('id_go_document_additional')->references('id')->on('go_documents_additional');
});