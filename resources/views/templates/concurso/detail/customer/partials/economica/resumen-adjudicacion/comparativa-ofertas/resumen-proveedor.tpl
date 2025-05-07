<div class="m-heading-1 border-default m-bordered text-left">
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Resumen por proveedor</h4>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered" id="ResumenProveedores">
            <thead>
                <tr style="background: #ccc;">
                    <th data-bind="text: 'Moneda: ' + $root.Moneda()"></th>
                    <th> Costo Total </th>
                    <th> Ahorro % </th>
                    <th> Dif % vs Mejor Ofert </th>
                    <th> Eval Tecnica </th>
                    <th> Plazo de Pago </th>
                    <th>Anticipo </th>
                </tr>
            </thead>
            <tbody>
                <!-- ko foreach: ConcursoEconomicas.proveedores -->
                <!-- ko ifnot: isRechazado || isVencido -->
                <tr>
                    <td class="text-center" data-bind="text: $data.razonSocial">
                    </td>
                    <td data-bind="number: $data.total, precision: 2, style: { background: $data.mejorOfertaIntegral ?  '#c6e0b4' : '#ffffff' }"
                        class="text-center vertical-align-middle"></td>
                    <td data-bind="number: $data.ahorro_porc, precision: 2, style: { background: '#ffffff' }"
                        class="text-center vertical-align-middle"></td>
                    <td data-bind="number: $data.difvsmejorofert, precision: 2, style: { background: '#ffffff' }"
                        class="text-center vertical-align-middle"></td>
                    <td data-bind="number: $data.evaluationalcanzada, , precision: 2, style: { background: '#ffffff' }"
                        class="text-center vertical-align-middle"></td>
                    <td data-bind="text: $data.plazoPago, style: { background: '#ffffff' }"
                        class="text-center vertical-align-middle"></td>
                    <td data-bind="text: $data.condicionPago, style: { background: '#ffffff' }"
                        class="text-center vertical-align-middle"></td>
                </tr>
                <!-- /ko -->
                <!-- /ko -->
            </tbody>
        </table>
    </div>
</div>