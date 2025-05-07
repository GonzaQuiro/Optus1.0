{extends 'reportes/evaluados/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/datatables/datatables.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}" rel="stylesheet"
        type="text/css" />
    <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript">
    </script>
    <script src="{asset('/global/scripts/xlsx.full.min.js')}"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.min.js"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'reports-list'}
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <div class="table-toolbar">
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-bordered" id="ConcursoAdj">
                        <thead>
                            <tr>
                                <th>Nº Concurso</th>
                                <th>Nombre Licitación</th>
                                <th>Tipo Adjudicación</th>
                                <th>Evaluadores</th>
                                <th>Proveedor/es Adjudicados</th>
                            </tr>
                        </thead>
                        <tbody
                            data-bind="dataTablesForEach : { data: Concursos, options: { paging: true, searching: false, pageLength: 10, bLengthChange: false }}">
                            <tr>
                                <td class="col-md-1" data-bind="text: Id"></td>
                                <td class="col-md-5" data-bind="text: Nombre"></td>
                                <td class="col-md-2" data-bind="text: Tipo"></td>
                                <td class="col-md-2" data-bind="text: Evaluadores"></td>
                                <td class="col-md-4" data-bind="text: Proveedores"></td>
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

<!-- KNOCKOUT JS -->
{block 'knockout' append}
    <script type="text/javascript">
        var Filters = function(data) {
            var self = this;
            this.Compradores = ko.observable(data.customers);
            this.Proveedores = ko.observable(data.offerers)
            this.CompradoresSelected = ko.observable(data.customersSelected);
            this.ProveedoresSelected = ko.observable(data.offerersSelected)

            self.CompradoresSelected.subscribe((CompradoresSelected) => {
                if (CompradoresSelected.length > 0) {
                    var compradores = self.Compradores()
                    var offerersSelected = compradores.filter(comprador => CompradoresSelected.includes(
                        comprador.id)).map(comprador => comprador.offerers).flat();
                    self.Proveedores(offerersSelected)
                }
            })
        }

        var ConcursoAdj = function(data) {
            var self = this;

            this.Id = ko.observable(data.Id);
            this.Nombre = ko.observable(data.Nombre);
            this.Tipo = ko.observable(data.Tipo);
            this.Evaluadores = ko.observable(data.Evaluadores);
            this.Proveedores = ko.observable(data.Proveedores);
        }

        var ConcursoReport = function(data) {
            this.Concurso = ko.observable(data.Concurso)
            this.Nombre = ko.observable(data.Nombre)
            this.RazonSocial = ko.observable(data.RazonSocial)
            this.Puntualidad = ko.observable(data.Puntualidad)
            this.Calidad = ko.observable(data.Calidad)
            this.OrdenYlimpieza = ko.observable(data.OrdenYlimpieza)
            this.MedioAmbiente = ko.observable(data.MedioAmbiente)
            this.HigieneYseguridad = ko.observable(data.HigieneYseguridad)
            this.Experiencia = ko.observable(data.Experiencia)
            this.Comentario = ko.observable(data.Comentario)
            this.puntaje = ko.observable(data.puntaje)
        }

        var ConfiguracionesConcursoAdj = function(data) {
            var self = this;

            this.Filters = ko.observable();
            this.Breadcrumbs = ko.observableArray(data.breadcrumbs);
            this.Concursos = ko.observableArray();
            this.Details = ko.observableArray();

            if (data.list.length > 0) {
                data.list.forEach(item => {
                    self.Concursos.push(new ConcursoAdj(item));
                    item.Detalles.forEach(i => {
                        self.Details.push(new ConcursoReport(i));
                    })
                });
            }

            self.downloadReport = function() {
                var concursos = this.Details();

                var wb = XLSX.utils.book_new();
                wb.Props = {
                    Title: "Reporte concursos evaluados",
                    Subject: "Reporte concursos evaluados",
                    Author: "Optus",
                    CreatedDate: new Date()
                };
                wb.SheetNames.push("Concursos");
                var ws_data = [
                    [
                        'Nº Concurso',
                        'Nombre Licitación',
                        'Proveedor',
                        'Puntaje',
                        'Puntualidad',
                        'Calidad',
                        'Orden y limpieza',
                        'Medio ambiente',
                        'Higiene y seguridad',
                        'Experiencia',
                        'Comentario'
                    ]
                ];

                concursos.forEach(item => {
                    var vItem = [];
                    vItem.push(
                        item['Concurso'](),
                        item['Nombre'](),
                        item['RazonSocial'](),
                        item['puntaje'](),
                        item['Puntualidad'](),
                        item['Calidad'](),
                        item['OrdenYlimpieza'](),
                        item['MedioAmbiente'](),
                        item['HigieneYseguridad'](),
                        item['Experiencia'](),
                        item['Comentario']()
                    );
                    ws_data.push(vItem);
                });

                var ws = XLSX.utils.aoa_to_sheet(ws_data);

                wb.Sheets["Concursos"] = ws;

                var wscols = [
                    { wch: 11 },
                    { wch: 60 },
                    { wch: 60 },
                    { wch: 8 },
                    { wch: 11 },
                    { wch: 7 },
                    { wch: 16 },
                    { wch: 15 },
                    { wch: 18 },
                    { wch: 11 },
                    { wch: 100 }
                ];

                ws['!cols'] = wscols;

                XLSX.writeFile(wb, 'Concursos_Evaluados.xlsx');

            };

            this.filter = function() {
            $.blockUI();
            var url = '/reportes/evaluados/filter';
            Services.Post(url, {
                    UserToken: User.Token,
                    Filters: JSON.stringify(ko.toJS(self.Filters))
                },
                (response) => {
                    $.unblockUI();
                    if (response.success) {
                        self.Concursos.removeAll();
                        self.Details.removeAll();
                        if (response.data.list.length > 0) {
                            var newResults = [];
                            var newDetails = [];
                            response.data.list.forEach(item => {
                                newResults.push(new ConcursoAdj(item));
                                item.Detalles.forEach(i => {
                                    newDetails.push(new ConcursoReport(i));
                                })
                            });
                            self.Concursos(newResults);
                            self.Details(newDetails);
                        }
                    }
                },
                (error) => {
                    $.unblockUI();
                    swal('Error', error.message, 'error');
                }
            );
        }

        this.cleanFilters = function() {
            self.initFilters();
            self.filter();
        }

        this.initFilters = function() {
            self.Filters(new Filters(data.filtros));
        }

        self.initFilters();
        };

        jQuery(document).ready(function() {
            $.blockUI();
            var data = {
                UserToken: User.Token
            };
            var url = '/reportes/evaluados/list';
            Services.Get(url, data,
                (response) => {
                    if (response.success) {
                        window.E = new ConfiguracionesConcursoAdj(response.data);
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