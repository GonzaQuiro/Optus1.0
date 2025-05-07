{extends 'empresas/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/jquery-ui/jquery-ui.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/icheck/skins/all.css')}" rel="stylesheet" type="text/css" />
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script>
        var IdUsuario = {$id};
        var TipoUsuario = '{$tipo}';
    </script>
    <script src="{asset('/global/scripts/knockout.plugins.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/jquery.pulsate.min.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
    <script src="{asset('/js/geo.js')}" type="text/javascript"></script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD3xU2zO42h1qL1s6bFkHsdhtv_hpvfxBo&callback=initMapEmpresaDetalle"></script>
{/block}

<!-- VISTA -->
{block 'company-detail'}
    <div class="row equal" style="margin-top: 25px;">
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">General</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
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
                                    <span 
                                        class="label label-md" 
                                        data-bind="text: EstadoDescripcion, css: { 
                                            'label-success': Estado() === 'active', 
                                            'label-warning': Estado() === 'inactive', 
                                            'label-danger': Estado() === 'blocked' 
                                        }">
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    Razón Social
                                </td>
                                <td data-bind="text: RazonSocial" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    CUIT
                                </td>
                                <td data-bind="text: Cuit" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">Contacto</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
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
                            <tr>
                                <td class="col-md-4 text-left uppercase vertical-align-middle">
                                    SitioWeb
                                </td>
                                <td data-bind="text: SitioWeb" class="col-md-4 text-right vertical-align-middle"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">Localización</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                    </div>
                </div>
                <div class="portlet-body form expandir-3" style="display: block;">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            País
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: Pais"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Provincia
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: Provincia"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Ciudad
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: Ciudad"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Dirección
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: Direccion"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Código Postal
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: CodigoPostal"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <div id="map-canvas-1" style="width: 100%; height: 406px; background: #ccc;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row equal">

        {if isAdmin()}
        <div class="col-md-6">
            <div class="portlet light bg-inverse">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <span class="caption-subject bold uppercase">Varios</span>
                    </div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                    </div>
                </div>
                <div class="portlet-body form expandir-5">
                    <table class="table">
                        <tbody>
                            {if $tipo eq 'oferentes'}
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Rubros
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <!-- ko foreach: Rubros -->
                                        <span 
                                            class="label label-success" 
                                            data-bind="text: $data">
                                        </span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Clientes Asociados
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <!-- ko foreach: Clientes -->
                                        <span 
                                            class="label label-success" 
                                            data-bind="text: $data">
                                        </span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Observaciones
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <span data-bind="text: Observaciones"></span>
                                    </td>
                                </tr>
                            {else}
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Sistema Tarifario
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <span class="label label-success" data-bind="text: SistemaTarifario"></span>
                                    </td>
                                </tr>
                            {/if}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {/if}

        {if $tipo eq 'oferentes'}
            <div class="col-md-6">
                <div class="portlet light bg-inverse">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <span class="caption-subject bold uppercase">Alcance de la prestación del servicio</span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form expandir-5">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Países
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <!-- ko foreach: Paises -->
                                        <span 
                                            class="label label-success" 
                                            data-bind="text: $data">
                                        </span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Provincias
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <!-- ko foreach: Provincias -->
                                        <span 
                                            class="label label-success" 
                                            data-bind="text: $data">
                                        </span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                                <tr>
                                    <td class="col-md-4 text-left uppercase vertical-align-middle">
                                        Ciudades
                                    </td>
                                    <td class="col-md-4 text-right vertical-align-middle">
                                        <!-- ko foreach: Ciudades -->
                                        <span 
                                            class="label label-success" 
                                            data-bind="text: $data">
                                        </span>
                                        <!-- /ko -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        {/if}
    </div>

    {if $tipo eq 'oferentes'}    
        <div class="row equal">
            <div class="col-md-12">
                <div class="portlet light bg-inverse">
                    <div class="portlet-title">
                        <div class="caption font-red-sunglo">
                            <span class="caption-subject bold uppercase">Negocio</span>
                        </div>
                        <div class="tools">
                            <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir"> </a>
                        </div>
                    </div>
                    <div class="portlet-body form expandir-6">
                        <table class="table">
                            <tbody>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Año de Fundación de la Empresa
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: FoundationYear"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Número de empleados
                                        </td>
                                        <td class="col-md-4 text-right vertical-align-middle">
                                            <span data-bind="text: NumberOfEmployees"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Ingresos anuales en dolares
                                        </td>
                                        <td data-bind="text: AnnualIncome" class="col-md-4 text-right vertical-align-middle">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Clasificación de la compañía
                                        </td>
                                        <td data-bind="text: CompanyClassification" class="col-md-4 text-right vertical-align-middle">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="col-md-4 text-left uppercase vertical-align-middle">
                                            Sector
                                        </td>
                                        <td data-bind="text: EconomicSector" class="col-md-4 text-right vertical-align-middle">
                                        </td>
                                    </tr>                                    
                            </tbody>
                        </table>                
                    </div>
                </div>
            </div>
        </div>
    {/if}

    <div class="row">
        <div class="col-md-12 text-right">
            <a 
                href="/empresas/{$tipo}" 
                type="button" 
                class="btn default">
                Volver a Listado
            </a>
        </div>
    </div>

{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
<script type="text/javascript">
var Empresa = function (data) {
    var self = this;

console.log(data.list);

    this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

    this.Tipo = ko.observable(data.list.Tipo);
    this.Id = ko.observable(data.list.Id);
    this.Estado = ko.observable(data.list.Estado);
    this.EstadoDescripcion = ko.observable(data.list.EstadoDescripcion);
    this.RazonSocial = ko.observable(data.list.RazonSocial);
    this.Cuit = ko.observable(data.list.Cuit);
    this.Pais = ko.observable(data.list.Pais);
    this.Provincia = ko.observable(data.list.Provincia);
    this.Ciudad = ko.observable(data.list.Ciudad);
    this.Direccion = ko.observable(data.list.Direccion);
    this.CodigoPostal = ko.observable(data.list.CodigoPostal);
    this.Latitud = ko.observable(data.list.Latitud);
    this.Longitud = ko.observable(data.list.Longitud);
    this.Nombre = ko.observable(data.list.Nombre);
    this.Apellido = ko.observable(data.list.Apellido);
    this.Telefono = ko.observable(data.list.Telefono);
    this.Celular = ko.observable(data.list.Celular);
    this.Email = ko.observable(data.list.Email);
    this.SitioWeb = ko.observable(data.list.SitioWeb);
    this.Observaciones = ko.observable(data.list.Observaciones);
    this.Rubros = ko.observableArray(data.list.Rubros);
    this.Paises = ko.observableArray(data.list.Paises);
    this.Provincias = ko.observableArray(data.list.Provincias);
    this.Ciudades = ko.observableArray(data.list.Ciudades);
    this.Clientes = ko.observableArray(data.list.Clientes);
    this.CodigoProveedor = ko.observable(data.list.CodigoProveedor);
    this.SistemaTarifario = ko.observable(data.list.SistemaTarifario);

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
    this.LogoPath = ko.observable(data.list.LogoPath);
    this.Certifications = ko.observable(data.list.Certifications);

};

jQuery(document).ready(function () {
    $.blockUI();

    var url = '/empresas/' + params[1] + '/' + params[2] + '/data/' + params[3];

    Services.Get(url, {
            UserToken: User.Token
        }, 
        (response) => {
            if(response.success) {
                window.E = new Empresa(response.data);
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