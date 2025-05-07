<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('offerers_customers');
Capsule::schema()->create('offerers_customers',function ($table)
{
    $table->increments('id');
    $table->integer('offerer_id')->unsigned();
    $table->integer('customer_id')->unsigned();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('offerer_id')->references('id')->on('offerer_companies')
        ->onDelete('cascade');
    $table->foreign('customer_id')->references('id')->on('customer_companies')
        ->onDelete('cascade');
});