<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="mt-checkbox">
                <input type="checkbox" name="cotizar_seguro" data-bind="checked: Entity.CotizarSeguro, disable: ReadOnly()">
                ¿Desea cotizar seguro de carga?
                <span></span>
            </label>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Suma Asegurada</label>
            <input class="form-control placeholder-no-fix" type="text" maxlength="11" id="suma_asegurada" data-bind="value: Entity.SumaAsegurada, disable: ReadOnly() || !Entity.CotizarSeguro()" />
        </div>

        <div class="form-group">
            <label class="mt-checkbox">
                <input type="checkbox" name="cotizar_armada" data-bind="checked: Entity.CotizarArmada, disable: ReadOnly()">
                ¿Desea cotizar custodia armada?
                <span></span>
            </label>
        </div>
    </div>
</div>