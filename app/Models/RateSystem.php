<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class RateSystem extends Model
{
    use SoftDeletes;
    
    protected $table = 'rate_systems';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    public static function getList()
    {
        $result = [];

        foreach (self::all() as $rate_system) {
            $result[] = [
                'id'    => (string) $rate_system->id,
                'text'  => $rate_system->description
            ];
        }

        return $result;
    }
}