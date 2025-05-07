<div class="m-heading-1 border-default m-bordered text-left">
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Etapa de comparativa de ofertas</h4>

    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <tbody data-bind="">
            <tr>
                <!-- ko if: !Adjudicado() -->
                <td class="text-justify">
                    <h4>ANALISIS DE OFERTAS PENDIENTE DE INICIO</h4>
                    <p>El concurso aun se encuentra en etapa de presentación de ofertas técnicas y económicas. Un vez presentadas todas las propuestas o cumplida la fecha límite se iniciará el proceso de análisis de ofertas.</p>
                </td>
                <!-- /ko -->
                <!-- ko if: Adjudicado() && IsAdjudicacionAceptada() -->
                <td class="text-justify">
                    <h4>CONCURSO ADJUDICADO</h4>
                    <p>Le informamos que el concurso ya ha sido adjudicado, lo invitamos a consultar la siguiente etapa para conocer el resultado.</p>
                </td>
                <!-- /ko -->
                <!-- ko if: Adjudicado() && (Rechazado() || IsAdjudicacionRechazada()) -->
                <td class="text-justify">
                    <h4>CONCURSO FINALIZADO</h4>
                    <p>Le informamos que concurso ha llegado a su etapa de finalización. En esta oportunidad sus propuestas técnicas y/o económicas no han resultado seleccionadas.<br><br>Le agradecemos su interés y esfuerzo en la participación del proceso.</p>
                </td>
                <!-- /ko -->
                <!-- ko if: Eliminado() -->
                <td class="text-justify">
                    <h4>CONCURSO CANCELADO</h4>
                    <p>Le informamos que el concurso ya cancelado, le agradecemos su participacion.</p>
                </td>
                <!-- /ko -->
            </tr>
        </tbody>
    </table>

    <!-- ko if: Adjudicado() || IsAdjudicacionPendiente() || IsAdjudicacionRechazada() -->
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Resultado final del concurso</h4>
    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
        <thead>
            <tr>
                <td colspan="2" class="text-center">Resultado</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center bold" style="vertical-align: middle;">
                    <!-- ko if: IsAdjudicacionPendiente() -->
                    <span style="color: #0000FF">Pendiente de adjudicación</span>
                    <!-- /ko -->
                    <!-- ko if: Adjudicado() && IsAdjudicacionAceptada() -->
                    <span style="color: #008000">Adjudicado</span>
                    <!-- /ko -->
                    <!-- ko if: Adjudicado() && (Rechazado() || IsAdjudicacionRechazada()) -->
                    <span style="color: #F00000">No ha resultado adjudicatario</span>
                    <!-- /ko -->
                </td>
            </tr>
        </tbody>
    </table>
    <!-- /ko -->
</div>