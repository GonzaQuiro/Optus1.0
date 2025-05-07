<div class="panel-group accordion" id="documents_accordion" data-bind="foreach: {literal}{ data: Oferentes, as: 'oferente' }{/literal}">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title bold">
                <a 
                    class="accordion-toggle accordion-toggle-styled collapsed" 
                    data-toggle="collapse" 
                    aria-expanded="" 
                    data-bind="text: razon_social, attr: { 'href': '#collapse_' + $index(), 'data-parent': '#documents_accordion' }, css: { 'collapsed': $index() > 0 }">
                </a>
            </h3>
        </div>

        <div 
            class="panel-collapse collapse" 
            aria-expanded="false" 
            data-bind="attr: { 'id': 'collapse_' + $index() }, css: { 'in': $index() == 0 }">

            <!-- DOCUMENTOS GCG -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="block bold vertical-align-middle">Estado de habilitación GCG</th>
                        <th class="block bold vertical-align-middle text-center">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-md-10 vertical-align-middle">
                            <i class="fa fa-exclamation-triangle text-warning" data-bind="visible: !oferente.success"></i>
                            <i class="fa fa-check-circle text-success" data-bind="visible: oferente.success"></i>
                            <span data-bind="text: message"></span>
                        </td>
                        <td class="col-md-2 vertical-align-middle text-center">
                            <button 
                                class="btn green"
                                data-bind="click: $parent.openDocumentsDetail">

                                Ver detalle
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="container-fluid">
                <div class="row no-gutters">
                    <div class="col col-sm-6">
                        <!-- DOCUMENTOS NO-GCG -->
                        <table class="table table-striped table-bordered" data-bind="visible: oferente.documents.go_nogcg_driver_documents.length">
                            <thead>
                                <tr>
                                    <th colspan="4" class="block bold">Documentación a subir en Optus</th>
                                </tr>
                                <tr>
                                    <th class="block bold text-center vertical-align-middle">Tipo</th>
                                    <th class="block bold vertical-align-middle">Nombre</th>
                                    <th class="block bold vertical-align-middle text-center">Archivo</th>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach: {literal}{ data: documents.go_nogcg_driver_documents, as: 'document' }{/literal}">
                                <tr>
                                    <!-- ko if: $index() == 0 -->
                                    <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{ rowspan: oferente.documents.go_nogcg_driver_documents.length }{/literal}">Conductor</td>
                                    <!-- /ko -->
                                    <td class="col-md-6 vertical-align-middle" data-bind="text: name"></td>
                                    <td class="col-md-2 vertical-align-middle text-center">
                                        <!-- ko if: filename -->
                                        <a data-bind="click: $root.downloadFile.bind($data, filename, 'oferente', id)" download class="btn btn-xl green" title="Descargar">
                                            Descargar
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <!-- /ko -->
                                        <!-- ko if: !filename -->
                                        <span class="label label-danger">Pendiente</span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col col-sm-6">
                        <!-- DOCUMENTOS ADICIONALES -->
                        <table class="table table-striped table-bordered" data-bind="oferente.documents.additional_driver_documents.length">
                            <thead>
                                <tr>
                                    <th colspan="4" class="block bold vertical-align-middle">Otros documentos</th>
                                </tr>
                                <tr>
                                    <th class="block bold text-center vertical-align-middle">Tipo</th>
                                    <th class="block bold vertical-align-middle">Nombre</th>
                                    <th class="block bold vertical-align-middle text-center">Archivo</th>
                                </tr>
                            </thead>
                            <tbody data-bind="foreach: {literal}{ data: documents.additional_driver_documents, as: 'document' }{/literal}">
                                <tr>
                                    <!-- ko if: $index() == 0 -->
                                    <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{ rowspan: oferente.documents.additional_driver_documents.length }{/literal}">Conductor</td>
                                    <!-- /ko -->
                                    <td class="col-md-6 vertical-align-middle" data-bind="text: name"></td>
                                    <td class="col-md-2 vertical-align-middle text-center">
                                        <!-- ko if: filename -->
                                        <a data-bind="click: $root.downloadFile.bind($data, filename, 'oferente', id)" download class="btn btn-xl green" title="Descargar">
                                            Descargar
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <!-- /ko -->
                                        <!-- ko if: !filename -->
                                        <span class="label label-danger">Pendiente</span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                            </tbody>
                            <tbody data-bind="foreach: {literal}{ data: documents.additional_vehicle_documents, as: 'document' }{/literal}">
                                <tr>
                                    <!-- ko if: $index() == 0 -->
                                    <td class="text-center vertical-align-middle" data-bind="attr: {literal}{ rowspan: oferente.documents.additional_vehicle_documents.length }{/literal}">Vehículo</td>
                                    <!-- /ko -->
                                    <td class="col-md-4 vertical-align-middle" data-bind="text: name"></td>
                                    <td class="col-md-6 vertical-align-middle text-center">
                                        <!-- ko if: filename -->
                                        <a data-bind="click: $root.downloadFile.bind($data, filename, 'oferente', id)" download class="btn btn-xl green" title="Descargar">
                                            Descargar
                                            <i class="fa fa-download"></i>
                                        </a>
                                        <!-- /ko -->
                                        <!-- ko if: !filename -->
                                        <span class="label label-danger">Pendiente</span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file='concurso/detail/customer/partials/economica/go-documentation-detail-modal.tpl'}