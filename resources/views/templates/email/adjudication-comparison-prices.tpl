{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>

    A continuación le compartimos el resultados de todas las cotizaciones recibidas para el concurso <b>Nº Concurso</b>:
    {$concurso->id}.<br><br>

    <span class="text-bold">
        {$concurso->nombre}
    </span>
    <br><br>

    Comparativas de ofertas Primera Ronda:<br>

    <table class="table table-striped table-bordered" style="width: 90%; text-align: center;">
        <thead>
            <tr style="background: #ccc;">
                <th> Proveedor </th>
                <th> Comentario </th>
            </tr>
        </thead>
        <tbody>

            {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'] item=$economica}
                <tr style="background: #fff;">
                    <td style="vertical-align: middle;">{$economica['razonSocial']}</td>
                    <td style="vertical-align: middle;">{$economica['comentarios']}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>

    <br><br>

    <table class="table table-striped table-bordered" style="width: 90%;">
        <thead style="text-align: center;">
            <tr style="background: #ccc;">
                <th> Proveedor </th>
                <th> Item </th>
                <th> Total </th>
                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'][0]['items'] item=$item}
                    <th class="text-center">{$item['nombre']}</th>
                {/foreach}
            </tr>
        </thead>
        <thead style="text-align: center;">
            <tr style="background: #ccc;">
                <th> </th>
                <th> Moneda </th>
                <th> </th>
                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'][0]['items'] item=$item}
                    <th>{$item['moneda']}</th>
                {/foreach}
            </tr>
        </thead>
        <thead style="text-align: center;">
            <tr style="background: #ccc;">
                <th> </th>
                <th> Unidad de medida </th>
                <th> </th>
                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'][0]['items'] item=$item}
                    <th>{$item['unidad']}</th>
                {/foreach}
            </tr>
        </thead>
        <thead style="text-align: center;">
            <tr style="background: #ccc;">
                <th> </th>
                <th> Cantidad solicitada </th>
                <th> </th>
                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['cantidadesSolicitadas'] item=$item}
                    <th>{$item}</th>
                {/foreach}
            </tr>
        </thead>
        <tbody>
            {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'] item=$item}
                {if $item['total'] > 0}
                    <tr style="background: #fff;">
                        <td class="vertical-align-middle">{$item['razonSocial']}</td>
                        <td class="vertical-align-middle">Precio unitario</td>
                        {if $item['mejorOfertaIntegral']}
                            <td style="background: #c6e0b4; text-align: center; vertical-align: middle;">{$item['total']}</td>
                        {else}
                            <td style="background: #ffffff; text-align: center; vertical-align: middle;">{$item['total']}</td>
                        {/if}
                        {foreach from=$item['items'] item=$subitem}
                            {if $subitem['isMejorCotizacion']}
                                <td style="background: #c6e0b4; text-align: center; vertical-align: middle;">{$subitem['cotizacion']}</td>
                            {else}
                                <td style="background: #ffffff; text-align: center; vertical-align: middle;">{$subitem['cotizacion']}</td>
                            {/if}
                        {/foreach}
                    </tr>
                {/if}
            {/foreach}

            {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'] item=$item}
                {if $item['total'] > 0}
                    <tr style="background: #fff;">
                        <td style="vertical-align: middle;">{$item['razonSocial']}</td>
                        <td style="vertical-align: middle;">Cantidad cotizada</td>
                        <td style="text-align: center; vertical-align: middle;"></td>
                        {foreach from=$item['items'] item=$subitem}
                            {if $subitem['isMenorCantidad']}
                                <td style="background: #c6e0b4; text-align: center; vertical-align: middle;">{$subitem['cantidad']}</td>
                            {else}
                                <td style="background: #ffffff; text-align: center; vertical-align: middle;">{$subitem['cantidad']}</td>
                            {/if}
                        {/foreach}
                    </tr>
                {/if}
            {/foreach}
        </tbody>
    </table>

    <br><br>


    {if ($concurso->segunda_ronda_habilita == 'si')}
        Comparativas de ofertas Segunda Ronda:<br>

        <table class="table table-striped table-bordered" style="width: 90%; text-align: center;">
            <thead>
                <tr style="background: #ccc;">
                    <th> Proveedor </th>
                    <th> Comentario </th>
                </tr>
            </thead>
            <tbody>

                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'] item=$economica}
                    <tr style="background: #fff;">
                        <td style="vertical-align: middle;">{$economica['razonSocial']}</td>
                        <td style="vertical-align: middle;">{$economica['comentarios']}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

        <br><br>

        <table class="table table-striped table-bordered" style="width: 90%;">
            <thead style="text-align: center;">
                <tr style="background: #ccc;">
                    <th> Proveedor </th>
                    <th> Item </th>
                    <th> Total </th>
                    {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'][0]['items'] item=$item}
                        <th class="text-center">{$item['nombre']}</th>
                    {/foreach}
                </tr>
            </thead>
            <thead style="text-align: center;">
                <tr style="background: #ccc;">
                    <th> </th>
                    <th> Moneda </th>
                    <th> </th>
                    {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'][0]['items'] item=$item}
                        <th>{$item['moneda']}</th>
                    {/foreach}
                </tr>
            </thead>
            <thead style="text-align: center;">
                <tr style="background: #ccc;">
                    <th> </th>
                    <th> Unidad de medida </th>
                    <th> </th>
                    {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'][0]['items'] item=$item}
                        <th>{$item['unidad']}</th>
                    {/foreach}
                </tr>
            </thead>
            <thead style="text-align: center;">
                <tr style="background: #ccc;">
                    <th> </th>
                    <th> Cantidad solicitada </th>
                    <th> </th>
                    {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['cantidadesSolicitadas'] item=$item}
                        <th>{$item}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'] item=$item}
                    {if $item['total'] > 0}
                        <tr style="background: #fff;">
                            <td class="vertical-align-middle">{$item['razonSocial']}</td>
                            <td class="vertical-align-middle">Precio unitario</td>
                            {if $item['mejorOfertaIntegral']}
                                <td style="background: #c6e0b4; text-align: center; vertical-align: middle;">{$item['total']}</td>
                            {else}
                                <td style="background: #ffffff; text-align: center; vertical-align: middle;">{$item['total']}</td>
                            {/if}
                            {foreach from=$item['items'] item=$subitem}
                                {if $subitem['isMejorCotizacion']}
                                    <td style="background: #c6e0b4; text-align: center; vertical-align: middle;">{$subitem['cotizacion']}
                                    </td>
                                {else}
                                    <td style="background: #ffffff; text-align: center; vertical-align: middle;">{$subitem['cotizacion']}
                                    </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/if}
                {/foreach}

                {foreach from=$listEconomicas['ConcursoEconomicasPrimeraRonda'][0]['oferenteItems'] item=$item}
                    {if $item['total'] > 0}
                        <tr style="background: #fff;">
                            <td style="vertical-align: middle;">{$item['razonSocial']}</td>
                            <td style="vertical-align: middle;">Cantidad cotizada</td>
                            <td style="text-align: center; vertical-align: middle;"></td>
                            {foreach from=$item['items'] item=$subitem}
                                {if $subitem['isMenorCantidad']}
                                    <td style="background: #c6e0b4; text-align: center; vertical-align: middle;">{$subitem['cantidad']}</td>
                                {else}
                                    <td style="background: #ffffff; text-align: center; vertical-align: middle;">{$subitem['cantidad']}</td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/if}
                {/foreach}
            </tbody>
        </table>

        <br><br>
    {/if}

    <div style="width: 100%; text-align: right;">Atte, OPTUS – {$concurso->cliente->full_name} en representación de
        {$concurso->cliente->customer_company->business_name}.</div>
{/block}