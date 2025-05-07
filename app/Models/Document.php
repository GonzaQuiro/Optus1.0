<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use App\Models\GoDocument;
use App\Models\DocumentType;

class Document extends Model
{
    use SoftDeletes;

    protected $table = 'documents';

    protected $fillable = [
        'id',
        'gcg_code',
        'name',
        'deleted_at'
    ];

    protected $apends = [
        'is_gcg',
        'is_driver',
        'is_vehicle',
        'is_trailer'
    ];

    public function document()
    {
    	return $this->hasOne(GoDocument::class, 'id_document', 'id');
    }

    public function types()
    {
        return $this->belongsToMany(DocumentType::class, 'document_type');
    }

    public function getIsGcgAttribute()
    {
        return substr($this->getAttribute('gcg_code'), 0, 6) === 'OPTUS_';
    }

    public function getIsDriverAttribute()
    {
        return $this->types->some('code', 'driver');
    }

    public function getIsVehicleAttribute()
    {
        return $this->types->some('code', 'vehicle');
    }

    public function getIsTrailerAttribute()
    {
        return $this->types->some('code', 'trailer');
    }

    public static function getListGcgDocumentsByType($type) 
    {
        $result =[];
        try {
            $documents = self::whereHas('types', function ($query) use ($type) {
                    $query->where('code', $type);
                })
                ->get()
                ->filter(function ($document) {
                    return $document->is_gcg;
                });

            foreach ($documents as $document) {
                $result[] = [
                    'id'            => (string) $document->id,
                    'text'          => $document->name,
                    'code'          => $document->gcg_code,
                    'is_driver'     => $document->types->some('code', 'driver'),
                    'is_vehicle'    => $document->types->some('code', 'vehicle'),
                    'is_trailer'    => $document->types->some('code', 'trailer')
                ];
            }
            
        } catch (\Exception $e) {

        }
        
        return $result;
    }
    
    public static function getListDocumentByCode($code)
    {
        $result = [];
        try {

            $document = self::where('gcg_code', $code)->get()->first();

            $result = [
                'id'    => $document->id,
                'text'  => $document->name
            ];
            
        } catch (\Exception $e) {

        }

        return $result;
    }
}