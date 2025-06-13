{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$user->full_name}<br><br>
    {$user->customer_company->business_name}<br><br>
    Le informamos que el <b>Nº Concurso</b>: {$concurso->id} de nombre {$concurso->nombre} ha sido cancelado correctamente.<br><br>
{/block}
