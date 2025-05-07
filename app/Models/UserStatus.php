<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class UserStatus extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_status';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    protected $appends = [
        'is_active',
        'is_inactive',
        'is_blocked'
    ];

    const CODES = [
        'active'    => 'active',
        'inactive'  => 'inactive',
        'blocked'   => 'blocked'
    ];

    public function users()
    {
    	return $this->hasMany(User::class, 'status_id', 'id');
    }

    public function getIsActiveAttribute()
    {
        return $this->attributes['code'] === $this::CODES['active'];
    }

    public function getIsInactiveAttribute()
    {
        return $this->attributes['code'] === $this::CODES['inactive'];
    }

    public function getIsBlockedAttribute()
    {
        return $this->attributes['code'] === $this::CODES['blocked'];
    }

    public function getIsExpiredAttribute()
    {
        return $this->attributes['code'] === $this::CODES['expired'];
    }

    public static function getList()
    {
        $result = [];

        foreach (self::all() as $status) {
            $result[] = [
                'id'    => (string) $status->id,
                'text'  => $status->description
            ];
        }

        return $result;
    }
}