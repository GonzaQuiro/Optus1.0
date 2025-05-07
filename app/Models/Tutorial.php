<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use Carbon\Carbon;

class Tutorial extends Model
{   
    use SoftDeletes;

    protected $table = 'tutorial';

    protected $primaryKey = 'idtutorial';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'nombre',
        'descripcion',
        'link',
        'permisos',
    ];
} 