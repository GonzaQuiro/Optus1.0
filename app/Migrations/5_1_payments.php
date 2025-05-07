<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('payments');
Capsule::schema()->create('payments',function ($table)
{
    $table->increments('payments_id');
    $table->integer('participante_id');
    $table->longText('link')->nullable();
    $table->string('paid')->nullable();
    $table->string('preference')->nullable();
    $table->string('title')->nullable();
    $table->string('itemid')->nullable();
    $table->string('payid')->nullable();
    $table->string('orderid')->nullable();
    $table->timestamps();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('participante_id')->references('id')->on('concursos_x_oferentes');
});
