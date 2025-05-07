 {if $tipo neq 'chat-muro-consultas'}
<div class="m-heading-1 border-default m-bordered text-left">
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Etapas / Pasos</h4>
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead>
            <tr>
                <th> Fechas límites </th>
                <th> Fecha </th>
                <th> Hora </th>
                <th> Zona Horaria </th>
                <th style="text-align: center;"> Acción / Estados </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-bind="text: 'Invitación'" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: AceptacionInvitacion" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="text: AceptacionInvitacionHora" class="col-md-1 vertical-align-middle"></td>
                <td data-bind="text: ZonaHoraria" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: AceptoInvitacion" class="col-md-4 vertical-align-middle text-center"></td>
            </tr>
            <tr>
                <td data-bind="text: 'Cierre muro de consultas'" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: CierreMuroConsultas" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="text: CierreMuroConsultasHora" class="col-md-1 vertical-align-middle"></td>
                <td data-bind="text: ZonaHoraria" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: EstadoChat" class="col-md-4 vertical-align-middle text-center"></td>
            </tr>
            <!-- ko if: IncluyeTecnica() -->
            <tr>
                <td data-bind="text: IsGo() ? 'Presentación de Documentación' : 'Presentación oferta técnica'"
                    class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: PresentacionTecnicas" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="text: PresentacionTecnicasHora" class="col-md-1 vertical-align-middle"></td>
                <td data-bind="text: ZonaHoraria" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: EstadoTecnica" class="col-md-4 vertical-align-middle text-center"></td>
            </tr>
            <!-- /ko -->

            <!-- ko if: IsSobrecerrado() || IsGo() -->
            <tr>
                <td data-bind="text: IsGo() ? 'Presentación de Cotización' : 'Presentación oferta económica'"
                    class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: PresentacionEconomicas" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="text: PresentacionEconomicasHora" class="col-md-1 vertical-align-middle"></td>
                <td data-bind="text: ZonaHoraria" class="col-md-3 vertical-align-middle"></td>
                <td></td>
            </tr>

            <!-- ko if: IncluyeEconomicaSegundaRonda() -->
            <tr>
                <td data-bind="text: 'Presentación nueva oferta económica'" class="vertical-align-middle col-md-3">
                </td>
                <td data-bind="text: PresentacionEconomicasSegundaRonda"
                    class="vertical-align-middle col-md-2"></td>
                <td data-bind="text: PresentacionEconomicasSegundaRondaHora"
                    class="vertical-align-middle col-md-1"></td>
                <td data-bind="text: ZonaHoraria" class="vertical-align-middle col-md-3"></td>
            </tr>
            <!-- /ko -->
            <!-- /ko -->
            <!-- ko if: IsOnline() -->
            <tr>
                <td data-bind="text: 'Inicio Subasta'" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: InicioSubasta" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="text: InicioSubastaHora" class="col-md-1 vertical-align-middle"></td>
                <td data-bind="text: ZonaHoraria" class="col-md-3 vertical-align-middle"></td>
                <td data-bind="text: EstadoSubasta" class="col-md-4 vertical-align-middle text-center"></td>
            </tr>
            <!-- /ko -->
        </tbody>
    </table>
    <!-- ko if: AdjudicacionAnticipada() && IsSobrecerrado() -->
    <div class="span12 text-center">
        <span style="color: red;" class="block bold text-center">Esta licitación puede finalizar antes del plazo
            estipulado. ¡Recomendamos cotizar a la brevedad!</span>
    </div>
    <!-- /ko -->

</div>

<div class="m-heading-1 border-default m-bordered text-left">
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Información del concurso</h4>
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <tbody data-bind="">
            <!-- ko if: !IsGo() -->
            <tr>
                <td data-bind="text: 'Nombre'" class="col-md-4 vertical-align-middle"></td>
                <td data-bind="text: Nombre" class="col-md-4 vertical-align-middle"></td>
                <td rowspan="5" class="col-md-4 text-center"
                    data-bind="style: {literal}{backgroundImage: 'url(' + ImagePath() + Portrait() + ')'}{/literal}"
                    style="vertical-align: middle;width: auto;height: 300px;background-repeat: no-repeat;background-position: center center;background-size:cover;border: 1px solid #ddd;">
                </td>
            </tr>
            <tr>
                <td data-bind="text: 'Solicitante'" class="col-md-4 vertical-align-middle"></td>
                <td data-bind="text: Solicitante" class="col-md-4 vertical-align-middle"></td>
            </tr>
            <tr>
                <td data-bind="text: 'Administrador'" class="col-md-4 vertical-align-middle"></td>
                <td data-bind="text: Administrador" class="col-md-4 vertical-align-middle"></td>
            </tr>
            <tr>
                <td data-bind="text: 'Tipología'" class="col-md-4 vertical-align-middle"></td>
                <td data-bind="text: TipoConcurso" class="col-md-4 vertical-align-middle"></td>
            </tr>
            <tr>
                <td data-bind="text: 'Tipo de operación'" class="col-md-4 vertical-align-middle"></td>
                <td data-bind="text: TipoOperacion" class="col-md-4 vertical-align-middle"></td>
            </tr>
            <!-- /ko -->
            <!-- ko if: IsGo() -->
            <tr>
                <td colspan="2" data-bind="text: 'Nombre'" class="col-md-4 vertical-align-middle"></td>
                <td colspan="2" data-bind="text: Nombre" class="col-md-4 vertical-align-middle"></td>
            </tr>
            <tr>
                <td colspan="1" data-bind="text: 'Fecha Desde'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: FechaDesde" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: 'Fecha Hasta'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: FechaHasta" class="col-md-2 vertical-align-middle"></td>
            </tr>
            <tr>
                <td colspan="1" data-bind="text: 'Horario Desde'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: HoraDesde" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: 'Horario Hasta'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: HoraHasta" class="col-md-2 vertical-align-middle"></td>
            </tr>
            <tr>
                <td colspan="1" data-bind="text: 'Provincia Desde'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: ProvinciaDesdeNombre" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: 'Provincia Hasta'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: ProvinciaHastaNombre" class="col-md-2 vertical-align-middle"></td>
            </tr>
            <tr>
                <td colspan="1" data-bind="text: 'Ciudad Desde'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: CiudadDesdeNombre" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: 'Ciudad Hasta'" class="col-md-2 vertical-align-middle"></td>
                <td colspan="1" data-bind="text: CiudadHastaNombre" class="col-md-2 vertical-align-middle"></td>
            </tr>
            <!-- /ko -->
        </tbody>
    </table>

    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Descripción del concurso</h4>
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <tbody data-bind="">
            <tr>
                <td data-bind="text: 'Reseña'" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="html: Resena" class="col-md-10 vertical-align-middle"></td>
            </tr>
            <!-- ko if: !IsGo() -->
            <tr>
                <td data-bind="text: 'Descripción'" class="col-md-2 vertical-align-middle"></td>
                <td data-bind="html: Descripcion" class="col-md-10 vertical-align-middle"></td>
            </tr>
            <!-- /ko -->
        </tbody>
    </table>
</div>

{/if}