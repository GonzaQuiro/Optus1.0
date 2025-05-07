<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Medio de Pago</label>
            <select 
                data-bind="value: Entity.PaymentMethod, valueAllowUnset: true, options: Entity.PaymentMethods, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...' }, disable: ReadOnly()">
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Plazo de Paga (dias)</label>
            <input class="form-control placeholder-no-fix" type="number" name="PlazoPago" id="PlazoPago" data-bind="value: Entity.PlazoPago, disable: ReadOnly()" />
        </div>
    </div>
</div>