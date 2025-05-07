<!-- Oferta Económica: Acción de usuario -->
<!-- ko if: IsOnline() -->
<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">
                Propuesta económica Subasta
            </h4>

            <!-- Instrucciones y Countdown --> 
            {include file='concurso/detail/offerer/partials/economica/online-tables-header.tpl'}

            <!-- Carga de datos -->
            {include file='concurso/detail/offerer/partials/economica/online-tables-live.tpl'}
        </div>
    </div>
</div>
<!-- /ko -->

<!-- ko if: IsSobrecerrado() || IsGo() -->
<!-- ko if: !Rechazado() || (Rechazado() || HasEconomicaPresentada()) || !HasEconomicaRevisada -->
<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;" data-bind="ifnot: RondaActual() > 1">
                <span data-bind="text:'Carga de archivo propuesta económica'"></span>
            </h4>
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;" data-bind="if: RondaActual() > 1">          
                <span data-bind="text:'Carga de archivo propuesta económica (' + Title() + ')'"></span>      
            </h4>
            {include file='concurso/detail/offerer/partials/economica/file_upload.tpl'}
        </div>
    </div>
</div>
<!-- /ko -->
<!-- /ko -->

<!-- ko if: IsSobrecerrado() || IsGo() -->
<!-- ko if: HasEconomicaRevisada() && !Rechazado() && !Adjudicado() -->
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success">
            Usted ya envió las propuestas, espere a ser calificado.
        </div>
    </div>
</div>
<!-- /ko -->
<!-- /ko -->

<!-- ko if: IsOnline() -->
<!-- ko if: !EnableEconomic() && !Timeleft() && !Rechazado() && !Adjudicado() -->
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-success">
            La subasta ya finalizó, espere a ser calificado.
        </div>
    </div>
</div>
<!-- /ko -->
<!-- /ko -->

<!-- ko if: Rechazado() -->
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-danger">
            Usted no pasó la etapa de presentación económica, ¡gracias por participar!
        </div>
    </div>
</div>
<!-- /ko -->