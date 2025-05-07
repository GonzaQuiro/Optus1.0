<!-- ko if: !IncluyeTecnica() -->
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success">
            Este concurso no incluye etapa de precalificación técnica.
        </div>
    </div>
</div>
<!-- /ko -->


<!-- ko if: IncluyeTecnica() -->
<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <!-- ko if: IsSobrecerrado() || IsOnline() -->
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Carga de archivos propuesta técnica</h4>
            {include file='concurso/detail/offerer/partials/tecnica/puntuacion_base.tpl'}
            {include file='concurso/detail/offerer/partials/tecnica/file_upload.tpl'}
            <!-- /ko -->

            <!-- ko if: IsGo() -->
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Documentación</h4>
            {include file='concurso/detail/offerer/partials/tecnica/go-documentation.tpl'}
            <!-- /ko -->
        </div>
    </div>
</div>
<!-- /ko -->