<?php

namespace App\Models;

use App\Models\Model;

class Moneda extends Model
{	
    protected $table = 'monedas';

    public static function getList() 
    {
        $result = [];

        foreach (self::all() as $moneda) {
            $result[] = [
                'id'    => (int) $moneda->id,
                'text'  => $moneda->nombre
            ];                
        }
        
        return $result;
    }
}