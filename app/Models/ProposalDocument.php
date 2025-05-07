<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Proposal;
use App\Models\ProposalDocumentType;
use Carbon\Carbon;

class ProposalDocument extends Model
{
    use SoftDeletes;
    
    protected $table = 'proposal_documents';

    protected $fillable = [
        'id',
        'proposal_id',
        'type_id',
        'filename'
    ];

    protected $appends = [
        'is_technical',
        'is_economic'
    ];

    public function proposal()
    {
    	return $this->belongsTo(Proposal::class, 'proposal_id', 'id');
    }

    public function type()
    {
    	return $this->belongsTo(ProposalDocumentType::class, 'type_id', 'id');
    }

    public function getIsEconomicAttribute()
    {
        return $this->type->is_economic;
    }

    public function getIsTechnicalAttribute()
    {
        return $this->type->is_technical;
    }
} 