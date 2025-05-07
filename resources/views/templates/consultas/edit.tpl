{extends 'consultas/main.tpl'}

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
{block 'consulta-edit'}
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
                        <div class="form-group required" data-bind="validationElement: Entity.Consulta">
                            <label 
                                class="control-label visible-ie8 visible-ie9" style="display: block;">
                                Consulta
                            </label>
                            <textarea 
                                class="form-control placeholder-no-fix" 
                                rows="5" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                name="Consulta" 
                                id="Consulta" 
                                data-bind="value: Entity.Consulta" />
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
            href="/dashboard" 
            type="button" 
            class="btn default sbold">
            Volver al inicio
        </a>
        <button 
            type="button" 
            class="btn sbold btn-primary" 
            data-bind="click: Save, disable: IsdisableSave">
            Enviar consulta
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

//var Consultas = function (data) {
var Consultas = function () {    
    var self = this;

    this.Consulta = ko.observable('');
    this.Breadcrumbs = ko.observableArray('');

    this.Entity = {
        Consulta: ko.observable('').extend({ required: true })
    };

    this.setEntity = function () {
        self.Entity.Consulta('');
    };
    self.setEntity();

    this.validarform = function () {
        return (
                this.Entity.Consulta.isValid()
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
        $.blockUI();
        var url = '/consultas/send';
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
        window.E = new Consultas();
        AppOptus.Bind(E);
    $.unblockUI();
});


// Chrome allows you to debug it thanks to this
{chromeDebugString('dynamicScript')}
</script>
{/block}