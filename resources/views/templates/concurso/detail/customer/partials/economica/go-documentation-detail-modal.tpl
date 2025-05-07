<!-- Oferente Documents Detail Modal -->
<div 
    id="documentsDetailModal" 
    class="modal fade" 
    tabindex="-1"
    data-backdrop="static"
    data-keyboard="false"
    data-bind="with: OferenteModalDetail">
    
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button 
                    type="button" 
                    class="close" 
                    aria-hidden="true"
                    data-bind="click: $parent.closeDocumentsDetail">
                </button>
                
                <h4 class="modal-title" data-bind="text: 'Documentación GCG - ' + razon_social"></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" data-bind="visible: !success">
                    <p data-bind="text: message"></p>
                </div>

                <div class="alert alert-success" data-bind="visible: success">
                    <p>Este Oferente cumple con los requisitos para ser adjudicado.</p>
                </div>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="block bold text-center vertical-align-middle">Tipo</th>
                            <th class="block bold vertical-align-middle">Nombre</th>
                            <th class="block bold vertical-align-middle text-center">Estado</th>
                        </tr>
                    </thead>
                    <!-- DRIVER -->
                    <tbody data-bind="foreach: documents.go_driver_documents">
                        <tr>
                            <!-- ko if: $index() == 0 -->
                            <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{ rowspan: $root.OferenteModalDetail().documents.go_driver_documents.length }{/literal}">Conductor</td>
                            <!-- /ko -->
                            <td class="col-md-6 vertical-align-middle" data-bind="text: name"></td>
                            <td class="col-md-10 vertical-align-middle text-center">
                                <i class="fa fa-exclamation-triangle text-warning" data-bind="visible: !success"></i>
                                <i class="fa fa-check-circle text-success" data-bind="visible: success"></i>
                                <span data-bind="text: message"></span>
                            </td>
                        </tr>
                    </tbody>
                    <!-- VEHICLE -->
                    <tbody data-bind="foreach: documents.go_vehicle_documents">
                        <tr>
                            <!-- ko if: $index() == 0 -->
                            <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{ rowspan: $root.OferenteModalDetail().documents.go_vehicle_documents.length }{/literal}">Vehículo</td>
                            <!-- /ko -->
                            <td class="col-md-6 vertical-align-middle" data-bind="text: name"></td>
                            <td class="col-md-10 vertical-align-middle text-center">
                                <i class="fa fa-exclamation-triangle text-warning" data-bind="visible: !success"></i>
                                <i class="fa fa-check-circle text-success" data-bind="visible: success"></i>
                                <span data-bind="text: message"></span>
                            </td>
                        </tr>
                    </tbody>
                    <!-- TRAILER -->
                    <tbody data-bind="foreach: documents.go_trailer_documents">
                        <tr>
                            <!-- ko if: $index() == 0 -->
                            <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{ rowspan: $root.OferenteModalDetail().documents.go_trailer_documents.length }{/literal}">Patente Tractor</td>
                            <!-- /ko -->
                            <td class="col-md-6 vertical-align-middle" data-bind="text: name"></td>
                            <td class="col-md-10 vertical-align-middle text-center">
                                <i class="fa fa-exclamation-triangle text-warning" data-bind="visible: !success"></i>
                                <i class="fa fa-check-circle text-success" data-bind="visible: success"></i>
                                <span data-bind="text: message"></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <!-- ko if: !success -->
                <button 
                    type="button"
                    class="btn green"
                    data-bind="click: $parent.sendDocumentationReminder.bind($data, $root.OferenteModalDetail())">
                    
                    Enviar aviso
                </button>
                <!-- /ko -->
                <button 
                    type="button"
                    class="btn green btn-outline"
                    data-bind="click: $parent.closeDocumentsDetail">
                    
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>