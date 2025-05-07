{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$user->full_name}<br><br>
    {$user->customer_company->business_name}<br><br>
    Le informamos que el <b>Nº Concurso</b>: {$concurso->id} de nombre {$concurso->nombre} ha sido cancelado debido a que no
    posee ya ningún oferente que siga aún en competencia.<br><br>
{/block}