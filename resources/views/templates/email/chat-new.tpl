{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$concurso->cliente->customer_company->business_name}
    <br><br>
    Ha recibido una nueva consulta en el muro de {$user->full_name} de la empresa
    {$user->offerer_company->business_name} sobre el <b>Nº Concurso</b>: {$concurso->id}, {$concurso->nombre}.
    <br><br>
    Le agradeceremos responder la consulta antes de 24 hs. ingresando a www.optus.com.ar.
    Recuerde que su respuesta será compartida a todos los proveedores invitados a participar en el
    concurso.
    <br><br>
    Contamos con su colaboración.
{/block}