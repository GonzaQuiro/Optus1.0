<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{env('APP_TITLE')}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="Verification page" name="description" />
    <meta content="" name="author" />
    <link href="{asset('/fonts/css.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/font-awesome/css/font-awesome.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/bootstrap/css/bootstrap.min.css')}" rel="stylesheet" type="text/css" />
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
        <div class="login-form" id="login">
            <h3 class="form-title" style="text-align: center;">Verificación de código</h3>

            <form method="GET"  id="verifyForm">
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Código de verificación</label>
                    <div class="input-icon">
                        <i class="fa fa-check"></i>
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Ingrese el código" name="verification_code" id="verification_code" required />
                    </div>
                   </div>

                <div class="form-actions">
                    <button type="button" id="back-btn" class="btn grey-salsa btn-outline" onclick="goBack()">Volver</button>
                    <button type="submit" id="btnSubmit" class="btn green pull-right">Verificar Código</button>
                </div>
            </form>
            
            {if $error}
                <div style="color: red; text-align: center; margin-top: 10px;">
                    <p>El código ingresado es incorrecto. Intente nuevamente.</p>
                </div>
            {/if}
            
        </div>
    </div>

    <script src="{asset('/global/plugins/jquery.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/bootstrap/js/bootstrap.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/scripts/app.min.js')}" type="text/javascript"></script>

    <script>
    $(document).ready(function() {
        // Obtén el código de verificación que se pasó desde el controlador
        var validCode = '{$two_factor_code}'; // El valor de two_factor_code enviado desde el controlador

        $("#verifyForm").submit(function(event) {
            event.preventDefault();
            var verificationCode = $("#verification_code").val();
            window.location.href = '/process-verify-code?verification_code=' + verificationCode;

        });
    });

    function goBack(){
        window.location.href = '/login'; // Volver a la página de login
    }
    </script>
</body>
</html>