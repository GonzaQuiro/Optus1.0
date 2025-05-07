<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{capture 'post_scripts_child'}
    <script src="{asset('/global/plugins/icheck/icheck.min.js')}" type="text/javascript"></script>
    <script src="{asset('/pages/scripts/form-icheck.min.js')}" type="text/javascript"></script>
{/capture}
{$post_scripts_child[] = $smarty.capture.post_scripts_child scope="global"}

<!-- ko if: Adjudicado() && !Eliminado() -->
{include file='concurso/detail/customer/partials/evaluacion/resultados-concurso.tpl'}
{include file='concurso/detail/customer/partials/evaluacion/adjudicacion-concurso.tpl'}
{include file='concurso/detail/customer/partials/evaluacion/evaluacion-concurso.tpl'}
<!-- /ko -->

<!-- ko if: !Adjudicado() && !Eliminado() -->
<div class="text-center vertical-align-middle" style="color: crimson;">El concurso no ha sido adjudicado</div>
<!-- /ko -->