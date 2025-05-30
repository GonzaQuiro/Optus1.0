{extends 'concurso/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/datatables/datatables.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/datatables/datatables.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

{block 'title'}
    {$title}
{/block}

<!-- VISTA -->
{block 'concurso-list'}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            {if isCustomer() || isAdmin()}
                <div class="portlet-title">
                    <div class="btn-group">
                        <a href="/concursos/{if $tipo eq 'online'}online{elseif $tipo eq 'sobrecerrado'}sobrecerrado{elseif $tipo eq 'go'}go{/if}/nuevo" id="sample_editable_1_new" class="btn sbold green"> Agregar Nueva {if $tipo eq 'online'}Subasta{elseif $tipo eq 'sobrecerrado'}Licitación{elseif $tipo eq 'go'}Go{/if}
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>
                </div>
            {/if}
            <div class="portlet-body">
                <table class="table table-striped table-bordered" id="listaConcursos">
                    <thead>
                        <tr>
                            <th>
                                Nº Concurso
                            </th>
                            <th>
                                Imagen
                            </th>
                            <th>
                                Nombre
                            </th>
                            <th>
                                Creado por
                            </th>
                            <th>
                                Tipo Operación
                            </th>
                            <th class="text-center">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody data-bind="dataTablesForEach : { data: ListaConcursos, options: { paging: true }}">
                        <tr>
                            <td 
                                class="vertical-align-middle" 
                                data-bind="text: Id">
                            </td>
                            <td 
                                class="col-md-4 text-center vertical-align-middle" 
                                data-bind="style: {literal}{backgroundImage: 'url(' + ImagePath + Portrait + ')'}{/literal}" 
                                style="width: auto;height: 150px;background-repeat: no-repeat;background-position: center center;background-size:cover;border: 1px solid #ddd;">
                            </td>
                            <td 
                                class="vertical-align-middle" 
                                data-bind="text: Nombre">
                            </td>
                            <td 
                                class="vertical-align-middle" 
                                data-bind="text: creadoPor">
                            </td>
                            <td 
                                class="vertical-align-middle" 
                                data-bind="html: TipoOperacion">
                            </td>
                            <td class="text-center vertical-align-middle">
                                {if isAdmin() || isCustomer()}
                                    <a href="javascript:void(0);"
                                        data-bind="click: function() { $root.EditarConcurso(Id, '{$tipo}') }, visible: User.Tipo !== 5"
                                        class="btn btn-xs green" title="Editar">
                                        Editar 
                                        <i class="fa fa-pencil"></i>
                                    </a>

                                    <a href="javascript:void(0);" class="btn btn-xs purple" title="Copiar"
                                        data-bind="click: function() { $root.CopiarConcurso(Id, '{$tipo}') }">
                                        Copiar 
                                        <i class="fa fa-copy"></i>
                                    </a>

                                    <a data-bind="click: function () {literal}{ $root.Eliminar(Id) }{/literal}, visible: User.Tipo !== 5" class="btn btn-xs btn-danger" title="Eliminar">
                                        Eliminar
                                        <i class="fa fa-trash-o"></i>
                                    </a>
                                {/if}
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
var ConcursosListado = function (data) {
    var self = this;
    console.log("Usuario", User)

    this.EditarConcurso = function(id, tipo) {
        $.post('/concursos/guardar-token-acceso', { id: id }, function(response) {
            if (response.success) {
                window.location.href = '/concursos/' + tipo + '/edicion/' + id;
            } else {
                swal('Error', response.message, 'error');
            }
        }).fail(function () {
            swal('Error', 'No se pudo iniciar la edición.', 'error');
        });
    };

    this.CopiarConcurso = function(id, tipo) {
        $.post('/concursos/guardar-token-acceso', { id: id }, function(response) {
            if (response.success) {
                window.location.href = '/concursos/' + tipo + '/nuevo?concurso=' + id;
            } else {
                swal('Error', response.message, 'error');
            }
        }).fail(function () {
            swal('Error', 'No se pudo iniciar la copia del concurso.', 'error');
        });
    };


    this.Eliminar = function (id) {
        swal({
            title: 'Cancelación de Concurso',
            text: '¿Por qué deseas cancelar el concurso?',
            type: 'input',
            inputPlaceholder: 'Escribe un motivo (obligatorio)',
            closeOnClickOutside: false,
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: false,
            confirmButtonText: 'Aceptar',
            confirmButtonClass: 'btn btn-success',
            cancelButtonText: 'Cancelar',
            cancelButtonClass: 'btn btn-default'
        }, function (result) {
            swal.close();
            if (result !== false) {
                $.blockUI();
                var url = '/concursos/delete/' + id;
                Services.Post(url, {
                        UserToken: User.Token,
                        Reason: result
                    },
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
                                        location.reload();
                                    }
                                });
                            }, 500);
                        } else {
                            setTimeout(function () {
                                swal('Error', 'Han ocurrido errores al enviar los correos.', 'error');
                            }, 500);
                        }
                    },
                    (error) => {
                        swal.close();
                        $.unblockUI();
                        setTimeout(function () {
                            swal('Error', error.message, 'error');
                        }, 500);
                    },
                    null,
                    null
                );
            }
        });
    };

    this.ListaConcursos = ko.observableArray(data.list);
    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);
};

jQuery(document).ready(function () {
    $.blockUI();
    var url = '/concursos/cliente/' + '{$tipo}' + '/list';
    Services.Get(url, {
            UserToken: User.Token
        }, 
        (response) => {
            if (response.success) {
                window.E = new ConcursosListado(response.data);
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