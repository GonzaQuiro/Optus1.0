<!-- ko if: UserType() != 'customer-approve' -->
<!-- ko if: 
    (AdjudicacionAnticipada() && IsRevisado()) || 
    (
        !AdjudicacionAnticipada() && 
        (
            (TodosPresentaronEconomica() || 
            PlazoVencidoEconomica()) && IsRevisado()
        )
    ) || Adjudicado()
-->
<table class="table table-striped table-bordered text-xsmall" id="ListaConcursosEconomicas">
    <thead class="text-center">
        <tr style="background: #ccc;">
            <th colspan="4" class="text-center"> Proveedor</th>
        </tr>
        <tr style="background: #ccc;">
            <th class="text-center"> Proveedor </th>
            <th class="text-center"> Comentario </th>
            <th class="text-center"> Propuesta Economica </th>
            <th class="text-center"> Estructura de Costos </th>
            <th class="text-center"> Análisis de Precios Unitarios (APU) </th>
        </tr>

    </thead>

    <tbody>
        <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
        <tr style="background: #fff;">
            <td class="text-center vertical-align-middle" data-bind="text: $data.razonSocial"></td>
            <td class="text-center vertical-align-middle" data-bind="text: $data.comentarios"></td>
            <td class="text-center vertical-align-middle">
                <!-- ko if: porpuesta_economica -->
                <a data-bind="click: $root.downloadFile.bind($data, porpuesta_economica, 'oferente', OferenteId)"
                    download class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !porpuesta_economica -->
                <span class="label label-danger">Sin archivo</span>
                <!-- /ko -->
            </td>
            <td class="text-center vertical-align-middle">
                <!-- ko if: planilla_costos -->
                <a data-bind="click: $root.downloadFile.bind($data, planilla_costos, 'oferente', OferenteId)" download
                    class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !planilla_costos -->
                <span class="label label-danger">Sin archivo</span>
                <!-- /ko -->
            </td>
            <td class="text-center vertical-align-middle">
                <!-- ko if: analisis_apu -->
                <a data-bind="click: $root.downloadFile.bind($data, analisis_apu, 'oferente', OferenteId)"
                    download class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !analisis_apu -->
                <span class="label label-danger">Sin archivo</span>
                <!-- /ko -->
            </td>
        </tr>
        <!-- /ko -->
    </tbody>
</table>

<!-- ko if: !IsGo() -->
<div class="table-scrollable">
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center" colspan="4"> <b>Primera Ronda de Ofertas</b> </th>
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> Moneda </th>
                <th colspan="2" class="text-center"
                    data-bind="text: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'][0]['items'][0]['moneda']">
                </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th colspan="3" class="text-center"
                    data-bind="style: { background: '#' + (Math.floor(Math.random() * 2 ** 24)).toString(16).padStart(0, 6)}">
                </th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr>
                <th class="text-center" colspan="3" style="background: #ccc;"> Proveedor </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Costo Total </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.total, precision: 0, style: { background: $data.mejorOfertaIntegral ?  '#c6e0b4' : '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Ahorro % </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.ahorro_porc, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Dif % vs Mejor Ofert </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.difvsmejorofert, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Eval Tecnica </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.evaluationalcanzada, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Plazo de Pago </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.plazoPago, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> ITEMS </th>
                <th class="text-center"> Cantidad<br>Solicitada </th>
                <th class="text-center"> Costo<br>Objetivo </th>
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th class="text-center"> Precio<br>Unitario </th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th class="text-center"> Cantidad<br>Cotizada </th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
                <th class="text-center"> Plazo de<br>entrega </th>
                <!-- /ko -->
            </tr>
        </thead>
        <!-- ko foreach: ConcursoEconomicasPrimeraRonda()[0]['cantidadesSolicitadas'] -->
        <thead class="text-center">
            <tr style="background: #fff;">
                <th data-bind="text: $parent.ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'][0]['items'][$index()].nombre"
                    class="text-left" style="background: #ccc;"> </th>
                <td data-bind="text: $data + ' - ' + $parent.ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'][0]['items'][$index()].unidad"
                    class="text-center vertical-align-middle"></td>
                <td data-bind="text: $parent.ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'][0]['items'][$index()].targetcost"
                    class="text-center vertical-align-middle"></td>
                <!-- ko foreach: $parent.ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
    <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle"
            data-bind="text: $data.items[$parentContext.$index()].cotizacion,
                       style: { background: $data.items[$parentContext.$index()].isMejorCotizacion ? '#c6e0b4' : '#ffffff' }">
        </td>
    <!-- /ko -->
    <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle">—</td>
    <!-- /ko -->
<!-- /ko -->

<!-- ko foreach: $parent.ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
    <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle"
            data-bind="number: $data.items[$parentContext.$index()].cantidad, precision: 0,
                       style: { background: $data.items[$parentContext.$index()].isMenorCantidad ? '#c6e0b4' : '#ffffff' }">
        </td>
    <!-- /ko -->
    <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle">—</td>
    <!-- /ko -->
<!-- /ko -->

                <!-- ko foreach: $parent.ConcursoEconomicasPrimeraRonda()[0]['oferenteItems'] -->
    <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
    <td class="text-center vertical-align-middle"
        data-bind="text: $data.items[$parentContext.$index()].fecha == 0 ? '—' : $data.items[$parentContext.$index()].fecha,
                   style: { background: $data.items[$parentContext.$index()].isMenorPlazo ? '#c6e0b4' : '#ffffff' }">
    </td>
    <!-- /ko -->
    <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
    <td class="text-center vertical-align-middle">—</td>
    <!-- /ko -->
<!-- /ko -->

            </tr>
        </thead>
        <!-- /ko -->
    </table>

</div>
<br><br>
<!-- ko if: HabilitaSegundaRonda() === 'si' -->
<div class="table-scrollable">
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center" colspan="4"> <b>Segunda Ronda de Ofertas</b> </th>
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> Moneda </th>
                <th colspan="2" class="text-center"
                    data-bind="text: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'][0]['items'][0]['moneda']">
                </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th colspan="3" class="text-center"
                    data-bind="style: { background: '#' + (Math.floor(Math.random() * 2 ** 24)).toString(16).padStart(0, 6)}">
                </th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr>
                <th class="text-center" colspan="3" style="background: #ccc;"> Proveedor </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Costo Total </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.total, precision: 0, style: { background: $data.mejorOfertaIntegral ?  '#c6e0b4' : '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Ahorro % </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.ahorro_porc, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Dif % vs Mejor Ofert </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.difvsmejorofert, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Eval Tecnica </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.evaluationalcanzada, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Plazo de Pago </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td data-bind="number: $data.plazoPago, precision: 0, style: { background: '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <td></td>
                <!-- /ko -->
            </tr>
        </thead>
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> ITEMS </th>
                <th class="text-center"> Cantidad<br>Solicitada </th>
                <th class="text-center"> Costo<br>Objetivo </th>
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th class="text-center"> Precio<br>Unitario </th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th class="text-center"> Cantidad<br>Cotizada </th>
                <!-- /ko -->
                <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
                <th class="text-center"> Plazo de<br>entrega </th>
                <!-- /ko -->
            </tr>
        </thead>
        <!-- ko foreach: ConcursoEconomicasSegundaRonda()[0]['cantidadesSolicitadas'] -->
        <thead class="text-center">
            <tr style="background: #fff;">
                <th data-bind="text: $parent.ConcursoEconomicasSegundaRonda()[0]['oferenteItems'][0]['items'][$index()].nombre"
                    class="text-left" style="background: #ccc;"> </th>
                <td data-bind="text: $data + ' - ' + $parent.ConcursoEconomicasSegundaRonda()[0]['oferenteItems'][0]['items'][$index()].unidad"
                    class="text-center vertical-align-middle"></td>
                <td data-bind="text: $parent.ConcursoEconomicasSegundaRonda()[0]['oferenteItems'][0]['items'][$index()].targetcost"
                    class="text-center vertical-align-middle"></td>
                <!-- ko foreach: $parent.ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
    <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle"
            data-bind="text: $data.items[$parentContext.$index()].cotizacion,
                       style: { background: $data.items[$parentContext.$index()].isMejorCotizacion ? '#c6e0b4' : '#ffffff' }">
        </td>
    <!-- /ko -->
    <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle">—</td>
    <!-- /ko -->
<!-- /ko -->

<!-- ko foreach: $parent.ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
    <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle"
            data-bind="number: $data.items[$parentContext.$index()].cantidad, precision: 0,
                       style: { background: $data.items[$parentContext.$index()].isMenorCantidad ? '#c6e0b4' : '#ffffff' }">
        </td>
    <!-- /ko -->
    <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
        <td class="text-center vertical-align-middle">—</td>
    <!-- /ko -->
<!-- /ko -->

                <!-- ko foreach: $parent.ConcursoEconomicasSegundaRonda()[0]['oferenteItems'] -->
    <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
    <td class="text-center vertical-align-middle"
        data-bind="text: $data.items[$parentContext.$index()].fecha == 0 ? '—' : $data.items[$parentContext.$index()].fecha,
                   style: { background: $data.items[$parentContext.$index()].isMenorPlazo ? '#c6e0b4' : '#ffffff' }">
    </td>
    <!-- /ko -->
    <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
    <td class="text-center vertical-align-middle">—</td>
    <!-- /ko -->
<!-- /ko -->

            </tr>
        </thead>
        <!-- /ko -->
    </table>

</div>
<!-- /ko -->
<!-- /ko -->

<!-- ko if: IsGo() -->
<div class="table-scrollable">
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> Proveedor </th>
                <th class="text-center"> Item </th>
                <th class="text-center"> Total </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
                <th class="text-center" data-bind="text: nombre"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead>
            <tr style="background: #ccc;">
                <th class="text-center"> </th>
                <th class="text-center"> Moneda </th>
                <th class="text-center"> </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
                <th class="text-center" data-bind="text: moneda"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead>
            <tr style="background: #ccc;">
                <th class="text-center"> </th>
                <th class="text-center"> Unidad de medida </th>
                <th class="text-center"> </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
                <th class="text-center" data-bind="text: unidad"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead>
            <tr style="background: #ccc;">
                <th class="text-center"> </th>
                <th class="text-center"> Cantidad solicitada </th>
                <th class="text-center"> </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['cantidadesSolicitadas'] -->
                <th class="text-center" data-bind="text: $data"></th>
                <!-- /ko -->
            </tr>
        </thead>

        <tbody data-bind="">
            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
            <!-- ko if: $data.total > 0 -->
            <tr style="background: #fff;">
                <td data-bind="text: $data.razonSocial" class="vertical-align-middle"></td>
                <td data-bind="text: 'Cotización'" class="vertical-align-middle"></td>
                <td data-bind="text: $data.total, style: { background: $data.mejorOfertaIntegral ?  '#ffffff' : '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- ko foreach: $data.items -->
                <td data-bind="number: $data.cotizacion, precision: 0, style: { background: $data.isMejorCotizacion ? '#ffffff' : '#ffffff' }"
                    class="text-center vertical-align-middle"></td>
                <!-- /ko -->
            </tr>
            <!-- /ko -->
            <!-- /ko -->
        </tbody>
    </table>
</div>
<!-- /ko -->
<!-- /ko -->
<!-- /ko -->

<!-- ko if: IsSobrecerrado() || IsGo() -->
<!-- ko if: 
    !AdjudicacionAnticipada() && !Adjudicado() &&
    (
        (!TodosPresentaronEconomica() && 
        !PlazoVencidoEconomica()) || !IsRevisado()
    ) || (AdjudicacionAnticipada() && !IsRevisado()) || UserType() == 'customer-approve'
-->
<table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
    <thead class="text-center">
        <tr style="background: #ccc;">
            <th class="text-center"> Proveedor </th>
            <th class="text-center"> Estado </th>
        </tr>
    </thead>
    <tbody>
        <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
        <tr style="background: #fff;">
            <td class="text-center vertical-align-middle" data-bind="text: $data.razonSocial"></td>
            <td class="text-center vertical-align-middle">
                <!-- ko if: $data.total > 0 -->
                <i class="fa fa-envelope fa-2x"></i>
                <!-- /ko -->
                <!-- ko if: $data.total === 0  -->
                <span>
                    <i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
                </span>
                <!-- /ko -->
            </td>
        </tr>
        <!-- /ko -->
    </tbody>
</table>
<!-- /ko -->
<!-- /ko -->