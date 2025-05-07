<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\User;
use App\Models\PermissionGroup;

class UserType extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_types';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    protected $appends = [
        'is_super_admin',
        'is_admin',
        'is_offerer',
        'is_customer'
    ];

    const TYPES = [
        'superadmin'        => 'superadmin',
        'admin'             => 'admin',
        'customer'          => 'customer',
        'customer-approve'  => 'customer-approve',
        'customer-read'     => 'customer-read',
        'evaluator'         => 'evaluator',
        'offerer'           => 'offerer',
        'supervisor'        => 'supervisor'
    ];

    public function permission_groups()
    {
    	return $this->belongsToMany(PermissionGroup::class, 'types_groups', 'type_id', 'group_id');
    }

    public function users()
    {
    	return $this->hasMany(User::class, 'id', 'type_id');
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->attributes['code'] === $this::TYPES['superadmin'];
    }

    public function getIsAdminAttribute()
    {
        return in_array($this->attributes['code'], [
            $this::TYPES['admin'],
            $this::TYPES['superadmin']
        ]);
    }

    public function getIsOffererAttribute()
    {
        return in_array($this->attributes['code'], [
            $this::TYPES['offerer']
        ]);
    }

    public function getIsCustomerAttribute()
    {
        return in_array($this->attributes['code'], [
            $this::TYPES['customer'],
            $this::TYPES['customer-approve'],
            $this::TYPES['customer-read'],
            $this::TYPES['evaluator'],
            $this::TYPES['supervisor']

        ]);
    }

    public static function getList($type)
    {
        $types = null;
        if($type == 'admin' || $type == 'superadmin')
        {
            $types = ['admin', 'superadmin'];
        }
        else if($type == 'client')
        {
            $types = ['customer', 'customer-approve', 'customer-read', 'evaluator','supervisor'];
        }
        /*else if($type == 'supervisor')
        {
            $types = ['supervisor'];
        }*/

        else
        {
            $types = ['offerer'];
        }

        $result = [];
        

        foreach (self::whereIn('code', $types)->get() as $type) {
            $result[] = [
                'id'    => (string) $type->id,
                'text'  => $type->description
            ];
        }

        return $result;
    }
}