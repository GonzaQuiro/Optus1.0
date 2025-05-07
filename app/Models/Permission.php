<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\User;
use App\Models\PermissionGroup;

class Permission extends Model
{
    use SoftDeletes;
    
    protected $table = 'permissions';

    public $timestamps = true;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'id',
        'code',
        'description',
        'group_id'
    ];

    public function users()
    {
    	return $this->belongsToMany(User::class, 'users_permissions');
    }

    public function group()
    {
    	return $this->belongsTo(PermissionGroup::class, 'group_id', 'id');
    }

    public function getList()
    {
        $results = [];

        foreach (self::all() as $permission) {
            $results[] = [
                'id'    => (string) $permission->id,
                'text'  => $permission->description
            ];
        }

        return $results;
    }
}