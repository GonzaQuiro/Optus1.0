<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\OffererCompany;
use App\Models\Category;

class Area extends Model
{
    use SoftDeletes;

    protected $table = 'areas';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'name',
        'category_id'
    ];

    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function offerer_companies()
    {
    	return $this->belongsToMany(OffererCompany::class, 'offerers_areas');
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

    public static function getFromCategoryIdsList($ids) 
    {
        $result = [];
        
        $areas = Category::whereIn('id', $ids)->get()->pluck('areas')->flatten();

        foreach ($areas->sortBy('nombre') as $area) {
            $result[] = [
                'id'    => (string) $area->id,
                'text'  => $area->name
            ];
        }
        return $result;
    }
}