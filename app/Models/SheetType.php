<?php

namespace App\Models;

use App\Models\Model;

class SheetType extends Model
{    
    protected $table = 'sheet_types';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];
}