<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Provincia;

class Pais extends Model
{

    protected $table = 'paises';

    protected $fillable = [
        'id',
        'nombre',
        'codigo'
    ];

    public function provincias()
    {
        return $this->hasMany(Provincia::class, 'id_pais', 'id');
    }

    public static function getList()
    {
        $result = [];
        foreach (self::all()->sortBy('nombre') as $country) {
            $result[] = [
                'id' => (string) $country->id,
                'text' => $country->nombre
            ];
        }

        return $result;
    }

    public static function getFromProvinceIdsList($ids)
    {
        $result = [];
        $countries = Provincia::whereIn('id', $ids)->get()->pluck('country')->flatten();
        foreach ($countries->sortBy('nombre') as $country) {
            $result[] = [
                'id' => (string) $country->id,
                'text' => $country->nombre
            ];
        }

        return $result;
    }

    public static function getCountriesWithProvincesList()
    {
        $result = [];

        $paises = self::with('provincias')->whereHas('provincias')->get();
        foreach ($paises->sortBy('nombre') as $pais) {
            $areasresult = [];
            foreach ($pais->provincias->sortBy('nombre') as $provincia)
                $areasresult[] = [
                    'id' => (string) $provincia->id,
                    'text' => $provincia->nombre
                ];

            $result[] = [
                'id' => (string) $pais->id,
                'text' => $pais->nombre,
                'provincias' => $areasresult
            ];
        }
        return $result;
    }

    public static function getCountries ()
    {
        $result = [];
        $paises = self::get();
        foreach($paises->sortBy('nombre') as $pais){
            $result[] = [
                'id' => $pais->id,
                'text' => $pais->nombre,
                'code' => $pais->codigo
            ];
        }

        return $result;
    }

}