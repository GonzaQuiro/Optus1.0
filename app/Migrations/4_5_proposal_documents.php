<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('proposal_documents');
Capsule::schema()->create('proposal_documents', function ($table)
{
    $table->increments('id');
    $table->unsignedInteger('proposal_id');
    $table->unsignedInteger('type_id');
    $table->string('filename', 255);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('proposal_id')->references('id')->on('proposals');
    $table->foreign('type_id')->references('id')->on('proposal_document_types');
});