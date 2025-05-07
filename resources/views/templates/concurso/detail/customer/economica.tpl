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


            <!-- Botón a la derecha -->
            <button class="btn btn-xl green" style="margin-left: auto;"
                data-bind="click: VerOfertas, visible: !IsRevisado(), disable: (!verOfertasEnable())">Ver
                Ofertas</button>

            <!-- Espacio para separar el elemento incluido -->
            <div style="flex: 1;"></div>

            <!-- Elemento incluido debajo de todo -->
            <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
                <thead class="text-center">
                    <tr style="background: #ccc;">
                        <th class="text-center"> Proveedor </th>
                        <th class="text-center"> Estado </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ko foreach: Proveedores() -->
                    <tr style="background: #fff;">
                        <td class="text-center vertical-align-middle" data-bind="text: $data.razonSocial"></td>
                        <td class="text-center vertical-align-middle">
                            <!-- ko if: $data.participa -->
                            <!-- ko if: $data.presento -->
                            <span>
                                <i class="fa fa-envelope fa-2x" aria-hidden="true" title="El proveedor ya presentó su propuesta economica" style="color:green"></i>
                            </span>
                            <!-- /ko -->
                            <!-- ko ifnot: $data.presento -->
                            <span>
                                <i class="fa fa-clock-o fa-2x" aria-hidden="true" title="El proveedor aun no envia su propuesta economica" style="color:rgb(45, 11, 236)"></i>
                            </span>
                            <!-- /ko -->
                            <!-- /ko -->
                            <!-- ko ifnot: $data.participa -->
                            <span>
                                <i class="fa fa-exclamation fa-2x" aria-hidden="true" title="El proveedor declinó su participación" style="color:red"></i>
                            </span>
                            <!-- /ko -->
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