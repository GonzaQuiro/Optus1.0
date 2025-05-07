<?php

namespace App\Models;

use App\Models\GoDocument;
use App\Models\GoDocumentAdditional;
use App\Models\Participante;

class ParticipanteGoDocument extends Model
{
    protected $table = 'participante_go_all_documents';

    protected $fillable = [
        'id',
        'participante_id',
        'id_go_document',
        'id_go_document_additional',
        'filename',
    ];

    public function go_document()
    {
    	return $this->hasOne(GoDocument::class, 'id', 'id_go_document');
    }

    public function go_additional_document()
    {
    	return $this->hasOne(GoDocumentAdditional::class, 'id', 'id_go_document_additional');
    }

    public function oferente()
    {
        return $this->belongsTo(Participante::class, 'participante_id', 'id');
    }
}