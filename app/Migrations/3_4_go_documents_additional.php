<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('go_documents_additional');
Capsule::schema()->create('go_documents_additional',function ($table)
{
    $table->increments('id');
    $table->integer('id_go');
    $table->string('name');
    $table->enum('type', ['driver', 'vehicle']);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});