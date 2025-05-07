<!-- ko if: Evaluaciones().length > 0 -->
<div class="m-heading-1 border-default m-bordered text-left">
    <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Expectativas</h4>
    <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2">
            <ul class="nav nav-tabs tabs-left" data-bind="foreach: Evaluaciones">
                <li data-bind="css: $index() == '0' ? 'active' : ''">
                    <a href="#" data-toggle="tab"
                        data-bind="text: RazonSocial, attr: { href: '#tab_8_' + $index() }"></a>
                </li>
            </ul>
        </div>
        <div class="col-md-10 col-sm-10 col-xs-10">
            <div class="tab-content" data-bind="foreach: Evaluaciones">
                <div class="tab-pane fade in"
                    data-bind="css: $index() == '0' ? 'active' : '', attr: { id: 'tab_8_' + $index() }">
                    <table class="table table-striped table-bordered centrar-celdas expectativas">

                        <thead class="text-center">
                            <tr>
                                <th class="text-center"> Item </th>
                                <th class="text-center"> No cumple </th>
                                <th class="text-center"> Cumple levemente </th>
                                <th class="text-center"> Cumple </th>
                                <th class="text-center"> Supera </th>
                                <th class="text-center"> No aplica </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Puntualidad</td>
                                {foreach from=array(1,2,3,4,0) item=valor}
                                    <td class="wrapper-expectativa">
                                        <label>
                                            <input type="radio" value="{$valor}"
                                                data-bind="checked: Puntualidad, attr: { name: 'puntualidad_' + $index() }, enable: PermitirEnvio" />
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td>Calidad</td>
                                {foreach from=array(1,2,3,4,0) item=valor}
                                    <td class="wrapper-expectativa">
                                        <label>
                                            <input type="radio" value="{$valor}"
                                                data-bind="checked: Calidad, attr: { name: 'calidad_' + $index() }, enable: PermitirEnvio" />
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td>Orden y limpieza</td>
                                {foreach from=array(1,2,3,4,0) item=valor}
                                    <td class="wrapper-expectativa">
                                        <label>
                                            <input type="radio" value="{$valor}"
                                                data-bind="checked: OrdenYlimpieza, attr: { name: 'orden-limpieza_' + $index() }, enable: PermitirEnvio" />
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td>Medio ambiente</td>
                                {foreach from=array(1,2,3,4,0) item=valor}
                                    <td class="wrapper-expectativa">
                                        <label>
                                            <input type="radio" value="{$valor}"
                                                data-bind="checked: MedioAmbiente, attr: { name: 'medio-ambiente_' + $index() }, enable: PermitirEnvio" />
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td>Higiene y seguridad</td>
                                {foreach from=array(1,2,3,4,0) item=valor}
                                    <td class="wrapper-expectativa">
                                        <label>
                                            <input type="radio" value="{$valor}"
                                                data-bind="checked: HigieneYseguridad, attr: { name: 'higiene-seguridad_' + $index() }, enable: PermitirEnvio" />
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td>Experiencia</td>
                                {foreach from=array(1,2,3,4,0) item=valor}
                                    <td class="wrapper-expectativa">
                                        <label>
                                            <input type="radio" value="{$valor}"
                                                data-bind="checked: Experiencia, attr: { name: 'experiencia_' + $index() }, enable: PermitirEnvio" />
                                        </label>
                                    </td>
                                {/foreach}
                            </tr>
                            <tr>
                                <td colspan="6" class="text-left" style="border: 1px solid #fff;">
                                    <label for="coment">Comentario</label>
                                    <textarea class="form-control" id="coment"
                                        data-bind="textInput: Comentario, attr: { name: 'comentario_' + $index() }, enable: PermitirEnvio"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right" style="border: 1px solid #fff;">
                                    <button type="button" class="btn btn-success"
                                        data-bind="click: $parent.EvaluationSend, text: PermitirEnvio ? 'Guardar y enviar evaluación' : 'La evaluación ya fue enviada', enable: PermitirEnvio">Guardar
                                        y enviar evaluación</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="tab_6_2">

                </div>
                <div class="tab-pane fade" id="tab_6_3">

                </div>
                <div class="tab-pane fade" id="tab_6_4">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- /ko -->