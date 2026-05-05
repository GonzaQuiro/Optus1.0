{extends 'index.tpl'}

{block 'title'}
    {$title}
{/block}

{block 'styles'}
    <link href="{asset('/global/plugins/bootstrap-summernote/summernote.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-fileinput-kartik/css/fileinput.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/datatables/datatables.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
{/block}

{block 'pre-scripts'}
    <script src="{asset('/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/components-bootstrap-maxlength.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap-summernote/summernote.min.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/components-editors.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.plugins.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery.pulsate.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery-inputmask/inputmask/inputmask.date.extensions.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/jquery.form.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput-kartik/js/fileinput.min.js')}"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput-kartik/js/locales/es.js')}"></script>
    <script src="{asset('/global/scripts/xlsx.full.min.js')}"></script>
{/block}

{block 'post-scripts'}
{/block}

{block 'main'}
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject bold uppercase">
                            Filtrar
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir">
                        </a>
                    </div>
                </div>

                <div class="portlet-body form">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="control-label visible-ie8 visible-ie9" style="display: block;">Fecha desde</label>
                                <div class="input-group date form_datetime bs-datetime">
                                    <input class="form-control" type="text" data-bind="dateTimePicker: Filters().Desde, dateTimePickerOptions: {
                                        format: 'dd-mm-yyyy',
                                        momentFormat: 'DD-MM-YYYY',
                                        value: Filters().Desde,
                                        todayBtn: true,
                                        minView: 2,
                                        todayHighlight:true,
                                        autoclose:true
                                    },
                                    ">
                                    <span class="input-group-addon">
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="control-label visible-ie8 visible-ie9" style="display: block;">Fecha hasta</label>
                                <div class="input-group date form_datetime bs-datetime">
                                    <input class="form-control" type="text" data-bind="dateTimePicker: Filters().Hasta, dateTimePickerOptions: {
                                        format: 'dd-mm-yyyy',
                                        momentFormat: 'DD-MM-YYYY',
                                        value: Filters().Hasta,
                                        todayBtn: true,
                                        minView: 2,
                                        todayHighlight:true,
                                        autoclose:true
                                    },
                                    ">
                                    <span class="input-group-addon">
                                        <button class="btn default date-set" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="filters_comprador" class="control-label">
                                    Comprador sugerido
                                </label>
                                <select id="filters_comprador"
                                    data-bind="selectedOptions:Filters().CompradoresSelected,
                                    options: Filters().Compradores,
                                    valueAllowUnset: true,
                                    optionsText: 'text',
                                    optionsValue: 'id', select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions text-right">
                        <button data-bind="click: cleanFilters" type="button" class="btn green btn-outline">
                            Limpiar
                        </button>
                        <button data-bind="click: filter" type="button" class="btn green">
                            Buscar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6"></div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered" id="SolicitudesList">
                        <thead>
                            <tr>
                                <th>N&ordm; Solicitud</th>
                                <th>Nombre Solicitud</th>
                                <th>Area</th>
                                <th>Tipo compra</th>
                                <th>Comprador sugerido</th>
                            </tr>
                        </thead>
                        <tbody data-bind="dataTablesForEach : { data: Solicitudes, options: { paging: true, searching: false, pageLength: 10, bLengthChange: false } }">
                            <tr>
                                <td class="col-md-1" data-bind="text: Id"></td>
                                <td class="col-md-4" data-bind="text: Nombre"></td>
                                <td class="col-md-2" data-bind="text: Area"></td>
                                <td class="col-md-2" data-bind="text: TipoCompra"></td>
                                <td class="col-md-3" data-bind="text: CompradorSugerido"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="btn-group">
                        <a data-bind="click: downloadReport" download class="btn sbold blue" title="Descargar Reporte">
                            Descargar Reporte
                            <i class="fa fa-download" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}

{block 'knockout' append}
    <script type="text/javascript">
        var Filters = function(data) {
            data = data || {};

            this.Desde = ko.observable(data.Desde || null);
            this.Hasta = ko.observable(data.Hasta || null);
            this.Compradores = ko.observableArray(data.customers || []);
            this.CompradoresSelected = ko.observableArray(data.customersSelected || []);
        };

        var Solicitud = function(data) {
            data = data || {};
            this.Id = ko.observable(data.Id);
            this.Nombre = ko.observable(data.Nombre);
            this.Area = ko.observable(data.Area);
            this.TipoCompra = ko.observable(data.TipoCompra);
            this.CompradorSugerido = ko.observable(data.CompradorSugerido);
        };

        var SolicitudReport = function(data) {
            data = data || {};
            this.id = ko.observable(data.id);
            this.nombre = ko.observable(data.nombre);
            this.fechaCreacion = ko.observable(data.fechaCreacion);
            this.areaSolicitante = ko.observable(data.areaSolicitante);
            this.usuarioSolicitante = ko.observable(data.usuarioSolicitante);
            this.compradorSugerido = ko.observable(data.compradorSugerido);
            this.itemSolicitado = ko.observable(data.itemSolicitado);
            this.fechaResolucion = ko.observable(data.fechaResolucion);
            this.fechaEntrega = ko.observable(data.fechaEntrega);
            this.tipoCompra = ko.observable(data.tipoCompra);
            this.estado = ko.observable(data.estado);
            this.compradorDecision = ko.observable(data.compradorDecision);
            this.numeroLicitacionSubasta = ko.observable(data.numeroLicitacionSubasta);
            this.fechaTratamiento = ko.observable(data.fechaTratamiento);
        };

        var ConfiguracionesSolicitudes = function(data) {
            var self = this;
            data = data || {};

            this.Filters = ko.observable();
            this.Breadcrumbs = ko.observableArray(data.breadcrumbs || []);
            this.Solicitudes = ko.observableArray();
            this.Details = ko.observableArray();

            this.loadResults = function(list) {
                var newResults = [];
                var newDetails = [];

                (list || []).forEach(function(item) {
                    newResults.push(new Solicitud(item));
                    (item.Detalles || []).forEach(function(detail) {
                        newDetails.push(new SolicitudReport(detail));
                    });
                });

                self.Solicitudes(newResults);
                self.Details(newDetails);
            };

            self.downloadReport = function() {
                var solicitudes = this.Details();
                var wb = XLSX.utils.book_new();
                wb.Props = {
                    Title: 'Reporte solicitudes',
                    Subject: 'Reporte solicitudes',
                    Author: 'Optus',
                    CreatedDate: new Date()
                };
                wb.SheetNames.push('Hoja1');
                var ws_data = [
                    [
                        'Nro de solpeds',
                        'Nombre Solped',
                        'Fecha Creacion Solped',
                        'Area solicitante',
                        'Usuario solicitante',
                        'Comprador Sugerido',
                        'Items solicitados',
                        'Fecha de resolucion',
                        'Fecha de entrega',
                        'Tipo de Compra',
                        'Estado',
                        'Comprador que trato la solped',
                        'Nbro de Licitacion/Subasta',
                        'Fecha de tratamiento real de la solped'
                    ]
                ];

                solicitudes.forEach(function(item) {
                    ws_data.push([
                        item.id(),
                        item.nombre(),
                        item.fechaCreacion(),
                        item.areaSolicitante(),
                        item.usuarioSolicitante(),
                        item.compradorSugerido(),
                        item.itemSolicitado(),
                        item.fechaResolucion(),
                        item.fechaEntrega(),
                        item.tipoCompra(),
                        item.estado(),
                        item.compradorDecision(),
                        item.numeroLicitacionSubasta(),
                        item.fechaTratamiento()
                    ]);
                });

                var ws = XLSX.utils.aoa_to_sheet(ws_data);
                wb.Sheets['Hoja1'] = ws;
                ws['!cols'] = [
                    { wch: 11 },
                    { wch: 45 },
                    { wch: 20 },
                    { wch: 25 },
                    { wch: 25 },
                    { wch: 25 },
                    { wch: 45 },
                    { wch: 18 },
                    { wch: 18 },
                    { wch: 18 },
                    { wch: 18 },
                    { wch: 30 },
                    { wch: 22 },
                    { wch: 35 }
                ];

                XLSX.writeFile(wb, 'Solicitudes.xlsx');
            };

            this.filter = function() {
                $.blockUI();
                Services.Post('/reportes/solicitudes/filter', {
                        UserToken: User.Token,
                        Filters: JSON.stringify(ko.toJS(self.Filters))
                    },
                    function(response) {
                        $.unblockUI();
                        if (response.success) {
                            self.loadResults(response.data.list);
                        }
                    },
                    function(error) {
                        $.unblockUI();
                        swal('Error', error.message, 'error');
                    }
                );
            };

            this.cleanFilters = function() {
                self.initFilters();
                self.filter();
            };

            this.initFilters = function() {
                self.Filters(new Filters(data.filtros));
            };

            self.initFilters();
            self.loadResults(data.list);
        };

        jQuery(document).ready(function() {
            $.blockUI();
            Services.Get('/reportes/solicitudes/list', {
                    UserToken: User.Token
                },
                function(response) {
                    if (response.success) {
                        window.E = new ConfiguracionesSolicitudes(response.data);
                        AppOptus.Bind(E);
                    }
                    $.unblockUI();
                },
                function(error) {
                    $.unblockUI();
                    swal('Error', error.message, 'error');
                }
            );

            {chromeDebugString('dynamicScript')}
        });
    </script>
{/block}
