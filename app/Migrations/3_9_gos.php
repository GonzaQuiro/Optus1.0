<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('gos');
Capsule::schema()->create('gos',function ($table)
{
    $table->increments('id');
    $table->unsignedInteger('type_id');
    $table->integer('peso');
    $table->integer('alto')->nullable();
    $table->integer('largo')->nullable();
    $table->integer('ancho')->nullable();
    $table->unsignedInteger('load_type_id');
    $table->integer('unidades_bultos');
    $table->unsignedInteger('payment_method_id');
    $table->integer('plazo_pago');
    $table->dateTime('fecha_desde');
    $table->dateTime('fecha_hasta');
    $table->string('calle_desde', 100);    
    $table->string('calle_hasta', 100);
    $table->string('numeracion_desde', 100);    
    $table->string('numeracion_hasta', 100);
    $table->unsignedInteger('ciudad_desde_id');
    $table->unsignedInteger('ciudad_hasta_id');
    $table->unsignedInteger('provincia_desde_id');
    $table->unsignedInteger('provincia_hasta_id');
    $table->string('nombre_desde', 100)->nullable();    
    $table->string('nombre_hasta', 100)->nullable();
    $table->boolean('cotizar_seguro')->default(false);
    $table->double('suma_asegurada')->nullable();
    $table->boolean('cotizar_armada')->default(false);
    $table->softDeletes();
    /**
     *  foreign key for relationship tables
     **/
    $table->foreign('type_id')->references('id')->on('go_types');
    $table->foreign('load_type_id')->references('id')->on('go_load_types');
    $table->foreign('payment_method_id')->references('id')->on('go_payment_methods');
    $table->foreign('ciudad_desde_id')->references('id')->on('ciudades');
    $table->foreign('ciudad_hasta_id')->references('id')->on('ciudades');
    $table->foreign('provincia_desde_id')->references('id')->on('provincias');
    $table->foreign('provincia_hasta_id')->references('id')->on('provincias');
});