<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('proposal_document_types');
Capsule::schema()->create('proposal_document_types', function ($table)
{
    $table->increments('id');
    $table->string('code');
    $table->string('description');
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});