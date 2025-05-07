<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Concurso;
use App\Models\SheetType;
use Carbon\Carbon;

class Sheet extends Model
{   
    protected $table = 'sheets';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'concurso_id',
        'type_id',
        'filename'
    ];

    public function concurso()
    {
    	return $this->belongsTo(Concurso::class, 'concurso_id', 'id');
    }

    public function type()
    {
    	return $this->belongsTo(SheetType::class, 'type_id', 'id');
    }
} 