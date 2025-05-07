<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('customer_company_status');
Capsule::schema()->create('customer_company_status', function ($table)
{
    $table->increments('id');
    $table->string('code');
    $table->string('description');
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
});