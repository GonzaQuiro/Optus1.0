<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('document_types');
Capsule::schema()->create('document_types',function ($table)
{
    $table->increments('id');
    $table->enum('code', ['driver', 'vehicle', 'trailer']);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});