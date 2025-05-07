<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('go_documents');
Capsule::schema()->create('go_documents',function ($table)
{
    $table->increments('id');
    $table->unsignedInteger('id_document');
    $table->unsignedInteger('id_go');
    $table->unsignedInteger('id_policy_amount')->nullable();
    $table->string('cuit', 20)->nullable();
    $table->string('razon_social', 50)->nullable();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('id_document')->references('id')->on('documents');
    $table->foreign('id_go')->references('id')->on('gos');
    $table->foreign('id_policy_amount')->references('id')->on('go_policies_amount');
});