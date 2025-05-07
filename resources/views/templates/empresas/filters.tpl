<div class="portlet light bg-inverse">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">
                Filtrar
            </span>
        </div>
        <div class="tools">
            <a 
                href="javascript:;" 
                class="collapse" 
                data-original-title="Retraer/Expandir" 
                title="Retraer/Expandir">
            </a>
        </div>
        <div class="actions">
            <ul class="list-inline">
                {if isCustomer()}
                    <li>
                        <span class="h3 text-capitalize">
                            <small>Vinculados</small>
                            <span data-bind="text: TotalAsociados()"></span>
                        </span>
                    </li>
                {/if}
                <li>
                    <span class="h3 text-capitalize">
                        <small>Total Optus</small>
                        <span data-bind="text: TotalOptus()"></span>
                    </span>
                </li>
            </ul>
        </div>
    </div>

    <div class="portlet-body form">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    {if isCustomer()}
                        <div class="form-group">
                            <label for="filters_associated" class="control-label">
                                Asociación
                            </label>
                            <select 
                                id="filters_associated"
                                data-bind="value: 
                                    Filters().Associated, 
                                    valueAllowUnset: true, 
                                    options: Filters().AssociatedList, 
                                    optionsText: 'text', 
                                    optionsValue: 'id', 
                                    select2: { placeholder: 'Seleccionar...' }">
                            </select>
                        </div>
                    {/if}
                    <div class="form-group">
                        <label for="filters_cuit" class="control-label">
                            CUIT
                        </label>
                        <input 
                            id="filters_cuit"
                            name="filters_cuit" 
                            class="form-control" 
                            type="text" 
                            data-bind="value: Filters().Cuit" />
                    </div>
                    <div class="form-group">
                        <label for="filters_rubros" class="control-label">
                            Rubros
                        </label>
                        <select 
                            id="filters_rubros" 
                            data-bind="selectedOptions: 
                                Filters().Areas, options: 
                                Filters().AreasList, 
                                valueAllowUnset: true, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }">
                        </select>
                    </div>
                    {if isAdmin()}
                        <div class="form-group">
                            <label for="filters_customers" class="control-label">
                                Clientes Asociados
                            </label>
                            <select 
                                id="filters_customers" 
                                data-bind="selectedOptions: 
                                    Filters().Customers, 
                                    options: Filters().CustomersList, 
                                    valueAllowUnset: true, 
                                    optionsText: 'text', 
                                    optionsValue: 'id', select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }">
                            </select>
                        </div>
                    {/if}
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="filters_countries" class="control-label">
                            Países
                        </label>
                        <select 
                            id="filters_countries" 
                            data-bind="selectedOptions: 
                                Filters().Countries, 
                                options: Filters().CountriesList, 
                                valueAllowUnset: true, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filters_provinces" class="control-label">
                            Provincias
                        </label>
                        <select 
                            id="filters_provinces" 
                            data-bind="selectedOptions: 
                                Filters().Provinces, 
                                options: Filters().ProvincesList, 
                                valueAllowUnset: true, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }, disable: Filters().Countries().length == 0">
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filters_cities" class="control-label">
                            Ciudades
                        </label>
                        <select 
                            id="filters_provinces" 
                            data-bind="selectedOptions: 
                                Filters().Cities, 
                                options: Filters().CitiesList, 
                                valueAllowUnset: true, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...', multiple: true, allowClear: true }, disable: Filters().Provinces().length == 0">
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