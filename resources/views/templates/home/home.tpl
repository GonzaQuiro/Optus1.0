{extends 'home/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'home'}
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
<script type="text/javascript">
var Home = function () {
    var self = this;
}

jQuery(document).ready(function () {
    window.E = new Home();
    AppOptus.Bind(E);
});

// Chrome allows you to debug it thanks to this
{chromeDebugString('dynamicScript')}
</script>
{/block}