<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->disableForeignKeyConstraints();
Capsule::schema()->dropIfExists('users');
Capsule::schema()->create('users', function ($table)
{
    $table->increments('id');
    $table->unsignedInteger('type_id');
    $table->unsignedInteger('status_id');
    $table->unsignedInteger('offerer_company_id')->nullable();
    $table->unsignedInteger('customer_company_id')->nullable();
    $table->string('username');
    $table->string('password');
    $table->string('first_name');
    $table->string('last_name');
    $table->string('phone')->nullable();
    $table->string('cellphone')->nullable();
    $table->string('email')->nullable();
    $table->string('token')->nullable();
    $table->dateTime('validity_date')->nullable();
    $table->timestamps();
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('type_id')->references('id')->on('user_types');
    $table->foreign('status_id')->references('id')->on('user_status');
    $table->foreign('offerer_company_id')->references('id')->on('offerer_companies');
    $table->foreign('customer_company_id')->references('id')->on('customer_companies');
});
Capsule::schema()->enableForeignKeyConstraints();