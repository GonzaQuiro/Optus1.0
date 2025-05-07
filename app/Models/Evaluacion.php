<?php 

namespace App\Models;

use App\Models\Model;
use App\Models\Participante;
use Carbon\Carbon;

class Evaluacion extends Model 
{
	protected $primaryKey = 'id';
    
    protected $table = 'evaluacion_x_oferente';

    protected $fillable = [
        'id',
    	'id_participante',
    	'valores',
    	'comentario'
    ];

    const VALUES = [
        '0' => 'No aplica',
        '1' => 'No cumple',
        '2' => 'Cumple levemente',
        '3' => 'Cumple',
        '4' => 'Supera',
    ];

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'id_participante', 'id');
    }
}