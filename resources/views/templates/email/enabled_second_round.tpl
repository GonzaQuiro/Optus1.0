{extends 'email/base.tpl'}

{block 'content'}
Estimado {$company_name}<br><br>

Lo invitamos a presentar una {$nuevaRonda} propuesta económica con plazo máximo a la fecha "{$date_limit}" para el <b>Nº Concurso</b>: {$concurso->id}, "{$concurso->nombre}".<br><br>
<strong> comentario: {$comentario} </strong><br><br>
Para cumplir esta etapa deberá ingresar a www.optus.com.ar. En caso de no cumplir este requisito, no podrá continuar participando del concurso.<br><br>
<b>¡Contamos con su participación!</b>
{/block}