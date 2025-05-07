<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Concurso;

class TipoOperacion extends Model
{
    protected $table = 'concursos_tipo_operaciones';

    public const TYPES = [
        'publica'   => 1,
        'asociados' => 2
    ];

    public function concursos()
    {
    	return $this->hasMany(Concurso::class, 'id', 'tipo_operacion');
    }

    public static function getList() 
    {
        $result = [];
        foreach (self::all() as $tipo) {
            $result[] = [
                'id'    => (string) $tipo->id,
                'text'  => $tipo->nombre
            ];                
        }
        
        return $result;
    }
}