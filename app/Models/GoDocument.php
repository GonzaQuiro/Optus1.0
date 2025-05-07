<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Document;
use App\Models\Go;
use App\Models\PolicyAmount;
use App\Models\ParticipanteGoDocument;

class GoDocument extends Model
{
    use SoftDeletes;

    protected $table = 'go_documents';

    protected $fillable = [
        'id',
        'id_document',
        'id_go',
        'id_policy_amount',
        'cuit',
        'razon_social',
        'deleted_at'
    ];

    public function go()
    {
    	return $this->belongsTo(Go::class, 'id', 'id_go');
    }

    public function document()
    {
    	return $this->hasOne(Document::class, 'id', 'id_document');
    }

    public function policy_amount()
    {
    	return $this->hasOne(PolicyAmount::class, 'id', 'id_policy_amount');
    }
   
    public function participante_document()
    {
    	return $this->hasMany(ParticipanteGoDocument::class, 'id_go', 'id');
    }
  
}