<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasColumn('offerer_companies', 'foundationyear'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->smallInteger('foundationyear')
            ->nullable()
            ->after('comments')
            ->comment = 'Información de negocios, Año de fundación';
    });        
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'numberofemployees'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->smallInteger('numberofemployees')
            ->nullable()
            ->after('foundationyear')
            ->comment = 'Información de negocios, Número de empleados';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'annualincome'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->decimal('annualincome', 8, 2)
            ->nullable()
            ->default(0)
            ->after('numberofemployees')
            ->comment = 'Información de negocios, Ingresos anuales';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'facebookaccount'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->string('facebookaccount', 200)
            ->nullable()
            ->default('')
            ->after('annualincome')
            ->comment = 'Marketing, Cuenta facebook';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'twitteraccount'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->string('twitteraccount', 200)
            ->nullable()
            ->default('')
            ->after('facebookaccount')
            ->comment = 'Marketing, Cuenta twitter';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'linkedinaccount'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->string('linkedinaccount', 200)
            ->nullable()
            ->default('')
            ->after('twitteraccount')
            ->comment = 'Marketing, Cuenta linkedin';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'companydescription'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->mediumText('companydescription', 1000)
            ->nullable()
            ->default('')
            ->after('linkedinaccount')
            ->comment = 'Marketing, Descripción de la compañía';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'companylogo'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        //$table->binary('companylogo')
        $table->string('companylogo', 100)
            ->nullable()
            ->after('companydescription')
            ->comment = 'Marketing, Logo de la compañía';
    });
}

if (!Capsule::schema()->hasColumn('offerer_companies', 'certifications'))
{
    Capsule::schema()->table('offerer_companies', function ($table) {
        $table->mediumText('certifications', 2000)
            ->nullable()
            ->after('companylogo')
            ->comment = 'Certificaciones del cliente';
    });
}

