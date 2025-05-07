{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>
Le recordamos que el <b>{$concurso->inicio_subasta}</b> dara inicio a la subasta <b>Nº</b>: {$concurso->id},  "{$concurso->nombre}" realizada por {$concurso->cliente->full_name}.<br><br>
<b>Recomendaciones:</b><br><br>
<ul>
<li> Ingresar a la sala de subastas de OPTUS al menos 5 minutos antes del horario establecido<br><br></li>
<li> Asegurar conexión a internet estable<br><br></li>
<li> Ver videos tutoriales de subastas<br><br></li>
<li> Prestar atención a los requerimientos de ofertas: valores unitarios o costo total, moneda, unidad de medida, importes mínimos para mejorar su oferta<br><br></li>
</ul>
<b>Contamos con su participación!</b><br><br>
Atte, OPTUS - {$concurso->cliente->full_name}
{/block}