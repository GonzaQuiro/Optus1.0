{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$concurso->cliente->full_name}<br><br>
    {$concurso->cliente->customer_company->business_name}<br><br>

    Le informamos que el proeedor {$oferente->company->business_name} ha declinado su participación en el <b>Nº
        Concurso</b>: {$concurso->id}, {$concurso->nombre}.<br><br>

    <ul>
        <li><b>Nº Concurso</b>: {$concurso->id}</li>
        <li><b>Nombre de Concurso</b>: {$concurso->nombre}</li>
        <li><b>Etapa declinación</b>: {$etapa}</li>
        <li><b>Motivo de declinación</b>: {$reason}</li>
        <li><b>Fecha de declinación</b>: {$fecha_declination->format('d-m-Y')}<br><br></li>
    </ul>

    <div style="width: 100%; text-align: right;">Atte, OPTUS – {$concurso->cliente->full_name} en representación de
        {$concurso->cliente->customer_company->business_name}.</div>
{/block}