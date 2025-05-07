<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;


if (Capsule::schema()->hasColumn('hijos_x_concursos', 'unidad'))
{
    Capsule::schema()->table('hijos_x_concursos', function ($table) {
        //DB::statement("ALTER TABLE `hijos_x_concursos` MODIFY COLUMN `unidad` INT UNSIGNED DEFAULT NULL NULL;");
        //$table->smallInteger('unidad')->unsignedInteger()->change();
        $table->foreign('unidad')
            ->references('id')->on('measurements')
            ->onDelete('cascade');
    });        
}

if (!Capsule::schema()->hasColumn('hijos_x_concursos', 'targetcost'))
{
    Capsule::schema()->table('hijos_x_concursos', function ($table) {
        $table->decimal('targetcost', 8, 2)
            ->nullable()
            ->default(0)
            ->after('unidad')
            ->comment = 'Costo objetivo del item';
    });
}
