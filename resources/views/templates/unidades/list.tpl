{extends 'unidades/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/datatables/datatables.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'measurement-list'}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="btn-group">
                    <a 
                        href="/configuraciones/unidades/nuevo"
                        class="btn sbold green">
                        Agregar Nueva Unidad
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
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
                <table class="table table-striped table-bordered" id="listaUnidades">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody data-bind="dataTablesForEach : { data: ListaUnidades, options: { paging: true }}">
                        <tr>
                            <td class="col-md-6" data-bind="text: Nombre"></td>
                            <td class="col-md-4 text-center">
                                <a data-bind="attr: {literal}{href: '/configuraciones/unidades/edicion/' + Id()}{/literal}" class="btn btn-xs green" title="Editar">
                                    Editar 
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <!-- ko if: !Eliminado() -->
                                <a data-bind="click: $root.Eliminar.bind($data, Id())" class="btn btn-xs btn-danger" title="Eliminar">
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
var Unidad = function (data) {
    var self = this;

    this.Id = ko.observable(data.Id);
    this.Nombre = ko.observable(data.Nombre);
    this.Eliminado = ko.observable(data.Eliminado);
}

var ConfiguracionesUnidadesListado = function (data) {
    var self = this;

    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);
    this.ListaUnidades = ko.observableArray();
    
    if (data.list.length > 0) {
        data.list.forEach(item => {
            self.ListaUnidades.push(new Unidad(item));
        });
    }

    this.Eliminar = function (id) {
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
        }, function (result) {
            swal.close();
            if (result) {
                $.blockUI();
                var data = { 
                    UserToken: User.Token
                };
                var url = '/configuraciones/unidades/toggle/' + id;
                Services.Post(url, data, 
                    (response) => {
                        swal.close();
                        $.unblockUI();
                        if (response.success) {
                            setTimeout(function () {
                                swal({
                                    title: 'Hecho',
                                    text: response.message,
                                    type: 'success',
                                    closeOnClickOutside: false,
                                    closeOnConfirm: true,
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonClass: 'btn btn-success'
                                }, function (result) {
                                    if (response.data.redirect) {
                                        window.location.href = response.data.redirect;
                                    } else {
                                        location.reload();
                                    }
                                });
                            }, 500);
                        } else {
                            setTimeout(function () {
                                swal('Error', response.message, 'error');
                            }, 500);
                        }
                    }, 
                    (error) => {
                        swal.close();
                        $.unblockUI();
                        setTimeout(function () {
                            swal('Error', error.message, 'error');
                        }, 500);
                    }
                );
            }
        });
    };
};

jQuery(document).ready(function () {
    $.blockUI();
    var data = {
        UserToken: User.Token
    };
    var url = '/configuraciones/unidades/list';
    Services.Get(url, data, 
        (response) => {
            if (response.success) {
                window.E = new ConfiguracionesUnidadesListado(response.data);
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