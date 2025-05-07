{extends 'rubros/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'sub-area-edit'}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light bg-inverse">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">General</span>
                </div>
            </div>
            <div class="portlet-body form expandir-1" style="display: block;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Nombre</label>
                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="" name="nombre" id="nombre" data-bind="value: Entity.Nombre" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 text-right">
        <a 
            data-bind="attr: {literal}{ href: '/configuraciones/rubros/' + CategoryId() + '/subrubros' }{/literal}"
            type="button" 
            class="btn default">
            Volver
        </a>

        <button 
            type="button" 
            class="btn sbold btn-primary" 
            data-bind="click: Save">
            Guardar
        </button>
    </div>
</div>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
<script type="text/javascript">
var ConfiguracionesSubRubro = function (data) {
    var self = this;

    this.Id = ko.observable(data.list.Id);
    this.Nombre = ko.observable(data.list.Nombre);
    this.CategoryId = ko.observable(data.list.CategoryId);
    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

    this.Entity = {
        Id: ko.observable(''),
        Nombre: ko.observable(''),
    };

    this.setEntity = function (data) {
        self.Entity.Id(data.list.Id);
        self.Entity.Nombre(data.list.Nombre);
    };
    self.setEntity(data);

    this.Save = function () {
        $.blockUI();
        var url = '/configuraciones/rubros/' + params[2] + '/subrubros/save';
        switch (params[4]) {
            case 'edicion':
                url += '/' + params[5];
                break;
        }
        var data = {
            UserToken: User.Token,
            Data: JSON.stringify(ko.toJS(self.Entity))
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
};

jQuery(document).ready(function () {
    $.blockUI();
    
    var data = {
        UserToken: User.Token
    };

    var url = '/configuraciones/rubros/' + params[2] + '/subrubros/';
    switch (params[4]) {
        case 'nuevo':
            url += 'nuevo/data';
            break;
        case 'edicion':
            url += 'edicion/data/' + params[5];
            break;
    }
    Services.Get(url, data, 
        (response) => {
            if (response.success) {
                window.E = new ConfiguracionesSubRubro(response.data);
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