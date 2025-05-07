<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class GoPaymentMethod extends Model
{
    use SoftDeletes;
    
    protected $table = 'go_payment_methods';

    protected $fillable = [
        'id',
        'name'
    ];

    public static function getList() 
    {
        $result = [];

        foreach (self::all() as $payment_method) {
            $result[] = [
                'id'    => (string) $payment_method->id,
                'text'  => $payment_method->name
            ];
        }

        return $result;
    }
}