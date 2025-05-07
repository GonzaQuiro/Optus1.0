<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('customer_catalog');
Capsule::schema()->create('customer_catalog',function ($table)
{
    $table->increments('id');
    $table->integer('customer_id')->unsigned();
    $table->integer('catalogcategory_id')->unsigned();
    $table->string('description', 100);
    $table->mediumText('long_description', 1000);
    $table->string('codigo_ERP', 100);
    $table->string('codigo_proveedor', 100)->nullable();
    $table->string('proveedor', 255)->nullable();
    $table->timestamps();
    $table->softDeletes();

    /**
     *  Foreign key for relationship tables
     **/
    $table
        ->foreign('catalogcategory_id')
        ->references('id')->on('catalogcategories')
        ->onDelete('cascade');

    $table
        ->foreign('customer_id')
        ->references('id')->on('customer_companies')
        ->onDelete('cascade');

    /**
     *  Index
     **/        
    $table->unique(['customer_id', 'codigo_ERP', 'description']);        
});
