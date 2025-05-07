<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Pais;

class Provincia extends Model
{	
    protected $table = 'provincias';
    
    protected $fillable = [
        'id',
        'nombre',
        'id_pais'
    ];

    public function ciudades()
    {
    	return $this->hasMany(Ciudad::class, 'provincia_id', 'id');
    }

    public function pais()
    {
    	return $this->belongsTo(Pais::class, 'id_pais', 'id');
    }

    public static function getList() 
    {
        $result = [];
        $provinces = Pais::all()->pluck('provincias')->flatten();
        foreach ($provinces->sortBy('nombre') as $province) {
            $result[] = [
                'id'    => (string) $province->id,
                'text'  => $province->nombre
            ];
        }

        return $result;
    }

    public static function getFromCountryIdsList($ids) 
    {
        $result = [];
        $provinces = Pais::whereIn('id', $ids)->get()->pluck('provincias')->flatten();
        foreach ($provinces->sortBy('nombre') as $province) {
            $result[] = [
                'id'    => (string) $province->id,
                'text'  => $province->nombre
            ];
        }

        return $result;
    }

    public static function getFromCityIdsList($ids) 
    {
        $result = [];
        $provinces = Ciudad::whereIn('id', $ids)->get()->pluck('provincia')->flatten();
        foreach ($provinces->sortBy('nombre') as $province) {
            $result[] = [
                'id'    => (string) $province->id,
                'text'  => $province->nombre
            ];
        }

        return $result;
    }
}