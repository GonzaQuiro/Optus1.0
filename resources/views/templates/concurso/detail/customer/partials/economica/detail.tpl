<!-- ko if: 
    (AdjudicacionAnticipada() && isRevisada()) || 
    (
        !AdjudicacionAnticipada() && 
        (
            TodosPresentaronEconomica() || 
            PlazoVencidoEconomica()
        )
    )
-->
<table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
    <thead class="text-center">
        <tr style="background: #ccc;">
            <th class="text-center"> Oferente </th>
            <th class="text-center"> Comentario </th>
            <th class="text-center"> Archivo </th>
        </tr>
    </thead>
    <tbody>
        <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
        <tr style="background: #fff;">
            <td class="text-center vertical-align-middle" data-bind="text: $data.razonSocial"></td>
            <td class="text-center vertical-align-middle" data-bind="text: $data.comentarios"></td>
            <td class="text-center vertical-align-middle">
                <!-- ko if: archivo -->
                <a data-bind="click: $root.downloadFile.bind($data, archivo, 'oferente', OferenteId)" download class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !archivo -->
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
                <th class="text-center"> Oferente </th>
                <th class="text-center"> Item </th>
                <th class="text-center"> Total </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
                <th class="text-center" data-bind="text: nombre"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead>
            <tr style="background: #ccc;">
                <th class="text-center">  </th>
                <th class="text-center"> Moneda </th>
                <th class="text-center">  </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
                <th class="text-center" data-bind="text: moneda"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead>
            <tr style="background: #ccc;">
                <th class="text-center">  </th>
                <th class="text-center"> Unidad de medida </th>
                <th class="text-center">  </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
                <th class="text-center" data-bind="text: unidad"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <thead>
            <tr style="background: #ccc;">
                <th class="text-center">  </th>
                <th class="text-center"> Cantidad solicitada </th>
                <th class="text-center">  </th>
                <!-- ko foreach: ConcursoEconomicas()[0]['cantidadesSolicitadas'] -->
                <th class="text-center" data-bind="text: $data"></th>
                <!-- /ko -->
            </tr>
        </thead>
        <tbody>
            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
            <!-- ko if: $data.total > 0 -->
            <tr style="background: #fff;">
                <td data-bind="text: $data.razonSocial" class="vertical-align-middle"></td>
                <!--<td data-bind="text: 'Cotización'" class="vertical-align-middle"></td>-->
                <td data-bind="text: 'Precio unitario'" class="vertical-align-middle"></td>
                <td data-bind="number: $data.total, precision: 0, style: { background: $data.mejorOfertaIntegral ?  '#c6e0b4' : '#ffffff' }" class="text-center vertical-align-middle"></td>
                <!-- ko foreach: $data.items -->
                <td data-bind="number: $data.cotizacion, precision: 0, style: { background: $data.isMejorCotizacion ? '#c6e0b4' : '#ffffff' }" class="text-center vertical-align-middle"></td>
                <!-- /ko -->
            </tr>
            <!-- /ko -->
            <!-- /ko -->

            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
            <!-- ko if: $data.total > 0 -->
            <tr style="background: #fff;">
                <td data-bind="text: $data.razonSocial" class="vertical-align-middle"></td>
                <td data-bind="text: 'Cantidad cotizada'" class="vertical-align-middle"></td>
                <td data-bind="text: ''" class="text-center vertical-align-middle"></td>
                <!-- ko foreach: $data.items -->
                <td data-bind="text: $data.cantidad, style: { background: $data.isMenorCantidad ? '#c6e0b4' : '#ffffff' }" class="text-center vertical-align-middle"></td>
                <!-- /ko -->
            </tr>
            <!-- /ko -->
            <!-- /ko -->

            <!-- ko if: IsSobrecerrado() -->
            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'] -->
            <!-- ko if: $data.total > 0 -->
            <tr style="background: #fff;">
                <td data-bind="text: $data.razonSocial" class="vertical-align-middle"></td>
                <td data-bind="text: 'Plazo de entrega (días)'" class="vertical-align-middle"></td>
                <td data-bind="text: ''" class="text-center vertical-align-middle"></td>
                <!-- ko foreach: $data.items -->
                <td data-bind="text: $data.fecha, style: { background: $data.isMenorPlazo ? '#c6e0b4' : '#ffffff' }" class="text-center vertical-align-middle"></td>
                <!-- /ko -->
            </tr>
            <!-- /ko -->
            <!-- /ko -->
            <!-- /ko -->
        </tbody>
    </table>
</div>
<!-- /ko -->

<!-- ko if: IsGo() -->
<div class="table-scrollable">
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead class="text-center">
        <tr style="background: #ccc;">
            <th class="text-center"> Oferente </th>
            <th class="text-center"> Item </th>
            <th class="text-center"> Total </th>
            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
            <th class="text-center" data-bind="text: nombre"></th>
            <!-- /ko -->
        </tr>
        </thead>
        <thead>
        <tr style="background: #ccc;">
            <th class="text-center">  </th>
            <th class="text-center"> Moneda </th>
            <th class="text-center">  </th>
            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
            <th class="text-center" data-bind="text: moneda"></th>
            <!-- /ko -->
        </tr>
        </thead>
        <thead>
        <tr style="background: #ccc;">
            <th class="text-center">  </th>
            <th class="text-center"> Unidad de medida </th>
            <th class="text-center">  </th>
            <!-- ko foreach: ConcursoEconomicas()[0]['oferenteItems'][0]['items'] -->
            <th class="text-center" data-bind="text: unidad"></th>
            <!-- /ko -->
        </tr>
        </thead>
        <thead>
        <tr style="background: #ccc;">
            <th class="text-center">  </th>
            <th class="text-center"> Cantidad solicitada </th>
            <th class="text-center">  </th>
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
            <td data-bind="text: $data.total, style: { background: $data.mejorOfertaIntegral ?  '#ffffff' : '#ffffff' }" class="text-center vertical-align-middle"></td>
            <!-- ko foreach: $data.items -->
            <td data-bind="number: $data.cotizacion, precision: 0, style: { background: $data.isMejorCotizacion ? '#ffffff' : '#ffffff' }" class="text-center vertical-align-middle"></td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
        <!-- /ko -->
        </tbody>
    </table>
</div>
<!-- /ko -->
<!-- /ko -->

<!-- ko if: IsSobrecerrado() || IsGo() -->
<!-- ko if: 
    (!AdjudicacionAnticipada() && 
    (
        !TodosPresentaronEconomica() && 
        !PlazoVencidoEconomica()
    )) || (AdjudicacionAnticipada() && !isRevisada()) 
-->
<table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
    <thead class="text-center">
        <tr style="background: #ccc;">
            <th class="text-center"> Oferente </th>
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