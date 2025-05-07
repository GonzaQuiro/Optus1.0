<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.5
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>{env('APP_TITLE')}</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for " name="description" />
        <meta content="" name="author" />
        <link href="{asset('/fonts/css.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/font-awesome/css/font-awesome.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/simple-line-icons/simple-line-icons.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/bootstrap/css/bootstrap.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/select2/css/select2.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/plugins/select2/css/select2-bootstrap.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/global/css/components-rounded.min.css')}" rel="stylesheet" id="style_components" type="text/css" />
        <link href="{asset('/global/css/plugins.min.css')}" rel="stylesheet" type="text/css" />
        <link href="{asset('/pages/css/login-3.min.css')}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{asset('/favicon.ico')}" /> </head>
    <!-- END HEAD -->

    <body class="login" style="background-color: #6C338C !important;">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="index.html">
                <img src="{asset('/global/img/logo.png')}" alt="" style="width: 200px;">
            </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            {*            <form class="login-form">*}
            <div class="login-form" id="login">
                <h3 class="form-title" style="text-align: center;">Recuperar contraseña</h3>
                {if {$token_valid}}
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Contraseña</label>
                        <div class="input-icon">
                            <i class="fa fa-lock"></i>
                            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Contraseña" name="password" id="password" required/> </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Reescriba la contraseña</label>
                        <div class="controls">
                            <div class="input-icon">
                                <i class="fa fa-check"></i>
                                <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Reescriba la contraseña" name="rpassword" id="rpassword" required /> 
                            </div>
                            <div id="CheckPasswordMatch"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <h6 class="form-title" style="text-align: center;">La contraseña debe contener:</h6>
                        <ul id="clue">
                            <li>De 8 a 16 caracteres alfanuméricos</li>
                            <li>Al menos una letra</li>
                            <li>Al menos una letra mayúscula</li>
                            <li>Al menos un número</li>
                        </ul>
                    </div>
                    <div class="form-actions">
                        <label class="rememberme mt-checkbox mt-checkbox-outline">
                        </label>
                        <button type="button" id="back-btn" class="btn grey-salsa btn-outline" onclick="goBack()"> {$l['login_forgot'][6]} </button>
                        <button type="button" id="btnSubmit" class="btn green pull-right" onclick="updatePassword()" disabled> Cambiar contraseña </button>
                    </div>
                {else}
                    <div class="form-group">
                        <span>
                            El token ha expirado, por favor realice una nueva petición
                        </span>
                    </div>
                {/if}
            </div>
            <!-- END LOGIN FORM -->
        </div>
        <br>
        <!-- END LOGIN -->
        <!--[if lt IE 9]>
<script src="{asset('/global/plugins/respond.min.js')}"></script>
<script src="{asset('/global/plugins/excanvas.min.js')}"></script> 
<script src="{asset('/global/plugins/ie8.fix.min.js')}"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="{asset('/global/plugins/jquery.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/bootstrap/js/bootstrap.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/js.cookie.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/jquery.blockui.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}" type="text/javascript"></script>
        <script src="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.min.js')}" type="text/javascript"></script>
        <script src="{asset('/pages/scripts/ui-sweetalert.min.js')}" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="{asset('/global/plugins/select2/js/select2.full.min.js')}" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script type="text/javascript">
        var HOST = '{env('APP_SITE_URL')}';
    </script>
    <script src="{asset('/global/scripts/app.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.validation/knockout.validation.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/knockout.validation/localization/es-ES.js')}" type="text/javascript"></script>'
    <script src="{asset('/global/scripts/knockout.validation/localization/en-US.js')}" type="text/javascript"></script>'    
    <script src="{asset('/js/services.js')}" type="text/javascript"></script>
    <script src="{asset('/js/app.js')}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <script>
        $(document).ready(function() {
            $("#password").on('keyup', function() {
                var password = $("#password").val();
                var confirmPassword = $("#rpassword").val();
                var cant = password.length;

                //longitud password
                if ((cant<8) || (cant>16))
                    $("#clue li:nth-child(1)").css("color", "red");
                else
                    $("#clue li:nth-child(1)").removeAttr("style");

                //validate letter
                if (!password.match(/[A-z]/) )
                    $("#clue li:nth-child(2)").css("color", "red");
                else
                    $("#clue li:nth-child(2)").removeAttr("style");

                //validate capital letter
                if (!password.match(/[A-Z]/) )
                    $("#clue li:nth-child(3)").css("color", "red");
                else
                    $("#clue li:nth-child(3)").removeAttr("style");
                
                //validate capital letter
                if (!password.match(/\d/) )
                    $("#clue li:nth-child(4)").css("color", "red");
                else
                    $("#clue li:nth-child(4)").removeAttr("style");

                //validate equals Password
                if (confirmPassword != password){
                    $("#CheckPasswordMatch").html("La contraseña no coincide").css("color", "red");
                }else{
                    $("#CheckPasswordMatch").html("");
                }
                //enableButton
                if(!((cant<8) || (cant>16)) && password.match(/[A-z]/) && password.match(/[A-Z]/) && password.match(/\d/) && (password == confirmPassword))
                    $('#btnSubmit').prop('disabled', false)
                else
                    $('#btnSubmit').prop('disabled', true)
            });

            $("#rpassword").on('keyup', function() {
                var password = $("#password").val();
                var confirmPassword = $("#rpassword").val();
                var cant = password.length;
                if (password != confirmPassword){
                    $("#CheckPasswordMatch").html("La contraseña no coincide").css("color", "red");
                    $('#btnSubmit').prop('disabled', true)
                }else{
                    $("#CheckPasswordMatch").html("");
                    if(!((cant<8) || (cant>16)) && password.match(/[A-z]/) && password.match(/[A-Z]/) && password.match(/\d/) && (password == confirmPassword)){
                        $('#btnSubmit').prop('disabled', false)
                    }
                    
                    
                }

            });


        });

        function goBack(){
            localStorage.clear();
            window.location.href = '/login';
        }
    
        function updatePassword(){
            var password = $("#password").val();
            var rpassword = $("#rpassword").val();
            var usuario = {$usuario};
            if(!password.trim() || !rpassword.trim()){
                swal({
                    title: 'Cambio de contraseña',
                    text: 'Debe completar los campos',
                    type: 'error',
                    closeOnClickOutside: false,
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonText: 'Aceptar',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonText: 'Cancelar',
                    cancelButtonClass: 'btn btn-default'
                }, function(result) {
                    swal.close();
                })
            }else{
                swal({
                    title: 'Cambio de contraseña',
                    text: 'Una vez culminado el proceso, puede ingresar con su nueva contraseña',
                    type: 'info',
                    closeOnClickOutside: false,
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonText: 'Aceptar',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonText: 'Cancelar',
                    cancelButtonClass: 'btn btn-default'
                }, function(result) {
                    swal.close();
                    if (result) {
                        $.blockUI();
                        var url = '/recover/';
                        Services.Patch(url, {
                                password,
                                usuario
                            },
                            (response) => {
                                swal.close();
                                $.unblockUI();
                                setTimeout(function() {
                                    if (response.success) {
                                        swal({
                                            title: 'Hecho',
                                            text: response.message,
                                            type: 'success',
                                            closeOnClickOutside: false,
                                            closeOnConfirm: true,
                                            confirmButtonText: 'Aceptar',
                                            confirmButtonClass: 'btn btn-success'
                                        }, function(result) {
                                        ko.cleanNode(document.body); // Limpia cualquier binding anterior
                                        localStorage.clear();
                                        sessionStorage.clear();

                                        window.location.href = '/login';
                                    });
                                    } else {
                                        swal('Error', response.message, 'error');
                                    }
                                }, 500);
                            },
                            (error) => {
                                swal.close();
                                $.unblockUI();
                                setTimeout(function() {
                                    swal('Error', error.message, 'error');
                                }, 500);
                            },
                            null,
                            null
                        );
                    }
                });
            }
        }

    </script>
</body>

</html>