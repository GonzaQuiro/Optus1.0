{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>

    Le informamos que se ha revisado la etapa de precalificación técnica del <b>Nº Concurso</b>: {$concurso->id},
    <b>{$concurso->nombre}</b>.<br><br>
    Solicitamos una <b>{$nuevaRonda}</b> <br><br>
    <b>Comentario del comprador:</b><br><br>
    <b>{$comentario}</b><br><br>
    Recuerde que la fecha limite para presentar una nueva propuesta técnica es: <b>{$concurso->ficha_tecnica_fecha_limite}<br><br>
    Para cumplir esta etapa deberá ingresar a www.optus.com.ar. En caso de no cumplir este requisito, no podrá continuar participando del concurso.<br><br>
    <b>¡Contamos con su participación!</b><br><br>
    <div style="width: 100%; text-align: right;">Atte, {$concurso->cliente->customer_company->business_name} - OPTUS</div>
{/block}