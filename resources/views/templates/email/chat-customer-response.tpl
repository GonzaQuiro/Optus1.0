{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}
    <br><br>
    Su consulta sobre el <b>Nº Concurso: {$concurso->id}, {$concurso->nombre}</b>, ha sido resuelta.
    <br>
    Para ver el contenido de la respuesta debe ingresar al muro de consulta de OPTUS en el concurso de referencia.
    <br><br>
    Recuerde que su consulta y la respuesta recibida será compartida a todos los proveedores invitados a participar en el
    concurso, brindando mas transparencia al proceso.
{/block}