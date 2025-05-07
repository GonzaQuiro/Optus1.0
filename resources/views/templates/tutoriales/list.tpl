{extends 'tutoriales/main.tpl'}

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
    <script src="{asset('/global/plugins/icheck/icheck.min.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/form-icheck.min.js')}" type="text/javascript"></script>
{/block}

{block 'title'}
    {$title}
{/block}

<!-- VISTA -->
{block 'tutorial-list'}
    {if isAdmin()}
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-plus"></i> Agregar Tutorial
                </div>
            </div>
            
            <div class="portlet-body">
                <form>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <input type="text" class="form-control" id="tutorialName" placeholder="Nombre">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="text" class="form-control" id="tutorialDescription" placeholder="Descripción">
                        </div>
                        <div class="form-group col-md-3">
                            <input type="text" class="form-control" id="tutorialLink" placeholder="Link">
                        </div>
                        <div class="form-group col-sm-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" data-bind="click: $root.Agregar">
                                <i class="fa fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                    <!-- Checkboxes -->
                    <div class="row">
                        <div class="form-group col-md-1">
                            <label>
                                <input type="checkbox" id="clienteCheckbox" name="cliente"> Cliente
                            </label>
                        </div>
                        <div class="form-group col-md-1">
                            <label>
                                <input type="checkbox" id="tecnicoCheckbox" name="tecnico"> Técnico
                            </label>
                        </div>
                        <div class="form-group col-md-1">
                            <label>
                                <input type="checkbox" id="visorCheckbox" name="visor"> Visor
                            </label>
                        </div>
                        <div class="form-group col-md-1">
                            <label>
                                <input type="checkbox" id="proveedorCheckbox" name="proveedor"> Proveedor
                            </label>
                        </div>
                        <div class="form-group col-md-1">
                            <label>
                                <input type="checkbox" id="evaluadorCheckbox" name="evaluador"> Evaluador
                            </label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    {/if}
    
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-book"></i> Tutoriales
            </div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-bordered table-hover" id="tutoriales_table">
                <thead>
                    <tr>
                        {if isAdmin()}
                            <th style="width: 150px;"> Nombre </th>
                            <th> Descripción </th>
                            <th> Link </th>
                            <th style="width: 340px;"> Permisos </th>
                            <th style="width: 130px;"> Acciones </th>
                        {else}
                            <th style="width: 400px;"> Nombre </th>
                            <th> Descripción </th>
                            <th style="width: 100px;"> Link </th>
                        {/if}
                    </tr>
                </thead>
                {if isAdmin()}
                    <tbody data-bind="dataTablesForEach : { data: List, options: { paging: true }}">
                        <tr data-bind="attr: { id: 'row-' + Id }">
                            <td><input type="text" class="form-control" data-bind="value: Nombre"></td>
                            <td><input type="text" class="form-control" data-bind="value: Descripcion"></td>
                            <td><input type="text" class="form-control" data-bind="value: Link"></td>
                            <td>
                                <label>
                                    <input type="checkbox" id="clienteEdit" name="cliente" data-bind="checked: Permisos[0] == '1'"> Cliente
                                </label>
                                <label>
                                    <input type="checkbox" id="tecnicoEdit" name="tecnico" data-bind="checked: Permisos[1] == '1'"> Técnico
                                </label>
                                <label>
                                    <input type="checkbox" id="visorEdit" name="visor" data-bind="checked: Permisos[2] == '1'"> Visor
                                </label>
                                <label>
                                    <input type="checkbox" id="proveedorEdit" name="proveedor" data-bind="checked: Permisos[3] == '1'"> Proveedor
                                </label>
                                <label>
                                    <input type="checkbox" id="evaluadorEdit" name="evaluador" data-bind="checked: Permisos[4] == '1'"> Evaluador
                                </label>
                            </td>
                            <td>
                                <a data-bind="click: $root.Editar.bind($data, Id, Nombre, Descripcion, Link)" class="btn btn-xs green" title="Editar">
                                    Guardar <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                </a>
                                <a data-bind="click: $root.Eliminar.bind($data, Id)" class="btn btn-xs red" title="Eliminar">
                                    Eliminar <i class="fa fa-trash-o" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                {else}
                    <tbody data-bind="dataTablesForEach : { data: List, options: { paging: true }}">
                        <tr>
                            <td data-bind="text: Nombre"></td>
                            <td data-bind="text: Descripcion"></td>
                            <td><a data-bind="attr: { href: Link }, text: 'Ver'" target="_blank"></a></td>
                        </tr>
                    </tbody>
                {/if}
            </table>
        </div>
    </div>
{/block}



<!-- KNOCKOUT JS -->
{block 'knockout' append}
    <script type="text/javascript">
        var Tutoriales = function(data){
            var self = this;

            this.List = ko.observableArray(data.list);
            this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

            this.Agregar = function() {
                var tutorialName = $('#tutorialName').val();
                var tutorialDescription = $('#tutorialDescription').val();
                var tutorialLink = $('#tutorialLink').val();

                var clienteChecked = $('#clienteCheckbox').is(':checked') ? '1' : '0';
                var tecnicoChecked = $('#tecnicoCheckbox').is(':checked') ? '1' : '0';
                var visorChecked = $('#visorCheckbox').is(':checked') ? '1' : '0';
                var proveedorChecked = $('#proveedorCheckbox').is(':checked') ? '1' : '0';
                var evaluadorChecked = $('#evaluadorCheckbox').is(':checked') ? '1' : '0';

                var permisos = clienteChecked + tecnicoChecked + visorChecked + proveedorChecked + evaluadorChecked;

                if (!tutorialName || !tutorialDescription || !tutorialLink) {
                    swal('Error', 'Debe completar todos los campos', 'error');
                    return;
                }

                if (tutorialName && tutorialDescription && tutorialLink) {
                    $.blockUI();
                    var data = {
                        UserToken: User.Token,
                        Nombre: tutorialName,
                        Descripcion: tutorialDescription,
                        Link: tutorialLink,
                        Permisos: permisos
                    };
                    var url = '/tutoriales/new';
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
                                            window.location.href = response.data.redirect;
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
                } else {
                    swal('Error', 'Debe completar todos los campos', 'error');
                }
            };


            this.Editar = function(id, nombre, descripcion, link) {
                var row = document.getElementById('row-' + id);
                var checkboxes = row.querySelectorAll('input[type="checkbox"]');
                var permisos = '';

                checkboxes.forEach(function(checkbox) {
                    permisos += checkbox.checked ? '1' : '0';
                });

                swal({
                    title: '¿Está seguro de que desea editar el tutorial?',
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
                            UserToken: User.Token,
                            Id: id,
                            Nombre: nombre,
                            Descripcion: descripcion,
                            Link: link,
                            Permisos: permisos
                        };
                        var url = '/tutoriales/edit';
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
                                                window.location.href = response.data.redirect;
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

            this.Eliminar = function(id) {
                swal({
                    title: '¿Está seguro de que desea eliminar el tutorial?',
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
                            UserToken: User.Token,
                            Id: id  
                        };
                        var url = '/tutoriales/delete';
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
                                                window.location.href = response.data.redirect;
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
            var url = '/tutoriales/list';
            Services.Get(url, {
                    UserToken: User.Token
                },
                function(response) {
                    if (response.success) {
                        window.E = new Tutoriales(response.data);
                        AppOptus.Bind(E);
                    } else {
                        swal('Error', response.message, 'error');
                    }
                    $.unblockUI();
                },
                function(error) {
                    swal('Error', error.message, 'error');
                    $.unblockUI();
                }
            );
        });

        // Chrome allows you to debug it thanks to this
        {chromeDebugString('dynamicScript')}
    </script>
{/block}
