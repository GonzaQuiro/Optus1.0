<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Measurement extends Model
{
    use SoftDeletes;
    
    protected $table = 'measurements';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'name'
    ];

    public static function getList() 
    {
        $result = [];
        
        foreach (self::all() as $measurement) {
            $result[] = [
                'id'    => (string) $measurement->id,
                'text'  => $measurement->name
            ];
        }

        return $result;
    }
}