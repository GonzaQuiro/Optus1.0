<?php

namespace App\Models;

use App\Models\Model;
use App\Models\GoDocument;

class PolicyAmount extends Model
{
    protected $table = 'go_policies_amount';

    protected $fillable = [
        'id',
        'amount',
        'ratio',
        'deleted_at'
    ];
 
   
    public static function GetListRatio()
    {
        $result = [];
        try {

            $result[] = [
                'id'    => '0',
                'text'  => 'Seleccionar...'
            ];
            
            foreach (self::all() as $amount) {
                
                $result[] = [
                    'id'    => (string) $amount->id,
                    'text'  => $amount->ratio
                ];
            }
        
        } catch (\Exception $e) {

        }
        
        return $result;
    }

    public static function getList() 
    {
        $result =[];
        foreach (self::all() as $amount) {

            $result[] = [
                'id'    => (string) $amount->id,
                'text'  => $amount->amount,
                'ratio' => $amount->ratio
            ];                
        }

        return $result;
    }

}