{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$concurso->cliente->full_name}<br><br>
    {$concurso->cliente->customer_company->business_name}<br><br>

    Le informamos que el proveedor {$oferente->company->business_name} ha rechazado la adjudicacion para el <b>Nº
        Concurso</b>: {$concurso->id}, {$concurso->nombre}.<br><br>

    <div style="width: 100%; text-align: right;">Atte, OPTUS – {$concurso->cliente->full_name} en representación de
        {$concurso->cliente->customer_company->business_name}.</div>
{/block}