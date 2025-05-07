{extends 'empresas/materiales/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/jquery-ui/jquery-ui.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery-inputmask/inputmask/inputmask.date.extensions.min.js')}" type="text/javascript"></script>        
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
    <script>
        Inputmask.extendAliases({
            monto: {
                      radixPoint:".", 
                      alias: "numeric",
                      placeholder: "0",
                      autoGroup: false,
                      digits: 2,
                      digitsOptional: false,
                      clearMaskOnLostFocus: true
                  }
          });
    </script>    
{/block}

<!-- VISTA -->
{block 'material-edit'}
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

                    <div class="col-md-4">
                        <div class="form-group required" data-bind="validationElement: Entity.Categoria">
                            <label 
                                class="control-label visible-ie8 visible-ie9" 
                                style="display: block;">
                                Categorías
                            </label>
                            <div  class="selectRequerido">                            
                                <select 
                                    data-bind="
                                        value: Entity.Categoria, 
                                        valueAllowUnset: true, 
                                        options: Categorias, 
                                        optionsText: 'nombre', 
                                        optionsValue: 'id', 
                                        select2: { placeholder: 'Seleccionar...' }">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label 
                                class="control-label visible-ie8 visible-ie9" 
                                style="display: block;">
                                Unidades
                            </label>
                            <div>                            
                                <select 
                                    data-bind="
                                        value: Entity.Unidad, 
                                        valueAllowUnset: true, 
                                        options: Unidades, 
                                        optionsText: 'text', 
                                        optionsValue: 'id', 
                                        select2: { placeholder: 'Seleccionar...' }">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label 
                                class="control-label visible-ie8 visible-ie9" 
                                style="display: block;">
                                Costo objetivo
                            </label>
                            <div>             
                                <input 
                                    class="form-control placeholder-no-fix"
                                    type="text" 
                                    data-bind="inputmask: { 
                                        value: Entity.TargetCost,
                                        alias: 'monto'
                                    }" />                                
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Código ERP
                            </label>
                            <input 
                                class="form-control placeholder-no-fix" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                name="CodigoERP" 
                                id="CodigoERP" 
                                data-bind="value: Entity.CodigoERP" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group required" data-bind="validationElement: Entity.Descripcion">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Nombre
                            </label>
                            <input 
                                class="form-control placeholder-no-fix" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                name="Descripcion" 
                                id="Descripcion" 
                                data-bind="value: Entity.Descripcion" />
                        </div>
                    </div>
                    <!--
                    <div class="col-md-12">
                        <div class="form-group required">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Descripción
                            </label>
                            <input 
                                class="form-control placeholder-no-fix" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                name="DescripcionLarga" 
                                id="DescripcionLarga" 
                                data-bind="value: Entity.DescripcionLarga" />
                        </div>
                    </div>           
                    -->
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Descripción
                            </label>
                            <textarea 
                                class="form-control placeholder-no-fix" 
                                maxlength="1000" 
                                name="DescripcionLarga" 
                                id="DescripcionLarga" 
                                data-bind="value: Entity.DescripcionLarga, attr: { 'placeholder':'Máximo 1000 caracteres' }">
                            </textarea>            
                        </div>
                    </div>                               

                    <div class="col-md-12">
                        <div class="form-group">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Código Proveedor
                            </label>
                            <input 
                                class="form-control placeholder-no-fix" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                name="CodigoProveedor" 
                                id="CodigoProveedor" 
                                data-bind="value: Entity.CodigoProveedor" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Proveedor
                            </label>
                            <input 
                                class="form-control placeholder-no-fix" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                name="Proveedor" 
                                id="Proveedor" 
                                data-bind="value: Entity.Proveedor" />
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Marcar como eliminado
                            </label>
                            <input 
                                type="checkbox" 
                                data-on-color="success" 
                                data-off-color="danger" 
                                data-size="mini" 
                                data-on-text="SI" 
                                data-off-text="NO"
                                data-bind="bootstrapSwitchOn: Entity.Eliminado"
                                />
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
            href="/materiales" 
            type="button" 
            class="btn default">
            Volver a Listado
        </a>

        <button 
            type="button" 
            class="btn sbold btn-primary" 
            data-bind="click: Save, disable: IsdisableSave">
            Guardar Datos
        </button>
    </div>
</div>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
<script type="text/javascript">

ko.validation.locale('es-ES');
ko.validation.init({
    insertMessages: false,
    messagesOnModified: false,
    decorateElement: false,
    errorElementClass: 'wrong-field'
}, false);

var Materiales = function (data) {
    var self = this;
    this.Categorias = ko.observable(data.list.Categorias);
    this.Unidades = ko.observable(data.list.Unidades);

    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

    this.Entity = {
        Id: ko.observable(),
        Categoria: ko.observable('').extend({ required: true }),
        CodigoERP: ko.observable(),
        Descripcion: ko.observable().extend({ required: true }),
        DescripcionLarga: ko.observable(),
        TargetCost: ko.observable(),
        Unidad: ko.observable(),
        CodigoProveedor: ko.observable(),
        Proveedor: ko.observable(),
        Eliminado: ko.observable('')
    };

    this.setEntity = function (data) {
        self.Entity.Id(data.list.Id);
        self.Entity.Categoria(data.list.Categoria);
        self.Entity.CodigoERP(data.list.CodigoERP);
        self.Entity.Descripcion(data.list.Descripcion);
        self.Entity.DescripcionLarga(data.list.DescripcionLarga);
        self.Entity.TargetCost(data.list.TargetCost);
        self.Entity.Unidad(data.list.Unidad);
        self.Entity.CodigoProveedor(data.list.CodigoProveedor);
        self.Entity.Proveedor(data.list.Proveedor);
        self.Entity.Eliminado(data.list.Eliminado);
    };
    self.setEntity(data);

    this.validarform = function () {
        return (
                this.Entity.Descripcion.isValid()
            );
    };
    
    self.isValid = ko.computed(function () {
            return ko.validation.group(
                self,
                {
                    observable: true,
                    deep: true
                }).showAllMessages(true);
        }, self);

    self.IsdisableSave = ko.computed(function () {
        return !self.validarform();
    });

    this.Save = function () {
        //$.blockUI();
        var url = '/materiales/save';
        switch (params[1]) {
            case 'edicion':
                url += '/' + params[2];
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
                        } else {
                            window.location.reload();
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
    var url = '/materiales/';
    switch (params[1]) {
        case 'nuevo':
            url += 'nuevo/data';
            break;
        case 'edicion':
            url += 'edicion/data/' + params[2];
            break;
    }

    //console.log('url = ', url);
    //console.log('params[1] = ', params[1]);
    //console.log('params[2] = ', params[2]);

    Services.Get(url, data, 
        (response) => {
            if (response.success) {
                console.log('response.data', response.data);
                window.E = new Materiales(response.data);
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