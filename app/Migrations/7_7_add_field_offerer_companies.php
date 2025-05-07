<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasColumn('offerer_companies', 'companyclassification'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        #$table->string('companyclassification', 50)
        $table->enum('companyclassification', ['Pequeña', 'Mediana', 'Grande'])
            ->nullable()
            #->default('')
            ->after('companydescription')
            ->comment = 'Marketing, clasificación de la compañía';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'economicsector'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        #$table->string('economicsector', 200)
        $table->enum('economicsector', ['Público', 'Privado', 'Ambos'])
            ->nullable()
            #->default('')
            ->after('companyclassification')
            ->comment = 'Marketing, Sector económico';
    });
}


