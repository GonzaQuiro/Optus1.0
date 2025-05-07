<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class ProposalType extends Model
{
    use SoftDeletes;
    
    protected $table = 'proposal_types';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    const CODES = [
        'technical' => 'technical',
        'economic'  => 'economic'
    ];
}