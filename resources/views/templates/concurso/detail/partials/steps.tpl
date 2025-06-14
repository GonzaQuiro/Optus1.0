<div class="mt-element-step">
    {if $tipo neq 'chat-muro-consultas'}
    <div class="row equal step-line">
        <!-- ko foreach: Steps -->
        <div class="mt-step-col col-xs-12" data-bind="css: {literal}
                { 
                    active: current, 
                    first: first, 
                    last: last, 
                    done: done,
                    'col-md-2': ($parent.Steps().length == 5 && (number !== 2 && number !== 3)),
                    'col-md-3': (($parent.Steps().length == 4) || ($parent.Steps().length === 5 && (number == 2 || number == 3))), 
                }
            {/literal}">
            <!-- ko if: !current -->
            <a href="#" data-bind="attr: {literal}{ href: url }{/literal}">
                <div class="mt-step-number bg-white" data-bind="text: number"></div>
                <div class="mt-step-title uppercase font-grey-cascade" data-bind="text: title"></div>
                <div class="mt-step-content font-grey-cascade" data-bind="text: description"></div>
            </a>
            <!-- /ko -->
            <!-- ko if: current -->
            <div class="mt-step-number bg-white" data-bind="text: number"></div>
            <div class="mt-step-title uppercase font-grey-cascade" data-bind="text: title"></div>
            <div class="mt-step-content font-grey-cascade" data-bind="text: description"></div>
            <!-- /ko -->
        </div>
        <!-- /ko -->
    </div>
    {/if}
    
    <!-- ko if: ShowChatButton()-->
    <div class="row" style="margin-bottom: 1em;">
        {if $tipo neq 'chat-muro-consultas'}
            <a class="btn uppercase btn-info btn-lg" href="javascript:void(0);" data-bind="click: goToChatMuroConToken">
                Chat Muro de consultas
            </a>

            <div class="caption-subject font-hide" style="padding:5px; display: inline-block;" data-bind="pulsate: $root.HasNewMessage, pulsateOptions: { 
                        color: '#ed6b75',
                        reach: '5',
                        speed: '500',
                        pause: '0',
                        glow: true,
                        repeat: true,
                        onHover: false
                    }">
                Nuevo Mensaje
            </div>
        {/if}
        {if $tipo eq 'chat-muro-consultas'}
            <a class="btn uppercase btn-info btn-md" href="javascript:void(0);" data-bind="click: goBackWithToken">Volver al concurso</a>
        {/if}
    </div>
    <!-- /ko -->
    
</div>