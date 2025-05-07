{extends 'index.tpl'}

{block 'title'}
    {$title}
{/block}

{block 'main'}

    {if $accion eq 'listado-offerer' && $userType eq 'superadmin'}
        <div class="row">
            <div class="col-md-12">
                {include file='empresas/filters.tpl'}
            </div>
        </div>
    {/if}

    {block 'company-list'}{/block}

    {block 'company-edit'}{/block}

    {block 'company-detail'}{/block}

    {block 'company-profile-edit'}{/block}

{/block}