<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\Document;

class DocumentType extends Model
{
    use SoftDeletes;
    
    protected $table = 'document_types';

    protected $fillable = [
        'id',
        'code'
    ];

    protected $appends = [
        'description'
    ];

    const TYPE_SLUGS = [
        'driver'    => 'driver',
        'vehicle'   => 'vehicle',
        'trailer'   => 'trailer'
    ];

    const TYPE_DESCRIPTIONS = [
        'driver'    => 'Conductor',
        'vehicle'   => 'Vehículo',
        'trailer'   => 'Patente Tractor'
    ];

    public function documents()
    {
        return $this->belongsToMany(Document::class, 'document_type');
    }

    public function getDescriptionAttribute()
    {
        return $this::TYPE_DESCRIPTIONS[$this->attributes['code']];
    }
}