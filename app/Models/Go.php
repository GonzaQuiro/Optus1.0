<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Concurso;
use App\Models\GoType;
use App\Models\GoPaymentMethod;
use App\Models\Provincia;
use App\Models\Ciudad;
use App\Models\Document;
use App\Models\GoDocument;
use App\Models\GoDocumentAdditional;
use Carbon\Carbon;

class Go extends Model
{
    use SoftDeletes;
    
    protected $table = 'gos';

    protected $casts = [
        'ancho' => 'float',
        'largo' => 'float',
        'alto' => 'float'
    ];

    protected $dates = [
        'fecha_desde',
        'fecha_hasta'
    ];

    protected $fillable = [
        'id',
        'type_id',
        'load_type_id',
        'payment_method_id',
        'peso',
        'ancho',
        'largo',
        'alto',
        'unidades_bultos',
        'plazo_pago',
        'fecha_desde',
        'fecha_hasta',
        'calle_desde',
        'calle_hasta',
        'numeracion_desde',
        'numeracion_hasta',
        'ciudad_desde_id',
        'ciudad_hasta_id',
        'provincia_desde_id',
        'provincia_hasta_id',
        'nombre_desde',
        'nombre_hasta',
        'cotizar_seguro',
        'suma_asegurada',
        'cotizar_armada'
    ];

    protected $appends = [
        'driver_gcg_documents',
        'vehicle_gcg_documents',
        'trailer_gcg_documents'
    ];

    public function concurso()
    {
    	return $this->hasOne(Concurso::class, 'id_go', 'id');
    }

    public function type()
    {
    	return $this->belongsTo(GoType::class, 'type_id', 'id');
    }

    public function load_type()
    {
    	return $this->belongsTo(GoLoadType::class, 'load_type_id', 'id');
    }

    public function payment_method()
    {
    	return $this->belongsTo(GoPaymentMethod::class, 'payment_method_id', 'id');
    }

    public function province_from()
    {
    	return $this->hasOne(Provincia::class, 'id', 'provincia_desde_id');
    }

    public function province_to()
    {
    	return $this->hasOne(Provincia::class, 'id', 'provincia_hasta_id');
    }

    public function city_from()
    {
    	return $this->hasOne(Ciudad::class, 'id', 'ciudad_desde_id');
    }

    public function city_to()
    {
    	return $this->hasOne(Ciudad::class, 'id', 'ciudad_hasta_id');
    }

    public function documents()
    {
        return $this->hasMany(GoDocument::class, 'id_go', 'id');
    }

    public function additional_documents()
    {
        return $this->hasMany(GoDocumentAdditional::class, 'id_go', 'id');
    }

    public function getDriverGcgDocumentsAttribute()
    {
        return $this->documents->pluck('document')
            ->where('is_gcg', true)
            ->where('is_driver', true);
    }

    public function getVehicleGcgDocumentsAttribute()
    {
        return $this->documents->pluck('document')
            ->where('is_gcg', true)
            ->where('is_vehicle', true);
    }

    public function getTrailerGcgDocumentsAttribute()
    {
        return $this->documents->pluck('document')
            ->where('is_gcg', true)
            ->where('is_trailer', true);
    }
} 