{extends 'email/base.tpl'}

{block 'content'}
<p>Estimado <strong>{$oferente->user->full_name} / {$oferente->user->offerer_company->business_name} </strong></p>
<p>Le informamos que adeuda la siguiente documentación la cual es requisito obligatorio para poder participar del <b>Nº Concurso</b>: {$concurso->id} de fletes "<strong>{$concurso->nombre}</strong>"</p>
<p><strong>Conductor: {$driver_description} </strong></p>
<ul>
    {foreach from=$driver_documents item=$document}
        <li>{$document['name']} ({$document['message']})</li>
    {/foreach}
    {if count($driver_documents) == 0}
        No adeuda documentación.
    {/if}
</ul>
<p><strong>Vehículo: {$vehicle_description} </strong></p>
<ul>
    {foreach from=$vehicle_documents item=$document}
        <li>{$document['name']} ({$document['message']})</li>
    {/foreach}
    {if count($vehicle_documents) == 0}
        No adeuda documentación.
    {/if}
</ul>
<p><strong>Vehículo: {$trailer_description} </strong></p>
<ul>
    {foreach from=$trailer_documents item=$document}
        <li>{$document['name']} ({$document['message']})</li>
    {/foreach}
    {if count($trailer_documents) == 0}
        No adeuda documentación.
    {/if}
</ul>

<p>Enviar la documentación a <strong><email>documentacion@optus.com.ar</email></strong></p>

Atte OPTUS
{/block}