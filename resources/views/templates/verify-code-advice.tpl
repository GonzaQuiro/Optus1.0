<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{env('APP_TITLE')}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Password expired page" name="description" />
    <meta content="" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{asset('/fonts/css.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/font-awesome/css/font-awesome.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap/css/bootstrap.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/pages/css/login-3.min.css')}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{asset('/favicon.ico')}" />
</head>
<body class="login" style="background-color: #6C338C !important;">
    <div class="logo">
        <a href="index.html">
            <img src="{asset('/global/img/logo.png')}" alt="" style="width: 200px;">
        </a>
    </div>

    <div class="content">
        <div class="login-form" id="verify">
            <h3 class="form-title" style="text-align: center;">Código de Verificación</h3>
            <p style="text-align: center;">Ingrese su correo electrónico para recibir un código de verificación.</p>

            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Correo Electrónico</label>
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Ingrese su correo" name="email" id="email" required />
                </div>
            </div>

            <div class="form-actions" style="text-align: center;">
                <button type="button" id="send-btn" class="btn green" onClick="sendVerifyCode()">Enviar Código</button>
            </div>
        </div>
    </div>
    

    <script src="{asset('/global/plugins/jquery.min.js')}" type="text/javascript"></script>
    
    <script src="{asset('/global/scripts/knockout.js')}" type="text/javascript"></script>

    
    <script src="{asset('/global/plugins/jquery.blockui.min.js')}" type="text/javascript"></script>
    <script src="{asset('/js/services.js')}" type="text/javascript"></script> 
    <script src="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.min.js')}" type="text/javascript"></script>

    <script>



        
    function sendVerifyCode(){
                var email = $('#email').val();
                swal({
                    title: '¿Desea recuperar la contraseña del usuario '+ email +' ? ',
                    text: 'Se enviará un correo electrónico con el link para recuperar su contraseña.',
                    type: 'info',
                    closeOnClickOutside: false,
                    showCancelButton: true,
                    closeOnConfirm: true,
                    confirmButtonText: 'Aceptar',
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonText: 'Cancelar',
                    cancelButtonClass: 'btn btn-default'
                }, function(result) {
                    if (result) {
                        $.blockUI();
                        var url = '/send-code';
                        Services.Post(url, {
                                email
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
                                            window.location.href = '/verify-code';
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



    </script>

</body>
</html>