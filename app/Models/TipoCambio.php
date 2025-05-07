<?php

namespace App\Models;

use App\Models\Model;

class Tipocambio extends Model
{
  
    protected $table = 'tipocambio';

    public $timestamps = true;

    protected $fillable = [
        'idtipocambio',
        'dolar',
        'cambio',
        'moneda'
    ];

    public static function getList() 
    {
        $result = [];
        
        foreach (self::all() as $tipocambio) {
            $result[] = [
                'id'    => (string) $tipocambio->idtipocambio,
                'dolar'  => $tipocambio->dolar,
                'cambio'  => $tipocambio->cambio,
                'moneda'  => $tipocambio->moneda,
            ];
        }

        return $result;
    }
}