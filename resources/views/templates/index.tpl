<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es">
    <!--<![endif]-->

    {include file="global/header.tpl"}

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="page-wrapper">
            {include file='global/toolbar.tpl'}

            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            <div class="clearfix"> </div>
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container">

                {include file='global/sidebar.tpl'}

                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content">

                        {include file='global/breadcrumbs.tpl'}

                        <h1 class="page-title"> 
                            {block name='title'}{/block}
                        </h1>

                        <div data-bind="template: { 'name': 'page-index', 'if': isLoaded() }"></div>
                        
                        <script id="page-index" type="text/html">
                            {block name='main'}{/block}
                        </script>

                    </div>
                </div>
            </div>
            <!-- END CONTAINER -->

            <!-- BEGIN FOOTER -->
            <div class="page-footer">
                <div class="page-footer-inner"> {'Y'|date} &copy; Optus by GCG
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>
            <!-- END FOOTER -->
        </div>
        
        <!-- BEGIN QUICK NAV -->
        <div class="quick-nav-overlay"></div>
        <!-- END QUICK NAV -->

        {include file='global/scripts.tpl'}
    </body>
</html>