<div class="portlet light bg-inverse">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">
                Filtrar
            </span>
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="Retraer/Expandir" title="Retraer/Expandir">
            </a>
        </div>
    </div>

    <div class="portlet-body form">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-6 form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Fecha desde</label>
                        <div class="input-group date form_datetime bs-datetime">
                            <input class="form-control" type="text" data-bind="dateTimePicker: Filters().Desde, dateTimePickerOptions: {
                                format: 'dd-mm-yyyy',
                                momentFormat: 'DD-MM-YYYY',
                                value: Filters().Desde,
                                todayBtn: true,
                                minView: 2,
                                todayHighlight:true,
                                autoclose:true
                            },
                            ">
                            <span class="input-group-addon">
                                <button class="btn default date-set" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="control-label visible-ie8 visible-ie9" style="display: block;">Fecha hasta</label>
                        <div class="input-group date form_datetime bs-datetime">
                            <input class="form-control" type="text" data-bind="dateTimePicker: Filters().Hasta, dateTimePickerOptions: {
                            format: 'dd-mm-yyyy',
                            momentFormat: 'DD-MM-YYYY',
                            value: Filters().Hasta,
                            todayBtn: true,
                            minView: 2,
                            todayHighlight:true,
                            autoclose:true
                        },
                        ">
                            <span class="input-group-addon">
                                <button class="btn default date-set" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6 form-group">
                        <label for="filters_comprador" class="control-label">
                            Comprador
                        </label>
                        <select id="filters_comprador"
                            data-bind="selectedOptions:Filters().CompradoresSelected, 
                            options: Filters().Compradores, 
                            valueAllowUnset: true, 
                            optionsText: 'text', 
                            optionsValue: 'id', select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }">
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="filters_proveedor" class="control-label">
                            Proveedor
                        </label>
                        <select id="filters_proveedor"
                            data-bind="selectedOptions:Filters().ProveedoresSelected, 
                            options: Filters().Proveedores, 
                            valueAllowUnset: true, 
                            optionsText: 'text', 
                            optionsValue: 'id', select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }">
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions text-right">
            <button 
                data-bind="click: cleanFilters" 
                type="button" 
                class="btn green btn-outline">
                Limpiar
            </button>
            <button 
                data-bind="click: filter" 
                type="button" 
                class="btn green">
                Buscar
            </button>
        </div>
    </div>
</div>