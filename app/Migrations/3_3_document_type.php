<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('document_type');
Capsule::schema()->create('document_type',function ($table)
{
    $table->increments('id');
    $table->integer('document_id')->unsigned();
    $table->integer('document_type_id')->unsigned();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('document_id')->references('id')->on('documents')
        ->onDelete('cascade');
    $table->foreign('document_type_id')->references('id')->on('document_types')
        ->onDelete('cascade');
});