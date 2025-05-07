<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class InvitationStatus extends Model
{
    use SoftDeletes;
    
    protected $table = 'invitation_status';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    protected $appends = [
        'is_accepted',
        'is_rejected',
        'is_pending',
        'is_expired'
    ];

    const CODES = [
        'accepted'  => 'accepted',
        'rejected'  => 'rejected',
        'pending'   => 'pending',
        'expired'   => 'expired'
    ];

    public function getIsAcceptedAttribute()
    {
        return $this->attributes['code'] === $this::CODES['accepted'];
    }

    public function getIsPendingAttribute()
    {
        return $this->attributes['code'] === $this::CODES['pending'];
    }

    public function getIsRejectedAttribute()
    {
        return $this->attributes['code'] === $this::CODES['rejected'];
    }

    public function getIsExpiredAttribute()
    {
        return $this->attributes['code'] === $this::CODES['expired'];
    }
}