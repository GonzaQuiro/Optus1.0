<div class="page-header navbar navbar-fixed-top">
    <div class="page-header-inner ">
        <div class="page-logo">
            <a href="/">
                <img src="{asset('/global/img/logo-small.png')}" alt="" class="logo-default" style="width: 100px; margin-top: 9px;">
            </a>
            {if $page != 'error'}
                <div class="menu-toggler sidebar-toggler">
                    <span></span>
                </div>
            {/if}
        </div>

        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>

        {if $page != 'error'}
            <div class="top-menu">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown dropdown-user">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <img 
                                class="img-circle" 
                                data-bind="attr: {
                                    src: User.Image
                                }" />
                            <span class="username username-hide-on-mobile" data-bind="text: User.FullName"></span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <li>
                                <a data-bind="click: User.LogOut" href="#">
                                    <i class="icon-key"></i> Salir
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        {/if}
    </div>
</div>