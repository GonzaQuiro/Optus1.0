{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>

    Le informamos que se ha completado la etapa de precalificación técnica del <b>Nº Concurso</b>: {$concurso->id},
    <b>{$concurso->nombre}</b>.<br><br>
    Resultado de su propuesta técnica: <b>{if $accepted}ACEPTADA{else}DENEGADA{/if}</b>.<br><br>
    {if $accepted}
        {if $concurso->is_online}
            A partir de este momento se encuentra habilitado para participar de la subasta online.<br><br>
        {elseif $concurso->is_sobrecerrado || $concurso->is_go}
            A partir de este momento se encuentra habilitado para presentar su oferta económica.<br><br>
        {/if}
        Para mayor información lo invitamos a consultar el concurso desde www.optus.com.ar<br><br>
    {else}
        Le agradecemos su interés y esfuerzo en la participación del proceso.<br><br>
        El motivo es: <b>{$comentario}</b>
    {/if}
    <div style="width: 100%; text-align: right;">Atte, {$concurso->cliente->customer_company->business_name} - OPTUS</div>
{/block}