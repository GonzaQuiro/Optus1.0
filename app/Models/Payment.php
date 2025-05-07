<?php


namespace App\Models;

use App\Models\Model;
use App\Models\User;
use App\Models\Concurso;
use App\Models\Evaluacion;
use App\Models\ParticipanteGoDocument;
use App\Models\Participante;
use Carbon\Carbon;

class Payment extends Model
{
    protected $primaryKey = 'payments_id';

    protected $table = 'payments';

    protected $fillable = [
        'participante_id',
        'link',
        'paid',
        'preference',
        'title',
        'itemid',
        'payid',
        'orderid',
        'created_at',
        'updated_at'
    ];

    /**
     * relationship between Pagos and Participantes
     **/
    public function participante()
    {
        return $this->hasOne(Participante::class, 'id', 'participante_id');
    }

}
