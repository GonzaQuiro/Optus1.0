{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$oferente->full_name}, la pregunta que usted ha realizado en el muro de consultas
    del <b>Nº Concurso</b>: {$concurso->id}, {$concurso->nombre} ha sido <b>{$message->nombre_estado}.</b>
    <br><br>
    <b>La pregunta fué:</b> {$message->mensaje}
    <br><br>
    <div style="width: 100%; text-align: right;">
        Atte, OPTUS - {$concurso->cliente->full_name} en representación de
        {$concurso->cliente->customer_company->business_name}.
    </div>
{/block}