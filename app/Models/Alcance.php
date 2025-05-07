<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\Ciudad;
use App\Models\OffererCompany;

class Alcance extends Model
{	
    protected $table = 'alcance';

    protected $fillable = [
        'id',
        'id_empresa_oferente',
        'id_pais',
        'id_provincia',
        'id_ciudad'
    ];

    public function offerer_company()
    {
    	return $this->belongsTo(OffererCompany::class, 'id', 'id_empresa_oferente');
    }

    public function country()
    {
        return $this->belongsTo(Pais::class, 'id_pais', 'id');
    }

    public function province()
    {
        return $this->belongsTo(Provincia::class, 'id_provincia', 'id');
    }

    public function city()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad', 'id');
    }
}