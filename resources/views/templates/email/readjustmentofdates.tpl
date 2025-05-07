{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>

    Le informamos ajustes en el concurso <b>{$concurso->nombre}</b>, administrado por
    {$concurso->cliente->full_name} en representación de {$concurso->cliente->customer_company->business_name}.<br><br>

    <ul>

        <li><b>Nº Concurso</b>: {$concurso->id}</li>
        <li><b>Nombre de Concurso</b>: {$concurso->nombre}</li>
        <li><b>Reseña</b>: {$concurso->resena}<br><br></li>
        <h4>Se Han realizado los siguientes cambios:</h4>
        
        {if $ajustdates}
            <h3>Ajustes de fecha de la {$tipoConcurso}</h3>
            <li><b>Fecha cierre de muro de consultas</b>: {$concurso->finalizacion_consultas->format('d-m-Y H:i')}<br><br></li>
            <li><b>Fecha límite para presentación de ofertas técnicas</b>: {$fecha_tecnica}<br><br></li>
            {if $concurso->is_go || $concurso->is_sobrecerrado}
                <li><b>Fecha límite para presentación de ofertas económicas</b>:
                    {$concurso->fecha_limite_economicas->format('d-m-Y H:i')}<br><br></li>
            {elseif $concurso->is_online}
                <li><b>Inicio de la subasta</b>: {$concurso->inicio_subasta->format('d-m-Y H:i:s')} hs. </li>
            {/if}
        {/if}
        {if $documentChange}
            <li>
                <h3>Se han cambiado los documentos de la {$tipoConcurso}</h3>
            </li>
        {/if}
        {if $documentDeleted}
            <li>
                <h3>Se han eliminado documentos en la {$tipoConcurso}</h3>
            </li>
        {/if}
        {if $productsDeleted}
            <li>
                <h3>Se han eliminado los siguientes productos de la {$tipoConcurso}</h3>
            </li>
            <ul>
                {foreach from=$listProductsDeleted item=$product}
                    <li>{$product['nombre']}</li>
                {/foreach}
            </ul>
        {/if}
        {if $productsUpdated}
            <li>
                <h3>Se han actualizado los siguientes productos de la {$tipoConcurso}</h3>
            </li>
            <ul>
                {foreach from=$listProductsUpdated item=$product}
                    <li>{$product['nombre']}</li>
                {/foreach}
            </ul>
        {/if}
        {if $productsNew}
            <li>
                <h3>Se han agregado los siguientes productos a la {$tipoConcurso}</h3>
            </li>
            <ul>
                {foreach from=$listProductsNew item=$product}
                    <li>{$product['nombre']}</li>
                {/foreach}
            </ul>
        {/if}
        {if $technicalAdded}
            <li>
                <h3>Se ha agregado la etapa técnica a la {$tipoConcurso}</h3>
            </li>
        {/if}
        {if $technicalDeleted}
            <li>
                <h3>Se ha eliminado la etapa técnica de la {$tipoConcurso}</h3>
            </li>
        {/if}
        {if $technicalChanged}
            <li>
                <h3>Se ha editado la etapa técnica de la {$tipoConcurso}</h3>
            </li>
        {/if}
        {if $concurso->plantilla_tecnica}
            {if $tecnicalDocuments}
                <li>
                    <h3>Se han cambiado los siguientes documentos en la etapa técnica de la {$tipoConcurso}</h3>
                </li>
                <ul>
                    {foreach from=$listTecnicalDocuments item=$doc}
                        <li>{$doc}</li>
                    {/foreach}
                </ul>
            {/if}
        {/if}
        {if $ajustDocumentsEconomica}
            <li>
                <h3>Se ha editado la etapa económica de la {$tipoConcurso}</h3>
            </li>
            <ul>
                {foreach from=$listDocumentsEconomica item=$doc}
                    <li>{$doc}</li>
                {/foreach}
            </ul>
        {/if}

    </ul>
    Para consultas de pliegos, bases y condiciones generales, le solicitamos ingresar a https://optus-portal.com/, donde encontrará
    mayor información.<br><br>
    Contamos con su participación.<br><br>
    <b>¡Una nueva oportunidad lo está esperando!</b>
{/block}