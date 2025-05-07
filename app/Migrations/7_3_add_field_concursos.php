<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasColumn('concursos', 'aperturasobre'))
{
    Capsule::schema()->table('concursos', function ($table) {
        $table->enum('aperturasobre', ['no', 'si'])->nullable()->default('no')->after('solo_ofertas_mejores')->comment = 'Apertura sobres con proveedores';
    });        
}

if (!Capsule::schema()->hasColumn('concursos', 'subastavistaciega'))
{
    Capsule::schema()->table('concursos', function ($table) {
        $table->enum('subastavistaciega', ['no', 'si'])
            ->nullable()
            ->default('no')
            ->after('aperturasobre')
            ->comment = 'Concurso online, opción de vista ciega durante la subasta, ver o no las ofertas de cada proveedor';
    });        
}
