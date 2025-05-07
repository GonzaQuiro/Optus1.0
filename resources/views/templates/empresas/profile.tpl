{extends 'empresas/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/icheck/skins/all.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-fileinput-kartik/css/fileinput.min.css')}" rel="stylesheet" type="text/css" />    
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script>
        var IdUsuario = {$id};
        var TipoUsuario = '{$tipo}';
    </script>
    <script src="{asset('/global/scripts/knockout.plugins.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery.pulsate.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput-kartik/js/fileinput.min.js')}"></script>
    <script src="{asset('/global/plugins/bootstrap-fileinput-kartik/js/locales/es.js')}"></script>    
    <script src="{asset('/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery-inputmask/inputmask/inputmask.date.extensions.min.js')}" type="text/javascript"></script>    
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
    <script src="{asset('/js/geo.js')}" type="text/javascript"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3xU2zO42h1qL1s6bFkHsdhtv_hpvfxBo&callback=initMapEmpresa"></script>
    <script src="{asset('/global/plugins/icheck/icheck.min.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/form-icheck.min.js')}" type="text/javascript"></script>
    
    <script>
        /*
        function validarMinMax(e) {
            if (Number.isNaN(parseInt(e.value, 10)))
                e.value = parseInt(e.min, 10);
        	else if (parseInt(e.value, 10) > parseInt(e.max, 10)) 
                e.value = parseInt(e.max, 10);
            else if (parseInt(e.value, 10) < parseInt(e.min, 10)) 
                e.value = parseInt(e.min, 10);
                //e.value|=0;
        }
        */

        Inputmask.extendAliases({
            monto: {
                      //prefix: "₱ ",
                      //prefix: "$ ",
                      //groupSeparator: "",
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
{block 'company-profile-edit'}
<div class="row">
    <div class="col-md-12 ">
        <div class="portlet light bg-inverse">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">Información de negocios</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                </div>
            </div>
            <div class="portlet-body form expandir-1">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Año de Fundación de la Empresa</label>
                            <input class="form-control placeholder-no-fix" type="text" name="" id="" 
                                data-bind="inputmask: { 
                                    value: Entity.FoundationYear, 
                                    mask: '\\Año y', 
                                    clearIncomplete: true,
                                    placeholder: 'X', 
                                    autoUnmask: true,
                                    allowPlus: false,
                                    allowMinus: false,
                                    clearMaskOnLostFocus: true
                                }" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Número de empleados</label>
                            <!--
                            <input class="form-control placeholder-no-fix" type="text" name="" id="" 
                                data-bind="inputmask: { 
                                    value: Entity.NumberOfEmployees,
                                    mask: '[9999]', 
                                    showMaskOnHover: true,
                                    showMaskOnFocus: true,
                                    clearIncomplete: true,
                                    placeholder: '', 
                                    allowPlus: false,
                                    allowMinus: false,
                                    rightAlign: true,
                                    clearMaskOnLostFocus: true
                                }" />
                            -->
                            <select
                                data-bind="
                                    value: Entity.NumberOfEmployees, 
                                    valueAllowUnset: true, 
                                    options: Entity.OptionsNumberOfEmployees, 
                                    optionsText: 'text', 
                                    optionsValue: 'id', select2: { placeholder: 'Seleccionar...' }">
                            </select>                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Ingresos anuales en dolares</label>
                            <!--
                            <input class="form-control placeholder-no-fix" type="text" name="" id="" 
                                data-bind="inputmask: { 
                                    value: Entity.AnnualIncome,
                                    alias: 'monto'
                                }" />
                            -->
                            <select
                                data-bind="
                                    value: Entity.AnnualIncome, 
                                    valueAllowUnset: true, 
                                    options: Entity.OptionsAnnualIncome, 
                                    optionsText: 'text', 
                                    optionsValue: 'id', select2: { placeholder: 'Seleccionar...' }">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="portlet light bg-inverse">            
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">Marketing</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                </div>
            </div>
            <div class="portlet-body form expandir-2">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">facebook.com/</label>
                            <input data-bind="value: Entity.FacebookAccount" class="form-control placeholder-no-fix" type="text" name="FacebookAccount" id="FacebookAccount" maxlength="200"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">twitter.com/</label>
                            <input data-bind="value: Entity.TwitterAccount" class="form-control placeholder-no-fix" type="text" name="TwitterAccount" id="TwitterAccount" maxlength="200"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">linkedin.com/</label>
                            <input data-bind="value: Entity.LinkedinAccount" class="form-control placeholder-no-fix" type="text" name="LinkedinAccount" id="LinkedinAccount" maxlength="200"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Descripción de la compañía</label>
                            <textarea 
                                class="form-control placeholder-no-fix" 
                                rows="8" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                maxlength="1000"
                                name="CompanyDescription" 
                                id="CompanyDescription" 
                                data-bind="value: Entity.CompanyDescription">                            
                            </textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row" style="margin: 0; padding: 0;">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label visible-ie8 visible-ie9" style="display: block;">Imagen / Logo</label>
                                    <input id="input-700" data-bind="fileinput: Entity.Logo, fileinputOptions: {
                                        uploadUrl: '/media/file/upload',
                                        initialCaption: Entity.Logo().filename() ? Entity.Logo().filename() : [],
                                        uploadExtraData: {
                                            UserToken: User.Token,
                                            path: Entity.LogoPath(),
                                        },
                                        initialPreview: Entity.Logo().filename() ? [Entity.LogoPath() + Entity.Logo().filename()] : [],
                                        allowedFileExtensions: ['jpg', 'jpeg', 'png']
                                    }"
                                    name="file[]" type="file">

                                    <!-- ko if: Entity.Logo().filename() -->
                                    <div class="text-center">
                                        <img 
                                            class="img-thumbnail img-responsive" 
                                            style="max-height: 150px;" 
                                            data-bind="attr:{literal}{src: Entity.LogoPath() + Entity.Logo().filename()}{/literal}">
                                    </div>
                                    <!-- /ko -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                    
                <div class="row">                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Clasificación de la compañía</label>
                            <select
                                data-bind="
                                    value: Entity.CompanyClassification, 
                                    valueAllowUnset: true, 
                                    options: Entity.OptionsClassification, 
                                    optionsText: 'text', 
                                    optionsValue: 'id', select2: { placeholder: 'Seleccionar...' }">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Sector</label>
                            <select
                                data-bind="
                                    value: Entity.EconomicSector, 
                                    valueAllowUnset: true, 
                                    options: Entity.OptionsEconomicSector, 
                                    optionsText: 'text', 
                                    optionsValue: 'id', select2: { placeholder: 'Seleccionar...' }">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>            
        <div class="portlet light bg-inverse">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <span class="caption-subject bold uppercase">Certificaciones</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                </div>
            </div>
            <div class="portlet-body form expandir-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea 
                                class="form-control placeholder-no-fix" 
                                rows="5" 
                                type="text" 
                                autocomplete="off" 
                                placeholder="" 
                                maxlength="1000"
                                name="Certifications" 
                                id="Certifications" 
                                data-bind="value: Entity.Certifications">
                            </textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="form-group pull-right">
            <button 
                type="button" 
                class="btn sbold btn-primary" 
                data-bind="click: Save, disable: IsdisableSave">
                Guardar Datos
            </button>
        </div>
        <div class="form-group pull-right">
            <a 
                href="/dashboard" 
                type="button" 
                class="btn default sbold">
                Volver al inicio
            </a>
        </div>
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

var Form = function (data) {
    var self = this;

    this.Id = ko.observable(data.list.Id);
    this.Tipo = ko.observable(data.list.Tipo);
    
    this.Estados = ko.observable(data.list.Estados);
    this.Estado = ko.observable(data.list.Estado),
    this.RazonSocial = ko.observable(data.list.RazonSocial);
    this.Cuit = ko.observable(data.list.Cuit);
    
    this.Pais = ko.observable(data.list.Pais);
    this.Provincia = ko.observable(data.list.Provincia);
    this.Localidad = ko.observable(data.list.Localidad);
    this.Direccion = ko.observable(data.list.Direccion);
    this.Cp = ko.observable(data.list.Cp);
    this.Latitud = ko.observable(data.list.Latitud);
    this.Longitud = ko.observable(data.list.Longitud);
    
    this.Nombre = ko.observable(data.list.Nombre);
    this.Apellido = ko.observable(data.list.Apellido);
    this.Telefono = ko.observable(data.list.Telefono);
    this.Celular = ko.observable(data.list.Celular);
    this.Email = ko.observable(data.list.Email);
    this.SitioWeb = ko.observable(data.list.SitioWeb);
    
    this.RubrosSelected = ko.observableArray([]);

    this.PaisesSelected = ko.observableArray([]);
    this.ProvinciasSelected = ko.observableArray([]);
    this.CiudadesSelected = ko.observableArray([]);
    
    this.ClienteAsociado = ko.observableArray([]);
    
    this.CodigoProveedor = ko.observable(data.list.CodigoProveedor);


    this.FoundationYear = ko.observable(data.list.FoundationYear);
    this.OptionsNumberOfEmployees = ko.observable(data.list.OptionsNumberOfEmployees);
    this.NumberOfEmployees = ko.observable(data.list.NumberOfEmployees);
    this.OptionsAnnualIncome = ko.observable(data.list.OptionsAnnualIncome);
    this.AnnualIncome = ko.observable(data.list.AnnualIncome);
    this.FacebookAccount = ko.observable(data.list.FacebookAccount);
    this.TwitterAccount = ko.observable(data.list.TwitterAccount);
    this.LinkedinAccount = ko.observable(data.list.LinkedinAccount);
    this.CompanyDescription = ko.observable(data.list.CompanyDescription);
    this.OptionsClassification = ko.observable(data.list.OptionsClassification);
    this.CompanyClassification = ko.observable(data.list.CompanyClassification);
    this.OptionsEconomicSector = ko.observable(data.list.OptionsEconomicSector);
    this.EconomicSector = ko.observable(data.list.EconomicSector);
    this.CompanyLogo = ko.observable(data.list.CompanyLogo);
    this.Logo = ko.observable(new Logo());
    if (data.list.CompanyLogo) {
        self.Logo(new Logo(data.list.CompanyLogo));
    }

    this.LogoPath = ko.observable(data.list.LogoPath);
    this.Certifications = ko.observable(data.list.Certifications);
    
    this.Tarifario = ko.observable(data.list.Tarifario);
    this.Observaciones = ko.observable(data.list.Observaciones);

}

var Empresa = function (data) {
    var self = this;

    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);
    this.Rubros = ko.observableArray(data.list.Rubros);
    this.Tarifarios = ko.observableArray(data.list.Tarifarios);
    this.ClientesAsociados = ko.observableArray(data.list.ClientesAsociados);

    this.Paises = ko.observableArray(data.list.Paises);
    this.Provincias = ko.observableArray([]);
    this.Ciudades = ko.observableArray([]);
    
    this.Entity = new Form(data);

    self.isValid = ko.computed(function () {
            return ko.validation.group(
                self,
                {
                    observable: true,
                    deep: true
                }).showAllMessages(true);
        }, self);

    if (params[1] == 'oferentes') {
        this.FirstTimePaisesSelected = true;
        self.Entity.PaisesSelected.subscribe((Countries) => {
            $.blockUI();

            if (Countries.length > 0) {
                var url = '/lists/provinces';
                var body = {
                    UserToken: User.Token,
                    Countries: Countries
                };
                Services.Get(url, body,
                    (response) => {
                        $.unblockUI();
                        self.Provincias(response.data.list);
                        if (self.FirstTimePaisesSelected) {
                            self.Entity.ProvinciasSelected(data.list.ProvinciasSelected);
                            self.FirstTimePaisesSelected = false;
                        }
                    },
                    (error) => {
                        $.unblockUI();
                    },
                    null,
                    null
                );
            } else {
                $.unblockUI();
                self.Entity.ProvinciasSelected([]);
                self.Provincias([]);
            }
        });

        this.FirstTimeProvinciasSelected = true;
        self.Entity.ProvinciasSelected.subscribe((Provinces) => {
            $.blockUI();
            if (Provinces.length > 0) {
                var url = '/lists/cities';
                var body = {
                    UserToken: User.Token,
                    Provinces: Provinces
                };
                Services.Get(url, body,
                    (response) => {
                        $.unblockUI();
                        self.Ciudades(response.data.list);
                        if (self.FirstTimeProvinciasSelected) {
                            self.Entity.CiudadesSelected(data.list.CiudadesSelected);
                            self.FirstTimeProvinciasSelected = false;
                        }
                    },
                    (error) => {
                        $.unblockUI();
                    },
                    null,
                    null
                );
            } else {
                $.unblockUI();
                self.Entity.CiudadesSelected([]);
                self.Ciudades([]);
            }
        });

    }

    // Inicializamos los observables múltiples.
    setTimeout(() => {
        self.Entity.RubrosSelected(data.list.RubrosSelected);
        self.Entity.ClienteAsociado(data.list.ClienteAsociado);
        self.Entity.PaisesSelected(data.list.PaisesSelected);
    }, 1000);

    this.validarform = function () {
        if (params[1] == 'oferentes') { 
            return (
                self.Entity.Estado.isValid() &&
                self.Entity.RazonSocial.isValid() &&
                self.Entity.Cuit.isValid() &&
                self.Entity.Nombre.isValid() &&
                self.Entity.Apellido.isValid() &&
                self.Entity.Email.isValid()
                );
        } else {
            return (
                self.Entity.Estado.isValid() &&
                self.Entity.RazonSocial.isValid() &&
                self.Entity.Cuit.isValid() &&
                self.Entity.Nombre.isValid() &&
                self.Entity.Apellido.isValid() &&
                self.Entity.Email.isValid() &&
                self.Entity.Tarifario.isValid()
                );
        }
    };

    self.IsdisableSave = ko.computed(function () {
        return !self.validarform();
    });

    this.Save = function () {
        if (!self.validarform()) {
            swal('Alerta!', 'Por favor complete los campos obligatorios.', 'error');
            return false;
        }

        $.blockUI();
        var url = '/empresas/' + params[1] + '/save';
        switch (params[2]) {
            case 'edicion':
                url += '/' + params[3];
                break;
        }

        var url = '/empresas/' + TipoUsuario + '/save/' + self.Entity.Id();

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
                        if (response.data.redirect)
                            window.location = '/';
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

var Logo = function (filename = null) {
    var self = this;
    this.filename = ko.observable(filename ? filename : null);
    this.action = ko.observable(null);
}

jQuery(document).ready(function () {
    $.blockUI();
    var url = '/empresas/' + TipoUsuario + '/edicion/dataPerfil/' + IdUsuario;
    Services.Get(url, { UserToken: User.Token }, 
        (response) => {
            $.unblockUI();
            if (response.success) {
                window.E = new Empresa(response.data);
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