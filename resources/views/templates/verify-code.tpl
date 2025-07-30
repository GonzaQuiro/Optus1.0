<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>{env('APP_TITLE')}</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link href="{asset('/fonts/css.css')}" rel="stylesheet" />
  <link href="{asset('/global/plugins/font-awesome/css/font-awesome.min.css')}" rel="stylesheet" />
  <link href="{asset('/global/plugins/bootstrap/css/bootstrap.min.css')}" rel="stylesheet" />

  <!-- CSS de Bootstrap SweetAlert -->
  <link href="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.css')}" rel="stylesheet" />

  <link href="{asset('/pages/css/login-3.min.css')}" rel="stylesheet" />
  <link rel="shortcut icon" href="{asset('/favicon.ico')}" />

  <style>
    .input-icon { position: relative; }
    .input-icon > i {
      position: absolute; left: 10px; top: 50%;
      transform: translateY(-50%); color: #888; z-index: 2;
    }
    .input-icon > input { padding-left: 35px !important; }
  </style>
</head>
<body class="login" style="background-color: #6C338C !important;">
  <div class="logo text-center">
    <a href="index.html">
      <img src="{asset('/global/img/logo.png')}" style="width:200px;" alt="Logo"/>
    </a>
  </div>

  <div class="content">
    <div class="login-form" id="login">
      <h3 class="form-title text-center">Verificación de código</h3>

      <form method="GET" action="/process-verify-code" id="verifyForm">
        <div class="form-group">
          <label class="control-label visible-ie8 visible-ie9">
            Código de verificación
          </label>
          <div class="input-icon">
            <i class="fa fa-check"></i>
            <input
              class="form-control placeholder-no-fix"
              type="text"
              placeholder="Ingrese el código"
              name="verification_code"
              id="verification_code"
              autocomplete="off"
              required
            />
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn grey-salsa btn-outline" onclick="goBack()">
            Volver
          </button>
          <button type="submit" class="btn green pull-right">
            Verificar Código
          </button>
        </div>
      </form>

      {if $error}
        <div class="text-center" style="color:red; margin-top:10px;">
          <p>El código ingresado es incorrecto. Intente nuevamente.</p>
        </div>
      {/if}
    </div>
  </div>

  <!-- JS libs -->
  <script src="{asset('/global/plugins/jquery.min.js')}"></script>
  <script src="{asset('/global/plugins/bootstrap/js/bootstrap.min.js')}"></script>
  <script src="{asset('/global/scripts/app.min.js')}"></script>

  <!-- JS de Bootstrap SweetAlert -->
  <script src="{asset('/global/plugins/bootstrap-sweetalert/sweetalert.min.js')}" type="text/javascript"></script>

  <script>
    // Mismo comportamiento de “Volver”
    function goBack() {
      window.location.href = '/login';
    }
  </script>

  {if $success}
  <script>
    swal({
      title: 'Código ingresado con éxito',
      text: 'Por favor inicie sesión nuevamente',
      type: 'success',
      showCancelButton: false,
      confirmButtonText: 'Aceptar',
      confirmButtonClass: 'btn btn-success',
      closeOnConfirm: true,
      animation: 'pop',         // igual estilo de animación
      customClass: 'swal-optus' // si quieres la misma clase que usas en tu otro template
    }, function() {
      window.location.href = '{$next}';
    });
  </script>
  {/if}
</body>
</html>
