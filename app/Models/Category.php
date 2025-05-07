<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Area;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'name'
    ];

    public function areas()
    {
    	return $this->hasMany(Area::class, 'category_id', 'id');
    }

    public static function getList()
    {
        $result = [];

        foreach (self::all() as $area) {
            $result[] = [
                'id'    => (string) $area->id,
                'text'  => $area->name
            ];
        }

        return $result;
    }

    public static function getFromCategoryGroupList()
    {
        $result = [];

        $categorias = self::with(['areas'])->get();
        foreach ($categorias->sortBy('name') as $categoria) {
            $areasresult =  [];
            foreach ($categoria->areas->sortBy('name') as $area) 
                $areasresult[] = [
                    'id'    => (string) $area->id,
                    'text'  => $area->name
                ];

            $result[] = [
                'id'    => (string) $categoria->id,
                'text'  => $categoria->name,
                'areas'  => $areasresult
            ];
        }
        return $result;
    }

    public static function getFromCategoryWithAreasList(){
        $result = [];
        $categorias = self::with('areas')->whereHas('areas')->get();
        foreach ($categorias->sortBy('name') as $categoria) {
            $areasresult =  [];
            foreach ($categoria->areas->sortBy('name') as $area) 
                $areasresult[] = [
                    'id'    => (string) $area->id,
                    'text'  => $area->name
                ];

            $result[] = [
                'id'    => (string) $categoria->id,
                'text'  => $categoria->name,
                'areas'  => $areasresult
            ];
        }
        return $result;
    }
}