<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\CustomerCatalogo;

class Catalogo extends Model
{
    use SoftDeletes;

    protected $table = 'catalogcategories';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'name',
    ];

    public function customercatalogs()
    {
        return $this->hasMany(CustomerCatalogo::class, 'catalogcategory_id', 'id');
    }

    public static function getList()
    {
        $result = [];

        foreach (self::all() as $area) {
            $result[] = [
                'id'    => (string) $area->id,
                'nombre'  => $area->name
            ];
        }

        return $result;
    }

    public static function getFromCatalogoGroupList($userId)
    {
        $result = [];
        $catalogos = self::with(['customercatalogs'])->get();
        foreach ($catalogos->sortBy('name') as $catalogo) {
            $areasresult =  [];

            $customercatalogs = CustomerCatalogo::
                where('catalogcategory_id', $catalogo->id)->
                where('customer_id', $userId)->
                get();

            if (isset($customercatalogs) && $customercatalogs->count() > 0) {
                foreach ($customercatalogs->sortBy('description') as $customercatalog) 
                $areasresult[] = [
                    'id'            => (string) $customercatalog->id,
                    'text'          => $customercatalog->description,
                    'targetcost'    => $customercatalog->targetcost,
                    'unidad'        => $customercatalog->unidad
                ];
                $result[] = [
                    'id'    => (string) $catalogo->id,
                    'text'  => $catalogo->name,
                    'meteriales'  => $areasresult
                ];
            }
        }
        return $result;
    }
}