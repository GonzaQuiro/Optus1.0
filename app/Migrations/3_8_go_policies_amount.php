<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('go_policies_amount');
Capsule::schema()->create('go_policies_amount',function ($table)
{
    $table->increments('id');
    $table->integer('amount');
    $table->integer('ratio');
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});