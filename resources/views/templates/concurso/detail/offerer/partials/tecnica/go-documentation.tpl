<div class="row">
    <!-- CARGA DE CONDUCTORES Y VEHÍCULOS -->
    <div class="col-sm-5">
        <table class="table table-striped table-bordered" id="ListaConcursosGo">
            <thead>
                <tr>
                    <th colspan="2" class="block bold">Datos Conductor y Vehículos</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td data-bind="text: 'Nombre y Apellido del Conductor'" class="col-md-2 vertical-align-middle"></td>
                    <td>
                        <input type="text" class="form-control" id="DriverSelected" name="DriverSelected" data-bind="value: DriverSelected, disable: !EnableTechnical() || HasTecnicaPresentada()">
                    </td>
                </tr>
                <tr>
                    <td data-bind="text: 'Vehículo'" class="col-md-4 vertical-align-middle"></td>
                    <td>
                        <input type="text" class="form-control" id="VehicleSelected" name="VehicleSelected" data-bind="value: VehicleSelected, disable: !EnableTechnical() || HasTecnicaPresentada()">
                    </td>
                </tr>
                <tr>
                    <td data-bind="text: 'Patente Tractor'" class="col-md-4 vertical-align-middle"></td>
                    <td>
                        <input type="text" class="form-control" id="TrailerSelected" name="TrailerSelected" data-bind="value: TrailerSelected, disable: !EnableTechnical() || HasTecnicaPresentada()">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="col-sm-7">
        <!-- DOCUMENTOS GCG -->
        <table class="table table-striped table-bordered" id="ListaConcursosGo">
            <thead>
                <tr>
                    <th colspan="3" class="block bold">Documentación GCG requerida</th>
                </tr>
                <tr>
                    <th class="block bold text-center vertical-align-middle">Tipo</th>
                    <th class="block bold">Nombre</th>
                    <th class="block bold text-center">Estado</th>
                </tr>
            </thead>
            <tbody data-bind="foreach: DriverDocuments">
                <tr>
                    <!-- ko if: $index() == 0 -->
                    <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{rowspan: $parent.DriverDocuments().length}{/literal}">Conductor</td>
                    <!-- /ko -->
                    <td class="col-md-6" data-bind="text: name"></td>
                    <td class="col-md-4 text-center">
                        <!-- ko if: !$root.Adjudicado() && !$root.Rechazado() -->
                        <input id="input-700" data-bind="fileinput: $data, fileinputOptions: {
                            uploadUrl: '/media/file/upload',
                            initialCaption: filename() ? filename() : [],
                            uploadExtraData: {
                                UserToken: User.Token,
                                path: $parent.FilePathOferente(),
                            },
                            initialPreview: filename() ? [$parent.FilePathOferente() + filename()] : [],
                        }"
                        name="file[]" type="file">
                        <!-- /ko -->
                    </td>
                </tr>
            </tbody>
            <tbody data-bind="foreach: VehicleDocuments">
                <tr>
                    <!-- ko if: $index() == 0 -->
                    <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{rowspan: $parent.VehicleDocuments().length}{/literal}">Vehículo</td>
                    <!-- /ko -->
                    <td class="col-md-6" data-bind="text: name"></td>
                    <td class="col-md-4 text-center">
                        <!-- ko if: !$root.Adjudicado() && !$root.Rechazado() -->
                        <input id="input-700" data-bind="fileinput: $data, fileinputOptions: {
                            uploadUrl: '/media/file/upload',
                            initialCaption: filename() ? filename() : [],
                            uploadExtraData: {
                                UserToken: User.Token,
                                path: $parent.FilePathOferente(),
                            },
                            initialPreview: filename() ? [$parent.FilePathOferente() + filename()] : [],
                        }"
                        name="file[]" type="file">
                        <!-- /ko -->
                    </td>
                </tr>
            </tbody>
            <tbody data-bind="foreach: TrailerDocuments">
                <tr>
                    <!-- ko if: $index() == 0 -->
                    <td class="col-md-2 text-center vertical-align-middle" data-bind="attr: {literal}{rowspan: $parent.TrailerDocuments().length}{/literal}">Patente Tractor</td>
                    <!-- /ko -->
                    <td class="col-md-6" data-bind="text: name"></td>
                    <td class="col-md-4 text-center">
                        <!-- ko if: !$root.Adjudicado() && !$root.Rechazado() -->
                        <input id="input-700" data-bind="fileinput: $data, fileinputOptions: {
                            uploadUrl: '/media/file/upload',
                            initialCaption: filename() ? filename() : [],
                            uploadExtraData: {
                                UserToken: User.Token,
                                path: $parent.FilePathOferente(),
                            },
                            initialPreview: filename() ? [$parent.FilePathOferente() + filename()] : [],
                        }"
                        name="file[]" type="file">
                        <!-- /ko -->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- DOCUMENTOS NO-GCG -->
<table class="table table-striped table-bordered" id="ListaConcursosGo" data-bind="visible: DriverNoGcgDocuments().length">
    <thead>
        <tr>
            <th colspan="6" class="block bold">Documentación requerida a subir en Optus</th>
        </tr>
        <tr>
            <th class="block bold text-center vertical-align-middle">Tipo</th>
            <th class="block bold vertical-align-middle">Nombre</th>
            <th class="block bold vertical-align-middle">CUIT</th>
            <th class="block bold vertical-align-middle">Razón Social</th>            
            <th colspan="2" class="block bold vertical-align-middle">Archivo</th>
        </tr>
    </thead>
    <tbody data-bind="foreach: DriverNoGcgDocuments">
        <tr>
            <!-- ko if: $index() == 0 -->
            <td class="text-center vertical-align-middle" data-bind="attr: {literal}{rowspan: $parent.DriverNoGcgDocuments().length}{/literal}">Conductor</td>
            <!-- /ko -->
            <td class="col-md-3 vertical-align-middle" data-bind="text: name"></td>
            <td class="col-md-1 vertical-align-middle" data-bind="text: cuit"></td>
            <td class="col-md-1 vertical-align-middle" data-bind="text: razon_social"></td>
            <td class="col-md-6 vertical-align-middle">
                <!-- ko if: !$root.Adjudicado() && !$root.Rechazado() -->
                <input id="input-700" data-bind="fileinput: $data, fileinputOptions: {
                    uploadUrl: '/media/file/upload',
                    initialCaption: filename() ? filename() : [],
                    uploadExtraData: {
                        UserToken: User.Token,
                        path: $parent.FilePathOferente(),
                    },
                    initialPreview: filename() ? [$parent.FilePathOferente() + filename()] : [],
                }"
                name="file[]" type="file">
                <!-- /ko -->
            </td>
            <td class="col-md-6 text-center vertical-align-middle">
                <!-- ko if: filename() -->
                <a data-bind="click: $root.downloadFile.bind($data, filename(), 'oferente', $root.OferenteId())" download class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !filename() -->
                <span class="label label-danger">Sin archivo</span>
                <!-- /ko -->
            </td>
        </tr>
    </tbody>
</table>
<!-- DOCUMENTOS ADICIONALES -->
<table class="table table-striped table-bordered" id="ListaConcursosGo" data-bind="visible: AdditionalDriverDocuments().length || AdditionalVehicleDocuments().length">
    <thead>
        <tr>
            <th colspan="4" class="block bold vertical-align-middle">Otros documentos requeridos</th>
        </tr>
        <tr>
            <th class="block bold text-center vertical-align-middle">Tipo</th>
            <th class="block bold vertical-align-middle">Nombre</th>
            <th colspan="2" class="block bold vertical-align-middle">Archivo</th>
        </tr>
    </thead>
    <tbody data-bind="foreach: AdditionalDriverDocuments">
        <tr>
            <!-- ko if: $index() == 0 -->
            <td class="text-center vertical-align-middle" data-bind="attr: {literal}{rowspan: $parent.AdditionalDriverDocuments().length}{/literal}">Conductor</td>
            <!-- /ko -->
            <td class="col-md-4 vertical-align-middle" data-bind="text: name"></td>
            <td class="col-md-6 vertical-align-middle">
                <!-- ko if: !$root.Adjudicado() && !$root.Rechazado() -->
                <input id="input-700" data-bind="fileinput: $data, fileinputOptions: {
                    uploadUrl: '/media/file/upload',
                    initialCaption: filename() ? filename() : [],
                    uploadExtraData: {
                        UserToken: User.Token,
                        path: $parent.FilePathOferente(),
                    },
                    initialPreview: filename() ? [$parent.FilePathOferente() + filename()] : [],
                }"
                name="file[]" type="file">
                <!-- /ko -->
            </td>
            <td class="col-md-6 text-center vertical-align-middle">
                <!-- ko if: filename() -->
                <a data-bind="click: $root.downloadFile.bind($data, filename(), 'oferente', $root.OferenteId())" download class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !filename() -->
                <span class="label label-danger">Sin archivo</span>
                <!-- /ko -->
            </td>
        </tr>
    </tbody>
    <tbody data-bind="foreach: AdditionalVehicleDocuments">
        <tr>
            <!-- ko if: $index() == 0 -->
            <td class="text-center vertical-align-middle" data-bind="attr: {literal}{rowspan: $parent.AdditionalVehicleDocuments().length}{/literal}">Vehículo</td>
            <!-- /ko -->
            <td class="col-md-4 vertical-align-middle" data-bind="text: name"></td>
            <td class="col-md-6 vertical-align-middle">
                <!-- ko if: !$root.Adjudicado() && !$root.Rechazado() -->
                <input id="input-700" data-bind="fileinput: $data, fileinputOptions: {
                    uploadUrl: '/media/file/upload',
                    initialCaption: filename() ? filename() : [],
                    uploadExtraData: {
                        UserToken: User.Token,
                        path: $parent.FilePathOferente(),
                    },
                    initialPreview: filename() ? [$parent.FilePathOferente() + filename()] : [],
                }"
                name="file[]" type="file">
                <!-- /ko -->
            </td>
            <td class="col-md-6 text-center vertical-align-middle">
                <!-- ko if: filename() -->
                <a data-bind="click: $root.downloadFile.bind($data, filename(), 'oferente', $root.OferenteId())" download class="btn btn-xl green" title="Descargar">
                    Descargar
                    <i class="fa fa-download"></i>
                </a>
                <!-- /ko -->
                <!-- ko if: !filename() -->
                <span class="label label-danger">Sin archivo</span>
                <!-- /ko -->
            </td>
        </tr>
    </tbody>
</table>