<!-- Encabezado h4 a la izquierda -->
<h4 class="bold" style="margin-top: 0; padding-top: 0;">Comparativas de ofertas</h4>

<table class="table table-striped table-bordered text-xsmall" id="ListaConcursosEconomicas">
    <thead class="text-center">
        <tr style="background: #ccc;">
            <th class="text-center"> Proveedor </th>
            <th class="text-center"> Comentario </th>
            <th class="text-center"> Propuesta Económica </th>
            <th class="text-center"> Estructura de Costos </th>
            <th class="text-center"> Análisis de Precios Unitarios (APU) </th>
        </tr>
    </thead>
    <!-- NOTA: el </thead> debe ir antes de <tbody> -->
    <tbody>
        <!-- ko foreach: ConcursoEconomicas.proveedores -->
        <tr style="background: #fff;">
            <!-- ko ifnot: isRechazado -->
                <td class="text-center vertical-align-middle" data-bind="text: $data.razonSocial"></td>
                <td class="text-center vertical-align-middle" data-bind="text: $data.comentarios"></td>
                <td class="text-center vertical-align-middle">
                    <!-- ko if: porpuesta_economica -->
                        <a data-bind="click: $root.downloadFile.bind($data, porpuesta_economica, 'oferente', OferenteId)"
                           download class="btn btn-xl green" title="Descargar">
                            Descargar <i class="fa fa-download"></i>
                        </a>
                    <!-- /ko -->
                    <!-- ko ifnot: porpuesta_economica -->
                        <span class="label label-danger">Sin archivo</span>
                    <!-- /ko -->
                </td>
                <td class="text-center vertical-align-middle">
                    <!-- ko if: planilla_costos -->
                        <a data-bind="click: $root.downloadFile.bind($data, planilla_costos, 'oferente', OferenteId)"
                           download class="btn btn-xl green" title="Descargar">
                            Descargar <i class="fa fa-download"></i>
                        </a>
                    <!-- /ko -->
                    <!-- ko ifnot: planilla_costos -->
                        <span class="label label-danger">Sin archivo</span>
                    <!-- /ko -->
                </td>
                <td class="text-center vertical-align-middle">
                    <!-- ko if: analisis_apu -->
                        <a data-bind="click: $root.downloadFile.bind($data, analisis_apu, 'oferente', OferenteId)"
                           download class="btn btn-xl green" title="Descargar">
                            Descargar <i class="fa fa-download"></i>
                        </a>
                    <!-- /ko -->
                    <!-- ko ifnot: analisis_apu -->
                        <span class="label label-danger">Sin archivo</span>
                    <!-- /ko -->
                </td>
            <!-- /ko -->

            <!-- ko if: isRechazado -->
                <td class="text-center vertical-align-middle" data-bind="text: $data.razonSocial"></td>
                <td colspan="3" class="text-center vertical-align-middle" style="color: crimson;">
                    El proveedor declinó su participación
                </td>
            <!-- /ko -->
        </tr>
        <!-- /ko -->
    </tbody>
</table>


<!-- ko if: !$root.IsGo() -->
<div class="table-scrollable">
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> Moneda </th>
                <th colspan="2" class="text-center" data-bind="text: $root.Moneda()"></th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <th colspan="3" class="text-center"
                            data-bind="style: { background: '#' + (Math.floor(Math.random() * 2**24)).toString(16).padStart(6, '0')}">
                        </th>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr>
                <th class="text-center" colspan="3" style="background: #ccc;"> Proveedor </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <th class="text-center" data-bind="text: $data.razonSocial" style="background: #fff;"></th>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Costo Total </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td data-bind="
                            number: $data.total, precision: 0,
                            style: { background: $data.mejorOfertaIntegral ? '#c6e0b4' : '#ffffff' }"
                            class="text-center vertical-align-middle">
                        </td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Ahorro % </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td data-bind="number: $data.ahorro_porc, precision: 0"
                            class="text-center vertical-align-middle"></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Dif % vs Mejor Ofert </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td data-bind="number: $data.difvsmejorofert, precision: 0"
                            class="text-center vertical-align-middle"></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Eval Técnica </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td data-bind="number: $data.evaluationalcanzada, precision: 0"
                            class="text-center vertical-align-middle"></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #fff;">
                <th class="text-center" colspan="3" style="background: #ccc;"> Plazo de Pago </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td data-bind="text: $data.plazoPago"
                            class="text-center vertical-align-middle"></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <td></td>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <thead class="text-center">
            <tr style="background: #ccc;">
                <th class="text-center"> ITEMS </th>
                <th class="text-center"> Cantidad<br>Solicitada </th>
                <th class="text-center"> Costo<br>Objetivo </th>

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <th class="text-center"> Precio<br>Unitario </th>
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <th class="text-center"> Cantidad<br>Cotizada </th>
                    <!-- ko ifnot: isRechazado -->
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <th class="text-center"> Plazo de<br>entrega </th>
                    <!-- /ko -->
                <!-- /ko -->
            </tr>
        </thead>

        <!-- ko foreach: ConcursoEconomicas.cantidadesSolicitadas -->
        <tbody>
            <tr style="background: #fff;">
                <th data-bind="text: $parent.ConcursoEconomicas.productos[$index()].nombre"
                    class="text-left" style="background: #ccc;"></th>
                <td data-bind="text: $data + ' - ' + $parent.ConcursoEconomicas.productos[$index()].unidad"
                    class="text-center vertical-align-middle"></td>
                <td data-bind="text: $parent.ConcursoEconomicas.productos[$index()].targetcost"
                    class="text-center vertical-align-middle"></td>

                <!-- ko foreach: $parent.ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
                        <!-- ko if: $data.items && $data.items.length > $parentContext.$index() -->
                            <td class="text-center vertical-align-middle"
                                data-bind="number: $data.items[$parentContext.$index()].cotizacion, precision: 2,
                                           style: { background: $data.items[$parentContext.$index()].isMejorCotizacion ? '#c6e0b4' : '#ffffff' }">
                            </td>
                        <!-- /ko -->
                        <!-- ko ifnot: $data.items && $data.items.length > $parentContext.$index() -->
                            <td class="text-center vertical-align-middle">—</td>
                        <!-- /ko -->
                    <!-- /ko -->
                <!-- /ko -->

                <!-- ko foreach: $parent.ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
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
                <!-- /ko -->

                <!-- ko foreach: $parent.ConcursoEconomicas.proveedores -->
                    <!-- ko ifnot: isRechazado -->
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
                <!-- /ko -->
            </tr>
        </tbody>
        <!-- /ko -->
    </table>
</div>
<!-- /ko -->
