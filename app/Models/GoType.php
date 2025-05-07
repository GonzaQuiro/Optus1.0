<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Go;

class GoType extends Model
{
    use SoftDeletes;
    
    protected $table = 'go_types';

    protected $fillable = [
        'id',
        'type_id',
        'name'
    ];

    public function go()
    {
    	return $this->hasMany(Go::class, 'id', 'type_id');
    }

    public static function getList() 
    {
        $result = [];
        try {

            $result[] = [
                'id'    => '0',
                'text'  => 'Seleccionar...'
            ];

            foreach (self::all() as $go_type) {
                $result[] = [
                    'id'    => (string) $go_type->id,
                    'text'  => $go_type->name
                ];
            }
        } catch (\Exception $e) {

        }

        return $result;
    }
}