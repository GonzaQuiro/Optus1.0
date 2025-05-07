<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            {if $page === 'home'}
                <span>Inicio</span>
            {else}
                <a href="{route('serveHome')}">
                    Inicio
                </a>

                <i class="fa fa-circle"></i>
            {/if}
        </li>

        {if $page !== 'home'}
            <!-- ko foreach: Breadcrumbs -->
            <li>
                <!-- ko if: $index() === ($parent.Breadcrumbs().length - 1) --> 
                <span data-bind="text: $data.description"></span>
                <!-- /ko -->
                
                <!-- ko ifnot: $index() === ($parent.Breadcrumbs().length - 1) --> 
                <!-- ko if: $data.url -->
                <a href="/" data-bind="text: $data.description, attr: {literal}{ 'href': $data.url }{/literal}"></a>
                <!-- /ko -->
                <!-- ko ifnot: $data.url -->
                <span data-bind="text: $data.description"></span>
                <!-- /ko -->
                <i class="fa fa-circle"></i>
                <!-- /ko -->
            </li>
            <!-- /ko -->
        {/if}
    </ul>
    <div class="page-toolbar">
        <div class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Fecha Actual">
            <i class="icon-calendar"></i>&nbsp;
            {$fechaActual}
        </div>
    </div>
</div>
<!-- END PAGE BAR -->