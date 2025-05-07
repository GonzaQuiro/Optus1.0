<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('go_payment_methods');
Capsule::schema()->create('go_payment_methods',function ($table)
{
    $table->increments('id');
    $table->string('name');
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});