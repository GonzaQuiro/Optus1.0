{extends 'index.tpl'}

{block 'title'}
    {$title}
{/block}

{block 'main'}
    <div class="row">
        <div class="col-md-12">
            {include file='reportes/evaluados/filters.tpl'}
        </div>
    </div>

    {block 'reports-list'}{/block}

{/block}