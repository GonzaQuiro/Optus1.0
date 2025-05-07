{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>
Le recordamos que a la fecha no hemos recibido su oferta económica requerida en el <b>Nº Concurso</b>: {$concurso->id},  "{$concurso->nombre}".<br><br>
Para cumplir esta etapa deberá ingresar a www.optus.com.ar. En caso de no cumplir este requisito, no podrá continuar participando del concurso.<br><br>
<b>Contamos con su participación!</b>
{/block}