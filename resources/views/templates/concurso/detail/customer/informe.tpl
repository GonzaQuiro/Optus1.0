<!-- ko if: Adjudicado() || Eliminado() -->
<div class="m-heading-1 border-default m-bordered text-left">
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">
        Descargas
    </h4>
    <table class="table table-striped table-bordered">
        <tbody>
            <!--
            <tr>
                <td class="vertical-align-middle">
                    Informes
                </td>
                <td class="text-center">
                    <a 
                        class="btn btn-success" 
                        data-bind="attr: { href: definirRutaDescargaInforme() }" 
                        download >
                        Descargar
                        <i class="fa fa-download"></i>
                    </a>
                </td>
            </tr>
            -->
            <tr>
                <td class="vertical-align-middle">
                    Informes
                </td>
                <td class="text-center">
                    <a 
                        class="btn btn-success" 
                        data-bind="click: downloadReport" 
                        download>
                        Descargar
                        <i class="fa fa-download"></i>
                    </a>
                </td>
            </tr>            
            <tr>
                <td class="vertical-align-middle">
                    Archivos Concurso
                </td>
                <td class="text-center">
                    <a 
                        class="btn btn-success" 
                        data-bind="click: downloadZip" 
                        download>
                        Descargar
                        <i class="fa fa-download"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<!-- /ko -->