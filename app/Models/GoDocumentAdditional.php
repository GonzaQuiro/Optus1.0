<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\ParticipanteGoDocument;
use App\Models\Go;

class GoDocumentAdditional extends Model
{
    use SoftDeletes;

    protected $table = 'go_documents_additional';

    protected $fillable = [
        'id',
        'id_go',
        'type',
        'name'
    ];

    const TYPE_SLUGS = [
        'driver'    => 'driver',
        'vehicle'   => 'vehicle'
    ];

    public function participante_document()
    {
    	return $this->hasMany(ParticipanteGoDocument::class);
    }

    public function go()
    {
    	return $this->belongsTo(Go::class, 'id', 'id_go');
    }
}