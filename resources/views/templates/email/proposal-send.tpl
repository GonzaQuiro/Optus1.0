{extends 'email/base.tpl'}

{block 'content'}
    Estimado <strong> {$cliente} </strong>
    <br><br>
    El proveedor invitado, <strong>{$proveedor}</strong><br>
    Ha enviado su propuesta técnica para la <b>{$nuevaRonda}</b> del <b>Nº Concurso</b>: {$concurso->id}, {$concurso->nombre} y está lista para su
    revisión
    <br><br>
    Le agradeceremos la revisión del mismo, ingresando a www.optus.com.ar.
    <br><br>
    Contamos con su colaboración.
{/block}