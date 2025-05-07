<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Provincia;

class Ciudad extends Model
{	
    protected $table = 'ciudades';

    protected $fillable = [
        'id',
        'nombre',
        'cp',
        'provincia_id'
    ];

    public function provincia()
    {
    	return $this->belongsTo(Provincia::class, 'provincia_id', 'id');
    }

    public static function getList() 
    {
        $result = [];
        $cities = Provincia::all()->pluck('ciudades')->flatten();
        foreach ($cities->sortBy('nombre') as $city) {
            $result[] = [
                'id'    => (string) $city->id,
                'text'  => $city->nombre
            ];
        }

        return $result;
    }

    public static function getFromProvinceIdsList($ids) 
    {
        $result = [];
        $cities = Provincia::whereIn('id', $ids)->get()->pluck('ciudades')->flatten();
        foreach ($cities->sortBy('nombre') as $city) {
            $result[] = [
                'id'    => (string) $city->id,
                'text'  => $city->nombre
            ];
        }

        return $result;
    }
}