{extends 'errors/main.tpl'}

{block 'styles'}
    <link href="{asset('/pages/css/error.min.css')}" rel="stylesheet" type="text/css" />
{/block}

{block 'main'}
<div class="row">
    <div class="col-md-12 page-500">                
        <div class="number font-green">
            {$status}
        </div>
        <div class="details">
            <h3>{$title}</h3>
            <p>
                {$message}
                <div class="form-group">
                    <a href="{route('serveHome')}" class="btn green btn-outline"> 
                        Volver a la Home
                    </a>
                </div>
            </p>
        </div>
    </div>
</div>
{/block}