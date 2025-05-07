{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>
Lo invitamos a presentar una segunda propuesta económica con plazo máximo a la fecha indicada para el <b>Nº Concurso</b>: {$concurso->id}, "{$concurso->nombre}".<br><br>
Para cumplir esta etapa deberá ingresar a www.optus.com.ar. En caso de no cumplir este requisito, no podrá continuar participando del concurso.<br><br>
<b>¡Contamos con su participación!</b>
{/block}