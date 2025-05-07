{extends 'empresas/materiales/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/datatables/datatables.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}" rel="stylesheet"
        type="text/css" />
    <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-fileinput-kartik/css/fileinput.min.css')}" rel="stylesheet"
        type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript">
    </script>

    <script src="{asset('/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput-kartik/js/fileinput.min.js')}"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput-kartik/js/locales/es.js')}"></script>
    <script src="{asset('/global/scripts/xlsx.full.min.js')}"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'material-list'}

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="btn-group">
                        <a href="/materiales/nuevo" class="btn sbold green">
                            Agregar Nuevo Material
                            <i class="fa fa-plus"></i>
                        </a>
                        <a data-bind="click: downloadPlantillaExcel" download class="btn sbold blue"
                            title="Exportar Plantilla">
                            Exportar la plantilla para la importación
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="portlet-title">
                    <table class="table table-striped table-bordered" id="ListaConcursosEconomicas">
                        <tbody>
                            <tr>
                                <td colspan="3">
                                    <h4>Instrucciones para importar materiales desde excel</h4>
                                    <ul class="list-unstyled">
                                        <li>1. Adjunte archivo con sus materiales en formato XLS, XLSX.</li>
                                        <li>2. Procese el archivo.</li>
                                        <li>4. En caso de existir inconsistencias en la estructura del archivo se indicará
                                            error y todos los registros serán rechazados.</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td class="col-md-5 vertical-align-middle">
                                    <input type="file" data-bind="fileUploadExcel: uploadFile">
                                </td>
                                <td class="col-md-3 text-center vertical-align-middle">
                                    <!-- ko if: uploadFile -->
                                    <a data-bind="click: uploadFileProcesar" download class="btn btn-xl green"
                                        title="Importar">
                                        Importar
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <a data-bind="click: uploadFileclear" download class="btn btn-xl red" title="Limpiar">
                                        Limpiar
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <!-- /ko -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered" id="listaCatalogos">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Nombre</th>
                                <th>Proveedor</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody data-bind="dataTablesForEach : { data: listaCatalogos, options: { paging: true }}">
                            <tr>
                                <td class="col-md-2" data-bind="text: NombreCategoria"></td>
                                <td class="col-md-4" data-bind="text: CodigoERP() + ' - ' + Descripcion()"></td>
                                <td class="col-md-4" data-bind="text: CodigoProveedor() + ' - ' + Proveedor()"></td>
                                <td class="col-md-4 text-center">
                                    <a data-bind="attr: {literal}{href: '/materiales/edicion/' + Id()}{/literal}"
                                        class="btn btn-xs green" title="Editar">
                                        Editar
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <!-- ko if: !Eliminado() -->
                                    <a data-bind="click: $root.Eliminar.bind($data, Id())" class="btn btn-xs btn-danger"
                                        title="Eliminar">
                                        Eliminar
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                    <!-- /ko -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
    <script type="text/javascript">
        var Material = function(data) {
            var self = this;
            this.Id = ko.observable(data.Id);
            this.NombreCategoria = ko.observable(data.NombreCategoria);
            this.CodigoERP = ko.observable(data.CodigoERP);
            this.Descripcion = ko.observable(data.Descripcion);
            this.CodigoProveedor = ko.observable(data.CodigoProveedor);
            this.Proveedor = ko.observable(data.Proveedor);
            this.Eliminado = ko.observable(data.Eliminado);
        }

        var MaterialesListado = function(data) {
            var self = this;

            this.Breadcrumbs = ko.observableArray(data.breadcrumbs);
            this.listaCatalogos = ko.observableArray();

            self.uploadFile = ko.observable(null);
            self.uploadName = ko.computed(function() {
                return !!self.uploadFile() ? self.uploadFile().name : '-';
            });

            self.uploadFileclear = function() {
                self.uploadFile(null);
            };

            self.uploadFileProcesar = function() {
                var file = self.uploadFile();
                var formData = new FormData();
                formData.append('file', file);

                //alert(self.uploadFile().name);
                //alert(window.location.pathname);

                var selectedFile;
                selectedFile = self.uploadFile();

                var data = [{
                    "name": "jayanth",
                    "data": "scd",
                    "abc": "sdef"
                }];
                var datatype = {
                    "type": "binary"
                };

                var rowObject = null;
                var loads = [];

                XLSX.utils.json_to_sheet(data, 'out.xlsx');
                if (selectedFile) {
                    var fileReader = new FileReader();
                    fileReader.readAsBinaryString(selectedFile);

                    rowObject = fileReader.onload = (event) => {
                        var data = event.target.result;
                        var workbook = XLSX.read(data, datatype);
                        var resultRowObject = null;

                        workbook.SheetNames.forEach(sheet => {
                            if (sheet === 'Materiales')
                                resultRowObject = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[
                                    sheet]);
                        });

                        if (resultRowObject === null) {
                            swal('Error', 'No se encontro la hoja "Materiales"', 'error');
                        } else {
                            enviarDatos(resultRowObject);
                        }
                        self.uploadFile(null);
                    };
                }
            };

            self.downloadPlantillaExcel = function() {
                var wb = XLSX.utils.book_new();
                wb.Props = {
                    Title: "Plantilla para importación de Materiales",
                    Subject: "Plantilla importación Materiales",
                    Author: "Optus",
                    CreatedDate: new Date()
                };
                wb.SheetNames.push("Materiales", "Lista - Categorias", "Lista - Unidades");
                var ws_data = [
                    ['Categoria', 'CodigoERP', 'Unidad', 'CostoObjetivo', 'Descripcion', 'DescripcionLarga',
                        'CodigoProveedor', 'Proveedor'
                    ]
                ];

                var ws_dataLC = [];
                data.categorias.forEach(item => {
                    var vItem = [];
                    vItem.push(item['nombre']);
                    ws_dataLC.push(vItem);
                });

                var ws_dataLU = [];
                data.unidades.forEach(item => {
                    var vItem = [];
                    vItem.push(item['text']);
                    ws_dataLU.push(vItem);
                });

                var ws = XLSX.utils.aoa_to_sheet(ws_data);
                var wsLC = XLSX.utils.aoa_to_sheet(ws_dataLC);
                var wsLU = XLSX.utils.aoa_to_sheet(ws_dataLU);

                if (!ws.A1.c) ws.A1.c = [];
                ws.A1.c.hidden = true;
                ws.A1.c.push({
                    a: "SheetJS",
                    t: "Ingrese el nombre de la categoría"
                });

                if (!ws.B1.c) ws.B1.c = [];
                ws.B1.c.hidden = true;
                ws.B1.c.push({
                    a: "SheetJS",
                    t: "Ingrese el código ERP del Material"
                });

                if (!ws.C1.c) ws.C1.c = [];
                ws.C1.c.hidden = true;
                ws.C1.c.push({
                    a: "SheetJS",
                    t: "Ingrese la unida de medida del Material"
                });

                if (!ws.D1.c) ws.D1.c = [];
                ws.D1.c.hidden = true;
                ws.D1.c.push({
                    a: "SheetJS",
                    t: "Ingrese el costo objetivo del Material"
                });

                if (!ws.G1.c) ws.G1.c = [];
                ws.G1.c.hidden = true;
                ws.G1.c.push({
                    a: "SheetJS",
                    t: "Ingrese el código ERP del Proveedor"
                });

                wb.Sheets["Materiales"] = ws;
                wb.Sheets["Lista - Categorias"] = wsLC;
                wb.Sheets["Lista - Unidades"] = wsLU;

                XLSX.writeFile(wb, 'Materiales_Importacion.xlsx');

            };

            function s2ab(s) {
                var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
                var view = new Uint8Array(buf); //create uint8array as viewer
                for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
                return buf;
            }

            if (data.list.length > 0) {
                data.list.forEach(item => {
                    self.listaCatalogos.push(new Material(item));
                });
            }

            function enviarDatos(rowObject) {
                var url = '/materiales/file/import';
                Services.Post(
                    url, {
                        UserToken: User.Token,
                        Data: JSON.stringify(rowObject, undefined, 4)
                    },
                    async (response) => {
                            $.unblockUI();
                            if (response.success) {
                                swal('Hecho', response.message, 'success');
                                await sleep(2000);
                                window.location.reload();
                            } else {
                                swal('Error', response.message, 'error');
                            }
                        },
                        async (error) => {
                                $.unblockUI();
                                swal('Error', error.message, 'error');
                            },
                            null,
                            null
                );
            }

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            this.Eliminar = function(id) {
                swal({
                    title: '¿Está seguro de que desea eliminar la unidad de medida?',
                    type: 'info',
                    closeOnClickOutside: false,
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonText: 'Aceptar',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonText: 'Cancelar',
                    cancelButtonClass: 'btn btn-default'
                }, function(result) {
                    swal.close();
                    if (result) {
                        $.blockUI();
                        var data = {
                            UserToken: User.Token
                        };
                        var url = '/materiales/toggle/' + id;
                        Services.Post(url, data,
                            (response) => {
                                swal.close();
                                $.unblockUI();
                                if (response.success) {
                                    setTimeout(function() {
                                        swal({
                                            title: 'Hecho',
                                            text: response.message,
                                            type: 'success',
                                            closeOnClickOutside: false,
                                            closeOnConfirm: true,
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonClass: 'btn btn-success'
                                        }, function(result) {
                                            if (response.data.redirect) {
                                                window.location.href = response.data
                                                    .redirect;
                                            } else {
                                                location.reload();
                                            }
                                        });
                                    }, 500);
                                } else {
                                    setTimeout(function() {
                                        swal('Error', response.message, 'error');
                                    }, 500);
                                }
                            },
                            (error) => {
                                swal.close();
                                $.unblockUI();
                                setTimeout(function() {
                                    swal('Error', error.message, 'error');
                                }, 500);
                            }
                        );
                    }
                });
            };
        };

        jQuery(document).ready(function() {
            $.blockUI();
            var data = {
                UserToken: User.Token
            };
            var url = '/materiales/list';
            Services.Get(url, data,
                (response) => {
                    if (response.success) {
                        window.E = new MaterialesListado(response.data);
                        AppOptus.Bind(E);
                    }
                    $.unblockUI();
                },
                (error) => {
                    $.unblockUI();
                    swal('Error', error.message, 'error');
                }
            );
        });

        // Chrome allows you to debug it thanks to this
        {chromeDebugString('dynamicScript')}
    </script>
{/block}