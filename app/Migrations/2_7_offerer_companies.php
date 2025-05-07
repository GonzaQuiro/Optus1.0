<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('offerer_companies');
Capsule::schema()->create('offerer_companies', function ($table)
{
    $table->increments('id');
    $table->unsignedInteger('status_id');
    $table->unsignedInteger('creator_id');
    $table->string('business_name');
    $table->string('cuit');
    $table->string('postal_code')->nullable();
    $table->string('country')->nullable();
    $table->string('province')->nullable();
    $table->string('city')->nullable();
    $table->string('address')->nullable();
    $table->string('latitude')->nullable();
    $table->string('longitude')->nullable();
    $table->string('first_name')->nullable();
    $table->string('last_name')->nullable();
    $table->string('phone')->nullable();
    $table->string('cellphone')->nullable();
    $table->string('email')->nullable();
    $table->string('website')->nullable();
    $table->string('supplier_code')->nullable();
    $table->mediumText('comments')->nullable();
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('status_id')->references('id')->on('offerer_company_status');
    $table->foreign('creator_id')->references('id')->on('users');
});