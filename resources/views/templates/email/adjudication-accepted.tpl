{extends 'email/base.tpl'}

{block 'content'}
    {$company_name}<br><br>
    Le informados que su oferta económica para el concurso <b>Nº Concurso</b>: {$concurso->id} ha sido seleccionada.<br><br>

    <span class="text-bold">
        {$concurso->nombre}
    </span>
    <br><br>

    Items adjudicados:<br>

    <ul>
        {foreach from=$adjudicated_products item=$product}
            <li>{$product['itemNombre']}</li>
        {/foreach}
    </ul>

    Le solicitamos ingresar al concurso para aceptar o rechazar la adjudicación.<br><br>

    Esta adjudicación es de acuerdo a los términos y condiciones, pliegos generales y técnicos que formante parte del mismo,
    como así también de acuerdo las propuestas técnicas y económicas presentadas por vuestra empresa.<br><br>

    El presente correo no implica confirmación de inicio de actividades, prestación de servicios u
    otros. La misma deberá ser refrendada a través de correspondiente orden de compra.<br><br>
    <div style="width: 100%; text-align: right;">Atte, OPTUS – {$concurso->cliente->full_name} en representación de
        {$concurso->cliente->customer_company->business_name}.</div>
{/block}