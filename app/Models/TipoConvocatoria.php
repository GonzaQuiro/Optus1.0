<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Concurso;

class TipoConvocatoria extends Model
{
    protected $table = 'tipo_convocatoria';

     public function concursos()
    {
    	return $this->hasMany(Concurso::class, 'id', 'tipo_convocatoria');
    }
}