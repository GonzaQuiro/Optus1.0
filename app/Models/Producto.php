<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Concurso;
use App\Models\User;
use App\Models\Measurement;

class Producto extends Model
{
    protected $table = 'hijos_x_concursos';

    protected $fillable = [
        'id',
        'id_concurso',
        'id_usuario',
        'nombre',
        'descripcion',
        'cantidad',
        'oferta_minima',
        'unidad',
        'targetcost',
        'eliminado',
    ];

    protected $casts = [
        'targetcost' => 'float',
        'cantidad'=> 'float',
        'oferta_minima'=> 'float'
    ];

    public function concurso()
    {
        return $this->belongsTo(Concurso::class, 'id_concurso', 'id');
    }

    public function oferente()
    {
        return $this->belongsTo(Participante::class, 'id_usuario', 'id_offerer');
    }

    public function unidad_medida()
    {
        return $this->belongsTo(Measurement::class, 'unidad', 'id');
    }
}