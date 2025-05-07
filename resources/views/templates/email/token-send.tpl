{extends 'email/base.tpl'}

{block 'content'}
    Estimado <strong> {$concurso->supervisor->full_name} </strong>
    <br><br>
    El Comprador desea ver las ofertas del concurso <b>Nº Concurso</b>: {$concurso->id}, {$concurso->nombre}
    <br><br> 
    El codigo es: <b>{$token}</b>
    <br><br>
    Le agradeceremos ponerse en contacto con el comprador para continuar con el concurso.
    <br><br>
    Contamos con su colaboración.
{/block}