<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Concurso;
use App\Models\User;

class Mensaje extends Model
{
    protected $table = 'muro_consultas';

    const STATUS = [
        0 => 'Rechazada',
        1 => 'Aprobada',
        2 => 'Moderación'
    ];

    protected $fillable = [
        'id',
        'usr_id',
        'cso_id',
        'fecha',
        'mensaje',
        'estado',
        'leido',
        'parent',
        'tipo', 
        'filename',
        'to'
    ];

    protected $appends = [
        'nombre_estado',
        'is_rejected',
        'is_approved',
        'is_pending',
        'users_read',
        'tipo_mensaje',
        'user_read'
    ];

    public function concurso()
    {
        return $this->belongsTo(Concurso::class, 'cso_id', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usr_id', 'id');
    }

    public function getNombreEstadoAttribute()
    {
        return $this::STATUS[$this->attributes['estado']];
    }

    public function getIsRejectedAttribute()
    {
        return $this->attributes['estado'] === 0;
    }

    public function getIsApprovedAttribute()
    {
        return $this->attributes['estado'] === 1;
    }

    public function getIsPendingAttribute()
    {
        return $this->attributes['estado'] === 3;
    }

    public function getUsersReadAttribute()
    {
        $result = collect();

        foreach (explode(',', $this->attributes['leido']) as $user_id) {
            $result = $result->push(User::find((int) $user_id));
        }

        return $result;
    }

    public function getTipoMensajeAttribute() {
        if($this->attributes['tipo'] == 'aviso') return 'Aviso';
        if($this->attributes['tipo'] == 'tecnica') return 'Técnica';
        if($this->attributes['tipo'] == 'comercial') return 'Comercial';
        
    }

    public function getUserReadAttribute()
    {
        $userRead = false;
        $userlogin = user();
        $usersRead = explode(',', $this->attributes['leido']);
        $userRead = in_array($userlogin->id, $usersRead );
        return $userRead;
    }

    public function scopeTipo($query, $tipo)
    {
        if (!is_null($tipo)) {
            return $query->where('tipo', $tipo);
        }

        return $query;
    }
    public function scopeCategoria($query, $categoria)
    {
        if (!is_null($categoria)) {
            return $categoria == 'si' ? $query->whereHas('respuestas') : $query->WhereDoesntHave('respuestas');
        }

        return $query;
    }
    public function scopeEstado($query, $estado)
    {
        $userlogin = user()->id;
        
        if (!is_null($estado)) {
            if($estado == 1) return $query->where('estado', 1);
            if($estado == 2) return $query->where('estado', 2);
            if ($estado == 'leidas') {
                // Obtener preguntas y respuestas no leídas por el usuario
                return $query->where(function ($q) use ($userlogin) {
                    $q->leido($userlogin)
                      ->orWhereHas('respuestas', function ($q) use ($userlogin) {
                          $q->leido($userlogin);
                      });
                });
            }
            if ($estado == 'no_leidas') {
                // Obtener preguntas y respuestas no leídas por el usuario
                return $query->where(function ($q) use ($userlogin) {
                    $q->noLeido($userlogin)
                      ->orWhereHas('respuestas', function ($q) use ($userlogin) {
                          $q->noLeido($userlogin);
                      });
                });
            }            
        }

        return $query;
    }

    public function respuestas()
    {
        return $this->hasMany(Mensaje::class, 'parent', 'id');
    }

    public function scopeLeido($query, $user)
    {
        return $query->whereRaw('FIND_IN_SET(?, leido)', [$user]);
    }

    public function scopeNoLeido($query, $user)
    {
        $query->whereRaw('NOT FIND_IN_SET(?, leido)', [$user]);
    }

    public function scopeRespuestasLeidas($query, $userId)
    {
        return $query->whereHas('respuestas', function($q) use ($userId) {
            $q->leido($userId);
        });
    }

    public function scopeRespuestasNoLeidas($query, $userId)
    {
        return $query->whereHas('respuestas', function($q) use ($userId) {
            $q->noLeido($userId);
        });
    }

    public function scopeUserType($query){
        $user = user();
        if(isOfferer()){
            return $query->where('to', $user->offerer_company->id)->orWhere('to', 0);
        }else{
            return $query;
        }
    }
    
    public function scopeTiposPreguntas($query, $tiposPreguntas){
        if (is_null($tiposPreguntas) || $tiposPreguntas == 'todas'){
            return $query;
        }else{
            return $query->where('to', $tiposPreguntas);
        }
    }

    public function messageTo()
    {
        return $this->belongsTo(Participante::class, 'to', 'id_offerer');
    }
}