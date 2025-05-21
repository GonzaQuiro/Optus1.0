{extends 'usuarios/main.tpl'}

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
    <script src="{asset('/global/plugins/jquery-ui/jquery-ui.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript">
    </script>
    <script src="{asset('/global/plugins/datatables/plugins/dataRender/ellipsis.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
    <script src="{asset('/js/geo.js')}" type="text/javascript"></script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3xU2zO42h1qL1s6bFkHsdhtv_hpvfxBo&callback=initMapEmpresa">
    </script>
    <script src="{asset('/global/plugins/icheck/icheck.min.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/form-icheck.min.js')}" type="text/javascript"></script>
{/block}

{block 'title'}
    {$title}
{/block}

<!-- VISTA -->
{block 'user-list'}
    <div class="portlet light bordered">
        {* {if isAdmin()} *}
            <div class="portlet-title">
                <div class="btn-group">
                    <a href="/usuarios/nuevo/{$type}" id="sample_editable_1_new" class="btn sbold green">
                        Agregar nuevo Usuario
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
        {* {/if} *}
        <div class="portlet-body">
            <div class="table-toolbar">
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </div>
            <table class="table table-striped table-bordered" id="sample_1">
                <thead>
                    <tr>
                        <th>
                            Nombre
                        </th>
                        <th>
                            Apellido
                        </th>                  
                        <th class="text-center">
                            Email
                        </th>
                        <th class="text-center">
                            Empresa Asociada
                        </th>
                        <th class="text-center">
                            Tipo
                        </th>
                        <th class="text-center">
                            Estado
                        </th>
                        {if $type == 'client'}
                        <th class="text-center">
                            Área
                        </th>
                        <th class="text-center">
                            Rol
                        </th>
                        {/if}
                        <th style="text-align: center;"> Acciones </th>
                    </tr>
                </thead>

                <tbody
                    data-bind="dataTablesForEach : { data: List, options: { paging: true, renderTruncate: { targets: [0, 1, 2, 3], cutOff: 15 } }}">
                    <tr>
                        <td data-bind="text: Nombre"></td>
                        <td data-bind="text: Apellido"></td>
                        <td class="text-center" data-bind="text: Email"></td>
                        <td class="text-center" data-bind="text: EmpresaAsociada"></td>
                        
                        <td>
                            <span class="label label-sm labelAlign" data-bind="text: TipoDescripcion, css: {
                                'label-danger': Tipo === 'superadmin',
                                'label-warning': Tipo === 'admin',
                                'label-info': (Tipo === 'customer' || Tipo === 'customer-approve' || Tipo === 'customer-read' || Tipo === 'evaluator' || Tipo === 'supervisor'),
                                'label-primary': Tipo === 'offerer'
                            }">
                            </span>
                        </td>
                        <td>
                            <span class="label label-sm labelAlign" data-bind="text: EstadoDescripcion, css: {
                                'label-success': Estado === 'active',
                                'label-warning': Estado === 'inactive',
                                'label-danger': Estado === 'blocked'
                            }">
                            </span>
                        </td>
                        {if $type == 'client'}
                        <td class="text-center">
                            <span class="label label-sm label-info labelAlign" data-bind="text: Area"></span>
                        </td> <!-- Mostrar el área con label-info -->
                        
                        <td class="text-center">
                            <span class="label label-sm label-info labelAlign" data-bind="text: Rol"></span>
                        </td> <!-- Mostrar el rol con label-info -->
                        {/if}
                        <td style="text-align: center;">

                            <a href="javascript:void(0);" class="btn btn-xs green"
                                data-bind="click: function() { $root.Editar(Id) }">
                                Editar
                                <i class="fa fa-pencil"></i>
                            </a>
                     
                            <a data-bind="attr: {literal}{ href: '/usuarios/edicion/' + Id + '/permisos' }{/literal}"
                                class="btn btn-xs green" title="Permisos">
                                Permisos
                                <i class="fa fa-lock"></i>
                            </a>
                            <a data-bind="click: $root.Eliminar.bind($data, Id)" class="btn btn-xs btn-danger"
                                title="Eliminar">
                                Eliminar
                                <i class="fa fa-trash-o"></i>
                            </a>
                            <a data-bind="attr: {literal}{href: '/usuarios/detalle/' + Id}{/literal}"
                                class="btn btn-xs green" title="Detalle">
                                Detalle
                                <i class="fa fa-show"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
    <script type="text/javascript">
        var UsuariosListado = function(data) {
            var self = this;

            this.List = ko.observableArray(data.list);
            this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

            self.Editar = function (id) {
                $.post('/usuarios/guardar-id-edicion', { id: id }, function(response) {
                    if (response.success) {
                        window.location.href = '/usuarios/edicion/' + id;
                    } else {
                        swal('Error', response.message, 'error');
                    }
                }).fail(function () {
                    swal('Error', 'No se pudo iniciar la edición.', 'error');
                });
            };

            this.Eliminar = function(id) {
                swal({
                    title: '¿Está seguro de que desea eliminar el usuario?',
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
                        var url = '/usuarios/delete/' + id;
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
            var url = '{$urlList}';
            Services.Get(url, {
                    UserToken: User.Token
                },
                (response) => {
                    if (response.success) {
                        window.E = new UsuariosListado(response.data);
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