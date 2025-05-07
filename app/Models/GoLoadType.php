<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Go;

class GoLoadType extends Model
{
    use SoftDeletes;
    
    protected $table = 'go_load_types';

    protected $fillable = [
        'id',
        'name'
    ];

    public function gos()
    {
        return $this->hasMany(Go::class, 'id', 'id_tipo_carga');
    }

    public static function getList() 
    {
        $result = [];
        try {
            
            $result[] = [
                'id'    => '0',
                'text'  => 'Seleccionar...'
            ];

            foreach (self::all() as $go_load_type) {
                $result[] = [
                    'id'    => (int) $go_load_type->id,
                    'text'  => $go_load_type->name
                ];                
            }

        } catch (\Exception $e) {

        }
        
        return $result;
    }
}