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
                <div class="col-md-12">
                    <div class="col-md-6 form-group">
                        <label for="filters_comprador" class="control-label">
                            Comprador
                        </label>
                        <select id="filters_comprador" data-bind="selectedOptions:Filters().CompradoresSelected, 
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
                        <select id="filters_proveedor" data-bind="selectedOptions:Filters().ProveedoresSelected, 
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