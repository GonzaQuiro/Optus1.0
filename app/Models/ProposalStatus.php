<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class ProposalStatus extends Model
{
    use SoftDeletes;
    
    protected $table = 'proposal_status';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    protected $appends = [
        'is_accepted',
        'is_rejected',
        'is_pending',
        'is_expired',
        'is_revisada'
    ];

    const CODES = [
        'accepted'  => 'accepted',
        'rejected'  => 'rejected',
        'pending'   => 'pending',
        'expired'   => 'expired',
        'revisada'  => 'revisada'
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
    public function getIsRevisadaAttribute()
    {
        return $this->attributes['code'] === $this::CODES['revisada'];
    }
}