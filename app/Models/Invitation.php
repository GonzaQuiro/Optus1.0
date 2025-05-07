<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Concurso;
use App\Models\Participante;
use App\Models\InvitationStatus;
use Carbon\Carbon;

class Invitation extends Model
{
    use SoftDeletes;

    protected $table = 'invitations';

    public $timestamps = true;

    protected $fillable = [
        'id',
        'concurso_id',
        'participante_id',
        'status_id',
        'reminder',
        'reminder_date',
        'comentario_rechazo'
    ];

    protected $dates = [
        'reminder_date',
        'created_at',
        'updated_at'
    ];

    protected $apends = [
        'is_accepted',
        'is_rejected',
        'is_pending',
        'is_expired'
    ];

    public function concurso()
    {
    	return $this->belongsTo(Concurso::class, 'concurso_id', 'id');
    }

    public function oferente()
    {
    	return $this->belongsTo(Participante::class, 'participante_id', 'id');
    }

    public function status()
    {
    	return $this->hasOne(InvitationStatus::class, 'id', 'status_id');
    }

    public function getUpdatedAtAttribute()
    {
        return 
            $this->attributes['created_at'] === $this->attributes['updated_at'] ? 
            null : 
            (
                $this->attributes['updated_at'] ?
                Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['updated_at']) : 
                null
            );
    }

    public function getIsAcceptedAttribute()
    {
    	return $this->status->is_accepted;
    }

    public function getIsRejectedAttribute()
    {
    	return $this->status->is_rejected;
    }

    public function getIsPendingAttribute()
    {
    	return $this->status->is_pending;
    }

    public function getIsExpiredAttribute()
    {
    	return $this->status->is_expired;
    }
}