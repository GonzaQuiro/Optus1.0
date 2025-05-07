{extends 'usuarios/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/jquery-ui/jquery-ui.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery.pulsate.min.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

{block 'title'}
    {$title} "<span data-bind="text: FullName()"></span>"
{/block}

<!-- VISTA -->
{block 'user-detail'}
    <div class="row equal" style="margin-top: 25px;">
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">General</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir"
                            title="Retraer/Expandir"> </a>
                    </div>
                </div>
                <div class="portlet-body form expandir-1" style="display: block;">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Estado
                                </td>
                                <td class="col-md-4 text-right vertical-align-middle">
                                    <span class="label label-md" data-bind="text: EstadoDescripcion, css: { 
                                        'label-success': Estado() === 'active', 
                                        'label-warning': Estado() === 'inactive', 
                                        'label-danger': Estado() === 'blocked' 
                                    }">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Tipo
                                </td>
                                <td class="col-md-4 text-right vertical-align-middle">
                                    <span class="label label-md label-primary" data-bind="text: Tipo">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Empresa Asociada
                                </td>
                                <td class="col-md-4 text-right vertical-align-middle">
                                    <span class="label label-md label-success" data-bind="text: Empresa">
                                    </span>
                                </td>
                            </tr>
                            {if $type == 'client'}
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Area
                                </td>
                                <td class="col-md-4 text-right vertical-align-middle">
                                    <span class="label label-md label-success" data-bind="text: Area">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Rol
                                </td>
                                <td class="col-md-4 text-right vertical-align-middle">
                                    <span class="label label-md label-success" data-bind="text: Rol">
                                    </span>
                                </td>
                            </tr>   
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">
                            Contacto
                        </span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir"
                            title="Retraer/Expandir"> </a>
                    </div>
                </div>

                <div class="portlet-body form expandir-4">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Nombre
                                </td>
                                <td data-bind="text: Nombre" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Apellido
                                </td>
                                <td data-bind="text: Apellido" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Teléfono
                                </td>
                                <td data-bind="text: Telefono" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Celular
                                </td>
                                <td data-bind="text: Celular" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Email
                                </td>
                                <td data-bind="text: Email" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-right">
            <a href="{$urlBack}" type="button" class="btn default">
                Volver a Listado
            </a>
        </div>
    </div>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
    <script type="text/javascript">
        var UsuarioDetalle = function(data) {
            var self = this;

            this.Id = ko.observable(data.list.Id);
            this.Breadcrumbs = ko.observable(data.breadcrumbs);
            this.Estado = ko.observable(data.list.Estado);
            this.EstadoDescripcion = ko.observable(data.list.EstadoDescripcion);
            this.Tipo = ko.observable(data.list.Tipo);
            this.Empresa = ko.observable(data.list.Empresa);
            this.Username = ko.observable(data.list.Username);
            this.Nombre = ko.observable(data.list.Nombre);
            this.Apellido = ko.observable(data.list.Apellido);
            this.FullName = ko.observable(data.list.FullName);
            this.Telefono = ko.observable(data.list.Telefono);
            this.Celular = ko.observable(data.list.Celular);
            this.Email = ko.observable(data.list.Email);
            this.Area = ko.observable(data.list.Area);
            this.Rol = ko.observable(data.list.Rol);
        };

        jQuery(document).ready(function() {
            $.blockUI();

            var data = {
                UserToken: User.Token
            };
            var url = '/usuarios/detalle/data/' + params[2];
            Services.Get(url, data,
                (response) => {
                    $.unblockUI();
                    if (response.success) {
                        window.E = new UsuarioDetalle(response.data);
                        AppOptus.Bind(E);
                    }
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