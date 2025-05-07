{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>
Le recordamos que el {$concurso->finalizacion_consultas->format('d-m-Y H:i')} finaliza el plazo para realizar consultas relacionadas
al <b>Nº Concurso</b>: {$concurso->id},  {$concurso->nombre}.<br><br>
Le recomendamos hacer las consultas que consideren necesarias para poder cotizar con mayor precisión.<br><br>
<div style="width: 100%; text-align: right;">Atte, {$concurso->cliente->full_name} - OPTUS</div> 
{/block}