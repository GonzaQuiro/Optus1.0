<!-- DOCUMENTACIÓN: CONDUCTOR -->
<div class="caption margin-bottom-20">
    <span class="caption-subject bold uppercase">
        Documentación para el conductor
    </span>
</div>

<!-- Documentación GCG -->
<div class="row">
    <div class="col-md-9">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Seleccione los documentos a requerir</label>
            <select 
                data-bind="selectedOptions: Entity.DriverDocumentsSelected, valueAllowUnset: true, options: Entity.DriverDocuments, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...', multiple: true }, disable: ReadOnly()">
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group" data-bind="visible: Entity.AmountsVisible">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Suma asegurada Accid Personales</label>
            <select 
                data-bind="value: Entity.AmountSelect, valueAllowUnset: true, options: Entity.Amount, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...' }, disable: ReadOnly()">
            </select>
        </div>
        <div class="form-group" data-bind="visible: Entity.AmountsVisible">
            <span>Asistencia Medica Farmacéutica (10%)</span>
            <input type="text" class="form-control placeholder-no-fix" data-bind="value : Entity.AmountsRatio" disabled />
        </div>
    </div>
</div>

<!-- Documentación NO-GCG -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="mt-checkbox">
                Clásusa de beneficiario Seg Acc Pers
                <input type="checkbox" name="clausula_beneficiario" data-bind="checked: Entity.ClausulaBeneficiario, disable: ReadOnly()">
                <span></span>
            </label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group" data-bind="visible: Entity.ClausulaBeneficiario">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Ingrese Cuit</label>
            <input class="form-control placeholder-no-fix" type="text" id="cuit_beneficiario" data-bind="value: Entity.CuitBeneficiario, disable: ReadOnly()" />
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group" data-bind="visible: Entity.ClausulaBeneficiario">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Ingrese Razon Social</label>
            <input class="form-control placeholder-no-fix" type="text" id="razon_social_beneficiario" data-bind="value: Entity.RazonSocialBeneficiario, disable: ReadOnly()" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="mt-checkbox">
                Cláusula de NO repetición de ART
                <input type="checkbox" name="clausula_art" data-bind="checked: Entity.ClausulaArt, disable: ReadOnly()">
                <span></span>
            </label>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group" data-bind="visible: Entity.ClausulaArt">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Ingrese Cuit</label>
            <input class="form-control placeholder-no-fix" type="text" name="CuitDoc" id="CuitDoc" data-bind="value: Entity.CuitDoc, disable: ReadOnly()" />
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group" data-bind="visible: Entity.ClausulaArt">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Ingrese Razon Social</label>
            <input class="form-control placeholder-no-fix" type="text" name="RazonSocialDoc" id="RazonSocialDoc"  data-bind="value: Entity.RazonSocialDoc, disable: ReadOnly()" />
        </div>
    </div>
</div>

<!-- Documentos Adicionales -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Otros Archivos Conductor</label>
            <input type="text" class="form-control input-large" data-bind="tagsinput: Entity.AdditionalDriverDocuments, tagsinputOptions: {
                maxTags: 3
            }">
        </div>
    </div>
</div>

<!-- DOCUMENTACIÓN: VEHÍCULO -->
<div class="portlet-title margin-bottom-20">
    <div class="caption">
        <span class="caption-subject bold uppercase">Documentación para vehiculo</span>
    </div>
</div>

<div class="row">
    <!-- Documentación GCG -->
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Seleccione los documentos a requerir</label>
            <select 
                data-bind="selectedOptions: Entity.VehicleDocumentsSelected, options: Entity.VehicleDocuments, valueAllowUnset: true, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...', multiple: true }, disable: ReadOnly()">
            </select>
        </div>
    </div>

    <!-- Documentos Adicionales -->
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Otros Archivos Vehículos</label>
            <input type="text" class="form-control input-large" data-bind="tagsinput: Entity.AdditionalVehicleDocuments, tagsinputOptions: {
                maxTags: 3
            }">
        </div>
    </div>
</div>