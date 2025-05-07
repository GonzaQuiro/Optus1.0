<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">
                Fecha Hasta
            </label>
            <div class="input-group date form_datetime bs-datetime">
                <input 
                    class="form-control" 
                    size="16" 
                    type="text" 
                    data-bind="dateTimePicker: Entity.FechaHasta, dateTimePickerOptions: {
                        format: 'dd-mm-yyyy hh:ii',
                        momentFormat: 'DD-MM-YYYY HH:mm',
                        startDate: new Date(),
                        todayBtn: true
                    },
                    disable: ReadOnly()">
                <span class="input-group-addon">
                    <button class="btn default date-set" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="tabbable-custom nav-justified">
            <ul class="nav nav-tabs nav-justified">
                <li class="active">
                    <a href="#tab_1" data-toggle="tab">Lugar de Destino</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Nombre Establecimiento Destino</label>
                        <input class="form-control placeholder-no-fix" type="text" name="NombreHasta" id="NombreHasta" data-bind="value: Entity.NombreHasta, disable: ReadOnly()" />
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Provincia</label>
                        <select 
                            data-bind="value: 
                                Entity.ProvinciaHastaSelect, 
                                options: ProvinciasHasta,
                                valueAllowUnset: true, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...' }, disable: ReadOnly()">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Ciudad</label>
                        <select 
                            data-bind="value: 
                                Entity.CiudadHastaSelect, 
                                valueAllowUnset: true, 
                                options: CiudadesHasta, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...' }, disable: ReadOnly() || !Entity.ProvinciaHastaSelect()">
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Calle</label>
                        <input type="text" class="form-control" id="CalleHasta" name="CalleHasta" data-bind="value: Entity.CalleHasta, disable: ReadOnly() || !Entity.CiudadHastaSelect()">
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Numeración</label>
                        <input type="text" class="form-control" id="NumeracionHasta" name="NumeracionHasta" data-bind="value: Entity.NumeracionHasta, disable: ReadOnly() || !Entity.CiudadHastaSelect()">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>