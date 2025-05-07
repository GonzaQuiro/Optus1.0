<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\User;
use App\Models\Catalogo;

class CustomerCatalogo extends Model
{
    use SoftDeletes;

    protected $table = 'customer_catalog';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'id',
        'customer_id',
        'catalogcategory_id',
        'description',
        'long_description',
        'targetcost',
        'unidad',
        'codigo_ERP',
        'codigo_proveedor',
        'proveedor'
    ];

    public function unidad_medida()
    {
    	return $this->belongsTo(Measurement::class, 'unidad', 'id');
    }

    public function catalog()
    {
    	return $this->hasOne(Catalogo::class, 'id', 'catalogcategory_id');
    }

    public function getNombreCategoriaAttribute()
    {
        return $this->catalog->where('id', $this->catalogcategory_id)->first()->name;
    }

    public static function getList()
    {
        $result = [];

        foreach (self::all() as $company) {
            $result[] = [
                'id'            => (string) $company->id,
                'name'          => $company->business_name,
                'is_offerer'    => false,
                'is_customer'   => true
            ];
        }

        return $result;
    }
}