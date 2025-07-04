{extends 'usuarios/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/jquery-ui/jquery-ui.min.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'user-edit-permissions'}
<div class="row equal" data-bind="foreach: { data: PermissionGroups, as: 'group' }">
    
  <!-- ko ifnot: (
    ($root.TipoUsuario() === 5 && group.description() === 'Tarifas') ||
    ($root.TipoUsuario() === 8 && group.description() === 'Tarifas') ||
    ($root.TipoUsuario() === 7 && group.description() === 'Tarifas') ||
    ($root.TipoUsuario() === 3 && group.description() === 'Tarifas')
)-->

    
    <div class="col-md-4">
        <div class="portlet light bordered" style="min-height: 200px">
            <div class="portlet-body">
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-9 col-sm-9 col-md-9 tit">
                                <span class="text-bold" data-bind="text: group.description()"></span>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3">
                                <input 
                                    type="checkbox" 
                                    data-on-color="success" 
                                    data-off-color="danger" 
                                    data-size="mini" 
                                    data-on-text="SI" 
                                    data-off-text="NO"
                                    data-bind="bootstrapSwitchOn: group.active, onChangeCallback: $root.toggleGroup.bind($data, group)"
                                />
                            </div>

                            <!-- ko foreach: group.visiblePermissions -->
                            <div class="col-xs-offset-1 col-xs-8 col-sm-offset-1 col-sm-8 col-md-offset-1 col-md-8 tit">
                                <span data-bind="text: description()"></span>
                            </div>
                            <div class="col-xs-3 col-sm-3 col-md-3">
                                <input 
                                    type="checkbox" 
                                    data-on-color="success" 
                                    data-off-color="danger" 
                                    data-size="mini" 
                                    data-on-text="SI" 
                                    data-off-text="NO"
                                    data-bind="bootstrapSwitchOn: active" />
                            </div>
                            <!-- /ko -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /ko -->

</div>

<div class="row">
    <div class="col-md-12 text-right">
        <a 
            href="{$urlBack}" 
            type="button" 
            class="btn default">
            Volver a Listado
        </a>
        <button 
            type="button" 
            class="btn btn-primary" 
            data-bind="click: Save">
            Guardar Datos
        </button>
    </div>
</div>

<style>
    {literal}
        .container {
            width: 100%;
            padding-left: 0;
        }
        .tit {
            margin-bottom: 10px;
        }
    {/literal}
</style>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
<script type="text/javascript">
var Permission = function (data) {
    var self = this;
    this.id = ko.observable(data.id);
    this.description = ko.observable(data.text);
    this.active = ko.observable(false);
};

var PermissionGroup = function (data, permissionsSelected) {
    var self = this;

    this.id = ko.observable(data.id);
    this.description = ko.observable(data.text);
    this.permissions = ko.observableArray();
    this.active = ko.pureComputed({
        read: () => {
            return self.permissions().every(p => p.active());
        },
        write: (value) => { }
    });

    data.permissions.forEach(item => {
        var permission = new Permission(item);
        if (permissionsSelected.some(p => p === item.id)) {
            permission.active(true);
        }
        self.permissions.push(permission);
    });

    this.visiblePermissions = ko.pureComputed(() => {
         const permisosNoVisibles = ['Clientes', 'Unidades de Medida'];

         return self.permissions().filter(p => {
            return !permisosNoVisibles.includes(p.description());
        });
    });

};

var UsuariosPermisos = function (data) {
    var self = this;
    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

    this.Id = ko.observable(data.list.Id);
    this.FullName = ko.observable(data.list.FullName);
    this.PermissionGroups = ko.observableArray();
    this.TipoUsuario = ko.observable(data.list.TipoUsuario);

    if (data.list.PermissionGroups && data.list.PermissionGroups.length > 0) {
        data.list.PermissionGroups.forEach(item => {
            self.PermissionGroups.push(new PermissionGroup(item, data.list.PermissionsSelected));
        });
    } else {
        self.noPermissionsMessage = ko.observable("No hay grupos de permisos disponibles para este usuario.");
    }

    this.Save = function () {
        $.blockUI();
        var url = '/usuarios/edicion/' + params[2] + '/permisos/save';
        var data = {
            UserToken: User.Token,
            Data: JSON.stringify(ko.toJS(self.PermissionGroups()))
        };
        Services.Post(url, data, 
            (response) => {
                $.unblockUI();
                if (response.success) {
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
                            window.location = response.data.redirect;
                        }
                    });
                } else {
                    swal('Error', response.message, 'error');
                }
            }, 
            (error) => {
                $.unblockUI();
                swal('Error', error.message, 'error');
            }
        );
    };

    this.toggleGroup = function (group, newValue, oldValue) {
        if (newValue !== oldValue) {
            group.permissions().forEach(permission => {
                permission.active(newValue);
            });
        }
    };
};

jQuery(document).ready(function () {
    $.blockUI();
    var data = {
        UserToken: User.Token
    };
    var url = '/usuarios/edicion/' + params[2] + '/permisos/data';
    Services.Get(url, data, 
        (response) => {
            if (response.success) {
                window.E = new UsuariosPermisos(response.data);
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
