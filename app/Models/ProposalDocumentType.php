<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;

class ProposalDocumentType extends Model
{
    use SoftDeletes;

    protected $table = 'proposal_document_types';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    protected $appends = [
        'is_technical',
        'is_economic'
    ];

    public function getIsTechnicalAttribute()
    {
        return in_array($this->attributes['code'], [
            'technical',
            'gantt',
            'surety',
            'condition',
            'general',
            'technicalSigned',
            'confidentiality',
            'impositive',
            'reference',
            'accidents',
            'sample',
            'nom',
            'distintivo',
            'filters',
            'repse',
            'responsability',
            'risk',
            'obras_referencias',
            'obras_organigrama',
            'obras_equipos',
            'obras_cronograma',
            'obras_memoria',
            'obras_antecedentes',
            'tarima_ficha_tecnica',
            'tarima_licencia',
            'tarima_nom_144',
            'tarima_acreditacion',
            'list_prov',
            'cert_visita',
            'edificio_balance',
            'edificio_iva',
            'edificio_cuit',
            'edificio_brochure',
            'edificio_organigrama',
            'edificio_organigrama_obra',
            'edificio_subcontratistas',
            'edificio_gestion',
            'edificio_maquinas',


            'entrega_doc_evaluacion',
            'requisitos_legales',
            'experiencia_y_referencias',
            'repse_two',
            'alcance_two',
            'garantias',
            'forma_pago',
            'tiempo_fabricacion',
            'ficha_tecnica'




        ]);
    }

    public function getIsEconomicAttribute()
    {
        return in_array($this->attributes['code'], [
            'economic',
            'costs',
            'analisisApu'
        ]);
    }
}
