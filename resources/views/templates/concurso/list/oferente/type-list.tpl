{extends 'concurso/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
    <style>
        #ListaConcursosEnPreparacion_wrapper div.row,
        #ListaConcursosConvocatoriaOferentes_wrapper div.row,
        #ListaConcursosPropuestasTecnicas_wrapper div.row,
        #ListaConcursosAnalisisOfertas_wrapper div.row,
        #ListaConcursosEvaluacionReputacion_wrapper div.row,
        #ListaConcursosInformes_wrapper div.row,
        #ListaConcursosInvitaciones_wrapper div.row,
        #ListaConcursosTecnicas_wrapper div.row,
        #ListaConcursosEconomicas_wrapper div.row,
        #ListaConcursosAnalisis_wrapper div.row,
        #ListaConcursosAdjudicados_wrapper div.row {
            display: none;
        }
    </style>
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript">
    </script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

{block 'title'}
    {$title}
{/block}

<!-- VISTA -->
{block 'concurso-list-offerer'}
    <div class="row">
        <div class="col-md-12 margin-bottom-20">
            <label class="control-label text-center" style="display: block;">
                Buscar
            </label>
            <div style="display: flex; justify-content: center;">
                <div class="input-group" style="max-width: 14.5vw; width: 100%;">
                    <input type="text" class="form-control"
                        data-bind="value: Filters().searchTerm"
                        placeholder="Nombre, Solicitante o ID de concurso">
                    <span class="input-group-addon" 
                        data-bind="visible: Filters().isIdSearch(), style: { color: 'green' }">
                        <i class="fa fa-id-card"></i> Búsqueda por ID
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="portlet box red-thunderbird">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-envelope"></i>
                        <span class="caption-subject bold">Invitaciones</span>
                        <span class="caption-helper font-white"
                            data-bind="text: Lists().ListaConcursosInvitaciones().length">
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" data-original-title="" data-bind="css: { 
                            'collapse': Lists().ListaConcursosInvitaciones().length > 0,
                            'expand': Lists().ListaConcursosInvitaciones().length == 0 
                        }" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body" data-bind="style: { 
                    'display': Lists().ListaConcursosInvitaciones().length > 0 ? 'block' : 'none'
                }">
                    <table class="table table-striped table-bordered ListaConcursos" id="ListaConcursosInvitaciones">
                        <thead>
                            <tr>
                                <th> Nº Concurso </th>
                                <th> Nombre del concurso </th>
                                <th> Área Solicitante </th>
                                <th> Comprador </th>
                                <th> N° de Solicitud </th>
                                <th> Fecha Límite </th>
                                <th> Tipo de concurso </th>
                                <th class="text-center"> Acciones </th>
                            </tr>
                        </thead>

                        <tbody
                            data-bind="dataTablesForEach : { data: Lists().ListaConcursosInvitaciones, options: { paging: false } }">
                            <tr>
                            <td data-bind="text: Id()" class="vertical-align-middle"></td>
                            <td data-bind="text: Nombre()" class="vertical-align-middle"></td>
                            <td data-bind="text: AreaSolicitante()" class="vertical-align-middle"></td>
                            <td data-bind="text: UsuarioSolicitante()" class="vertical-align-middle"></td>
                            <td data-bind="text: NumSolicitud()" class="vertical-align-middle"></td>
                            <td data-bind="text: FechaLimite()" class="vertical-align-middle"></td>
                            <td data-bind="text: TipoConcurso()" class="vertical-align-middle"></td>
                                <td class="vertical-align-middle text-center">
                                    <a data-bind="attr: {literal}{href: '/concursos/oferente/' + TipoConcursoPath() + '/invitacion/' + Id()}{/literal}"
                                        class="btn btn-xs red-thunderbird" title="Acceder">
                                        Acceder
                                        <i class="fa fa-play"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="portlet box yellow-gold">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>
                        <span class="caption-subject bold">Presentación propuestas técnicas</span>
                        <span class="caption-helper font-white" data-bind="text: Lists().ListaConcursosTecnicas().length">
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" data-original-title="" data-bind="css: { 
                            'collapse': Lists().ListaConcursosTecnicas().length > 0,
                            'expand': Lists().ListaConcursosTecnicas().length == 0 
                        }" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body" data-bind="style: { 
                    'display': Lists().ListaConcursosTecnicas().length > 0 ? 'block' : 'none'
                }">
                    <table class="table table-striped table-bordered ListaConcursos" id="ListaConcursosTecnicas">
                        <thead>
                            <tr>
                                <th> Nº Concurso </th>
                                <th> Nombre del concurso </th>
                                <th> Área Solicitante </th>
                                <th> Comprador </th>
                                <th> N° de Solicitud </th>
                                <th> Fecha Límite </th>
                                <th> Tipo de concurso </th>
                                <th class="text-center"> Acciones </th>
                            </tr>
                        </thead>
                        <tbody
                            data-bind="dataTablesForEach : { data: Lists().ListaConcursosTecnicas, options: { paging: false }}">
                            <tr>
                                <td data-bind="text: Id()" class="vertical-align-middle"></td>
                                <td data-bind="text: Nombre()" class="vertical-align-middle"></td>
                                <td data-bind="text: AreaSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: UsuarioSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: NumSolicitud()" class="vertical-align-middle"></td>
                                <td data-bind="text: FechaLimite()" class="vertical-align-middle"></td>
                                <td data-bind="text: TipoConcurso()" class="vertical-align-middle"></td>
                                <td class="vertical-align-middle text-center">
                                    <a data-bind="attr: {literal}{href: '/concursos/oferente/' + TipoConcursoPath() + '/tecnica/' + Id()}{/literal}"
                                        class="btn btn-xs yellow-gold" title="Editar">
                                        Acceder
                                        <i class="fa fa-play"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="portlet box yellow-lemon">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-money"></i>
                        <span class="caption-subject bold">Presentación propuestas económicas</span>
                        <span class="caption-helper font-white" data-bind="text: Lists().ListaConcursosEconomicas().length">
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" data-original-title="" data-bind="css: { 
                            'collapse': Lists().ListaConcursosEconomicas().length > 0,
                            'expand': Lists().ListaConcursosEconomicas().length == 0 
                        }" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body" data-bind="style: { 
                    'display': Lists().ListaConcursosEconomicas().length > 0 ? 'block' : 'none'
                }">
                    <table class="table table-striped table-bordered ListaConcursos" id="ListaConcursosEconomicas">
                        <thead>
                            <tr>
                            <th> Nº Concurso </th>
                                <th> Nombre del concurso </th>
                                <th> Área Solicitante </th>
                                <th> Comprador </th>
                                <th> N° de Solicitud </th>
                                <th> Fecha Límite </th>
                                <th> Tipo de concurso </th>
                                <th class="text-center"> Acciones </th>
                            </tr>
                        </thead>
                        <tbody
                            data-bind="dataTablesForEach : { data: Lists().ListaConcursosEconomicas, options: { paging: false }}">
                            <tr>
                                <td data-bind="text: Id()" class="vertical-align-middle"></td>
                                <td data-bind="text: Nombre()" class="vertical-align-middle"></td>
                                <td data-bind="text: AreaSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: UsuarioSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: NumSolicitud()" class="vertical-align-middle"></td>
                                <td data-bind="text: FechaLimite()" class="vertical-align-middle"></td>
                                <td data-bind="text: TipoConcurso()" class="vertical-align-middle"></td>
                                <td class="vertical-align-middle text-center">
                                    <a data-bind="attr: {literal}{href: '/concursos/oferente/' + TipoConcursoPath() + '/economica/' + Id()}{/literal}"
                                        class="btn btn-xs yellow-lemon" title="Editar">
                                        Acceder
                                        <i class="fa fa-play"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-star"></i>
                        <span class="caption-subject bold">Análisis de ofertas</span>
                        <span class="caption-helper font-white" data-bind="text: Lists().ListaConcursosAnalisis().length">
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" data-original-title="" data-bind="css: { 
                            'collapse': Lists().ListaConcursosAnalisis().length > 0,
                            'expand': Lists().ListaConcursosAnalisis().length == 0 
                        }" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body" data-bind="style: { 
                    'display': Lists().ListaConcursosAnalisis().length > 0 ? 'block' : 'none'
                }">
                    <table class="table table-striped table-bordered ListaConcursos" id="ListaConcursosAnalisis">
                        <thead>
                            <tr>
                            <th> Nº Concurso </th>
                                <th> Nombre del concurso </th>
                                <th> Área Solicitante </th>
                                <th> Comprador </th>
                                <th> N° de Solicitud </th>
                                <th> Fecha Límite </th>
                                <th> Tipo de concurso </th>
                                <th class="text-center"> Acciones </th>
                            </tr>
                        </thead>
                        <tbody
                            data-bind="dataTablesForEach : { data: Lists().ListaConcursosAnalisis, options: { paging: false }}">
                            <tr>
                                <td data-bind="text: Id()" class="vertical-align-middle"></td>
                                <td data-bind="text: Nombre()" class="vertical-align-middle"></td>
                                <td data-bind="text: AreaSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: UsuarioSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: NumSolicitud()" class="vertical-align-middle"></td>
                                <td data-bind="text: FechaLimite()" class="vertical-align-middle"></td>
                                <td data-bind="text: TipoConcurso()" class="vertical-align-middle"></td>
                                <td class="vertical-align-middle text-center">
                                    <a data-bind="attr: {literal}{href: '/concursos/oferente/' + TipoConcursoPath() + '/analisis/' + Id()}{/literal}"
                                        class="btn btn-xs green" title="Editar">
                                        Acceder
                                        <i class="fa fa-play"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="portlet box green-jungle">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-archive"></i>
                        <span class="caption-subject bold">Concursos adjudicados</span>
                        <span class="caption-helper font-white"
                            data-bind="text: Lists().ListaConcursosAdjudicados().length">
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" data-original-title="" data-bind="css: { 
                            'collapse': Lists().ListaConcursosAdjudicados().length > 0,
                            'expand': Lists().ListaConcursosAdjudicados().length == 0 
                        }" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body" data-bind="style: { 
                    'display': Lists().ListaConcursosAdjudicados().length > 0 ? 'block' : 'none'
                }">
                    <table class="table table-striped table-bordered ListaConcursos" id="ListaConcursosAdjudicados">
                        <thead>
                            <tr>
                                <th> Nº Concurso </th>
                                <th> Nombre del concurso </th>
                                <th> Área Solicitante </th>
                                <th> Comprador </th>
                                <th> N° de Solicitud </th>
                                <th> Fecha Límite </th>
                                <th> Tipo de concurso </th>
                                <th> Estado </th>
                                <th class="text-center"> Acciones </th>
                            </tr>
                        </thead>
                        <tbody
                            data-bind="dataTablesForEach : { data: Lists().ListaConcursosAdjudicados, options: { paging: false }}">
                            <tr>
                                <td data-bind="text: Id()" class="vertical-align-middle"></td>
                                <td data-bind="text: Nombre()" class="vertical-align-middle"></td>
                                <td data-bind="text: AreaSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: UsuarioSolicitante()" class="vertical-align-middle"></td>
                                <td data-bind="text: NumSolicitud()" class="vertical-align-middle"></td>
                                <td data-bind="text: FechaLimite()" class="vertical-align-middle"></td>
                                <td data-bind="text: TipoConcurso()" class="vertical-align-middle"></td>
                                <td data-bind="text: EstadoAdjudicacion()" class="vertical-align-middle"></td>
                                <td class="vertical-align-middle text-center">
                                    <a data-bind="attr: {literal}{href: '/concursos/oferente/' + TipoConcursoPath() + '/adjudicado/' + Id()}{/literal}"
                                        class="btn btn-xs green-jungle" title="Editar">
                                        Acceder
                                        <i class="fa fa-play"></i>
                                    </a>
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
        var ListItem = function(data) {
            var self = this;

            this.Id = ko.observable(data.Id);
            this.Nombre = ko.observable(data.Nombre);
            this.Solicitante = ko.observable(data.Solicitante);
            this.NumSolicitud = ko.observable(data.NumSolicitud);
            this.FechaLimite = ko.observable(data.FechaLimite);
            this.TipoConcurso = ko.observable(data.TipoConcurso);
            this.TipoConcursoPath = ko.observable(data.TipoConcursoPath);
            this.EstadoAdjudicacion = ko.observable(data.EstadoAdjudicacion);
            this.UsuarioSolicitante = ko.observable(data.UsuarioSolicitante);
            this.AreaSolicitante = ko.observable(data.AreaSolicitante);
        }

        var List = function(data) {
            var self = this;

            this.ListaConcursosInvitaciones = ko.observableArray([]);
            if (data.ListaConcursosInvitaciones.length > 0) {
                data.ListaConcursosInvitaciones.forEach(item => {
                    self.ListaConcursosInvitaciones.push(new ListItem(item));
                });
            }
            this.ListaConcursosTecnicas = ko.observableArray([]);
            if (data.ListaConcursosTecnicas.length > 0) {
                data.ListaConcursosTecnicas.forEach(item => {
                    self.ListaConcursosTecnicas.push(new ListItem(item));
                });
            }
            this.ListaConcursosEconomicas = ko.observableArray([]);
            if (data.ListaConcursosEconomicas.length > 0) {
                data.ListaConcursosEconomicas.forEach(item => {
                    self.ListaConcursosEconomicas.push(new ListItem(item));
                });
            }
            this.ListaConcursosAnalisis = ko.observableArray([]);
            if (data.ListaConcursosAnalisis.length > 0) {
                data.ListaConcursosAnalisis.forEach(item => {
                    self.ListaConcursosAnalisis.push(new ListItem(item));
                });
            }
            this.ListaConcursosAdjudicados = ko.observableArray([]);
            if (data.ListaConcursosAdjudicados.length > 0) {
                data.ListaConcursosAdjudicados.forEach(item => {
                    self.ListaConcursosAdjudicados.push(new ListItem(item));
                });
            }
        }

        var Filters = function(parent) {
            var self = this;
            
            //Search observable
            this.searchTerm = ko.observable(null);

            //Subscribe to search term changes
            this.searchTerm = ko.observable(null);
            self.searchTerm.subscribe((value) => {
                parent.filter(self);
            });

            //Detect if searching by ID (all digits)
            this.isIdSearch = ko.computed(function() {
                return /^\d+$/.test(self.searchTerm());
            });
        };

        var ConcursosListados = function(data) {
            var self = this;

            this.Breadcrumbs = ko.observableArray(data.breadcrumbs);
            this.Lists = ko.observable(new List(data.list));

            this.filter = function(filters) {
                if (filters) {
                    $.blockUI()
                    var data = {};
                    Services.Post('/concursos/oferente/list', {
                            UserToken: User.Token,
                            Entity: JSON.stringify(ko.toJS(data)),
                            Filters: JSON.stringify(ko.toJS(filters))
                        },
                        (response) => {
                            if (response.success) {
                                self.Lists(new List(response.data.list))
                            }
                            $.unblockUI();
                        },
                        (error) => {
                            $.unblockUI();
                            swal('Error', error.message, 'error');
                        },
                        null,
                        null
                    );
                }
            };
            this.Filters = ko.observable(new Filters(self));
        };

        jQuery(document).ready(function() {
            $.blockUI();
            var data = {};
            Services.Get('/concursos/oferente/list', {
                    UserToken: User.Token,
                    Entity: JSON.stringify(ko.toJS(data))
                },
                (response) => {
                    if (response.success) {
                        window.E = new ConcursosListados(response.data);
                        AppOptus.Bind(E);
                    }
                    $.unblockUI();
                },
                (error) => {
                    $.unblockUI();
                    swal('Error', error.message, 'error');
                },
                null,
                null
            );
        });

        // Chrome allows you to debug it thanks to this
        {chromeDebugString('dynamicScript')}
    </script>
{/block}