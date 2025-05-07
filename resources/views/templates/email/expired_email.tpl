{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>

    Le informamos que <b>Nº Concurso</b>: {$concurso->id} de nombre {$concurso->nombre} ha llegado a su fecha limite para la
    etapa <b>{$etapa}</b>.<br><br>
    Le agradecemos su participación hasta esta instancia.<br><br>
    <div style="width: 100%; text-align: right;">Atte, OPTUS – {$concurso->cliente->full_name} en representación de
        {$concurso->cliente->customer_company->business_name}.</div>
{/block}