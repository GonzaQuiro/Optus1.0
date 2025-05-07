<?php

namespace App\Models;

use App\Models\Model;

class PlantillaTecnicaItem extends Model
{
    protected $table = 'plantilla_precalificacion_tecnica_attr';

    protected $fillable = [
    	'id_plantilla',
        'atributo',
        'puntaje'
    ];

    protected $appends = [
        'ponderacion'
    ];

    public function atributos_items()
    {
        return $this->hasMany(PlantillaTecnica::class, 'id', 'id_plantilla');
    }

    public function getPonderacionAttribute()
    {
        $result = collect();
        if(!isset($this->ponderacion)){
            $atributo = $this->ponderacion='';
            $result = $result->push($atributo);
        }
        return $result;
    }

}