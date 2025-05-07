<?php

namespace App\Models;

use App\Models\Model;

class Mailer extends Model
{

    protected $table = 'mailer';

    protected $fillable = [
        'idmailer',
        'idlogin',
        'host',
        'smtp',
        'port',
        'alias',
        'user',
        'pass',
        'logo',
        'logosmall'
    ];

}