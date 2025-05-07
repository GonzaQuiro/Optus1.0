<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasColumn('customer_catalog', 'unidad'))
{
    Capsule::schema()->table('customer_catalog', function ($table) {
        $table->unsignedInteger('unidad')
            ->nullable()
            ->after('long_description')
            ->comment = 'Unidad de medida';
        $table->foreign('unidad')->references('id')->on('measurements');
        //$table->foreign('unidad', 'cm2_user_id_foreign')->references('id')->on('measurements');
    });        
}

if (!Capsule::schema()->hasColumn('customer_catalog', 'targetcost'))
{
    Capsule::schema()->table('customer_catalog', function ($table) {
        $table->decimal('targetcost', 8, 2)
            ->nullable()
            ->default(0)
            ->after('long_description')
            ->comment = 'Costo objetivo del items';
    });
}