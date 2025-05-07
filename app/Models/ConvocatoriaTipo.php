<?php

namespace App\Models;

use App\Models\Model;

class ConvocatoriaTipo extends Model
{
    protected $table = 'tipo_convocatoria';

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