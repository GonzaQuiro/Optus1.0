<!-- ko if: UserType() != 'customer-approve' -->
<!-- ko if: 
    (IsOnline() && !Countdown() && !Timeleft()) || 
    (IsSobrecerrado() || IsGo())
-->

<!-- ko if: 
    (
        IsOnline() && ExistenOfertas()
    ) || 
    (
        (
            IsSobrecerrado() || IsGo()
        ) && 
        (
            AdjudicacionAnticipada() && ExistenOfertas() && IsRevisado()
        ) || 
        (
            !AdjudicacionAnticipada() && (
                TodosPresentaronEconomica() || 
                PlazoVencidoEconomica()
            ) && IsRevisado()
        )
    )
-->
{include file='concurso/detail/customer/partials/economica/adjudication.tpl'}
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
<!-- ko if: Proveedores().length > 0 -->
<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left"
            style="display: flex; justify-content: space-between; flex-direction: column;">
            <!-- Encabezado h4 a la izquierda -->
            <h4 class="bold" style="margin-top: 0; padding-top: 0; flex-direction:row">Oferta de proveedores
                <span style="text-aling: right; float: right;" data-bind="text:Ronda()"></span>
            </h4>

            
            <!-- Botones alineados horizontalmente y a la derecha -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <!-- Botón "Modificar fechas" -->
                <button class="btn btn-xl green"
                    data-bind="click: ModificarFechasSobres, visible: !IsRevisado()">Modificar Fechas</button>

                <!-- Botón "Ver Ofertas" -->
                <button class="btn btn-xl green"
                    data-bind="click: VerOfertas, visible: !IsRevisado(), disable: (!verOfertasEnable())">Ver
                    Ofertas</button>
            </div>


            <!-- Elemento incluido debajo de todo -->
            
            <table class="table table-striped table-bordered" id="ListaConcursosEconomicas" style="width:100%; table-layout: fixed;">
                <!-- Colgroup para definir anchos de columna -->
                <colgroup>
                    <col style="width: 70%;">  <!-- Proveedor: ocupa la mayor parte -->
                    <col style="width: 10%;">  <!-- Estado: icono pequeño -->
                    <col style="width: 20%;">  <!-- Fecha: se ajusta al 20% -->
             <!-- Fecha presentación: fijo a 120px -->
                </colgroup>
                <thead class="text-center">
                    <tr style="background: #ccc;">
                        <th class="text-center">Proveedor</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center" style="width:120px;">Fecha presentación</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ko foreach: Proveedores() -->
                    <tr style="background: #fff;">
                        <!-- Nombre del proveedor -->
                        <td class="text-center vertical-align-middle"
                            data-bind="text: $data.razonSocial"></td>
                        
                        <!-- Icono de estado -->
                        <td class="text-center vertical-align-middle">
                            <!-- si participa -->
                            <!-- ko if: $data.participa -->
                                <!-- ya presentó económica -->
                                <!-- ko if: $data.presento -->
                                <span>
                                    <i class="fa fa-envelope fa-2x"
                                    aria-hidden="true"
                                    title="El proveedor ya presentó su propuesta económica"
                                    style="color: green"></i>
                                </span>
                                <!-- /ko -->
                                <!-- no ha presentado todavía -->
                                <!-- ko ifnot: $data.presento -->
                                <span>
                                    <i class="fa fa-clock-o fa-2x"
                                    aria-hidden="true"
                                    title="El proveedor aún no envía su propuesta económica"
                                    style="color: rgb(45, 11, 236)"></i>
                                </span>
                                <!-- /ko -->
                            <!-- /ko -->

                            <!-- si no participa -->
                            <!-- ko ifnot: $data.participa -->
                            <span>
                                <i class="fa fa-exclamation fa-2x"
                                aria-hidden="true"
                                title="El proveedor declinó su participación"
                                style="color: red"></i>
                            </span>
                            <!-- /ko -->
                        </td>
                        
                        <!-- Fecha de presentación o mensaje por defecto -->
                        <td class="text-center vertical-align-middle"
                            data-bind="text: $data.presento
                                        ? $data.fechaPresentacion
                                        : 'Aún no presentó propuesta económica'">
                        </td>
                    </tr>
                    <!-- /ko -->
                </tbody>
            </table>

        </div>
    </div>
</div>
<!-- /ko -->
<!-- /ko -->
<!-- /ko -->

<!-- /ko -->

<!-- ko if: IsGo() -->
<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Documentación</h4>

            {include file='concurso/detail/customer/partials/economica/go-documentation.tpl'}
        </div>
    </div>
</div>
<!-- /ko -->

<!-- ko if: IsOnline() -->
<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">
                Propuesta económica subasta
            </h4>

            <!-- Instrucciones y Countdown -->
            {include file='concurso/detail/customer/partials/economica/online-tables-header.tpl'}

            <!-- Carga de datos -->
            <!-- ko if: !IsSubastaciega() -->
            {include file='concurso/detail/customer/partials/economica/online-tables-live.tpl'}
            <!-- /ko -->
        </div>
    </div>
</div>
<!-- /ko -->