<?php

namespace App\Migrations;

require '../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasColumn('customer_companies', 'estrategia_active')) {
    Capsule::schema()->table('customer_companies', function ($table) {
        $table->string('estrategia_active')->default('no')->after('solped_active');
    });
}
