<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Permission;
use App\Models\UserType;

class PermissionGroup extends Model
{
    use SoftDeletes;

    protected $table = 'permission_groups';

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

    const CODES = [
        'dashboard' => 'dashboard',
        'concursos' => 'concursos',
        'users' => 'users',
        'configuration' => 'configuration',
        'companies' => 'companies',
        'rates' => 'rates',
        'chat' => 'chat',
        'reporte' => 'reportes'
    ];

    public function types()
    {
        return $this->belongsToMany(UserType::class, 'types_groups', 'group_id', 'type_id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'group_id', 'id');
    }
}