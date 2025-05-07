<?php

namespace App\Migrations;

require __DIR__ . '/../OldServices/rest.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('catalogcategories');
Capsule::schema()->create('catalogcategories',function ($table)
{
    $table->increments('id');
    $table->string('name', 100);
    $table->timestamps();
    $table->softDeletes();
});
