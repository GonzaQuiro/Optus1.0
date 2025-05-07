<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <tbody>
            <tr>
                <td class="col-md-5 text-center" style="vertical-align: middle; padding: 0;">
                    <div class="mt-element-step">
                        <div class="row step-background">
                            <div class="col-md-12 bg-grey-steel mt-step-col" data-bind="css: Countdown() ? 'done' : (Timeleft() ? ' active' : '')">
                                <div class="mt-step-number">
                                    <small>
                                        <i class="fa fa-clock-o"></i>
                                    </small>
                                </div>
                                
                                <div class="mt-step-title bold font-grey-cascade" data-bind="text: Countdown() ? Countdown() : (Timeleft() ? Timeleft() : 'Finalizada')"></div>

                                <div class="mt-step-content font-grey-cascade" data-bind="text: Countdown() ? 'Tiempo hasta finalizar subasta' : (Timeleft() ? 'Tiempo hasta comenzar subasta' : 'La subasta ha finalizado.')"></div>

                            </div>
                        </div>
                    </div>
                </td>
                <td class="col-md-7">
                    <h4 class="bold">Parámetros del Concurso</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Unidad mínima para mejorar una oferta:</td>
                                <td data-bind="text: UnidadMinima() + ' (' + Moneda() + ')'">
                                </td>
                            </tr>
                            <tr>
                                <td>Cantidad de participantes</td>
                                <td data-bind="text: Countdown()  ? (Conectados() + '/' + CantidadOferentes()) : 'No disponible'">
                                </td>
                            </tr>
                            <tr>
                                <td>Permite ofertas intermedias</td>
                                <td data-bind="text: SoloOfertasMejores() ? 'NO' : 'SI'">
                                </td>
                            </tr>
                            <tr>
                                <td>Oferta Min/Max para finalizar concurso</td>
                                <td data-bind="text: PrecioMaximo() > '0' || PrecioMinimo() > '0' ? 'SI' : 'NO'">
                                </td>
                            </tr>
                            <tr>
                                <td>Moneda</td>
                                <td data-bind="text: Moneda()">
                                </td>
                            </tr>
                            <tr>
                                <td>Chat habilitado</td>
                                <td data-bind="text: Chat() ? 'SI' : 'NO' ">
                                </td>
                            </tr>
                            <tr>
                                <td>Duración</td>
                                <td data-bind="text: Duracion()">
                                </td>
                            </tr>
                            <tr>
                                <td>Tiempo adicional</td>
                                <td data-bind="text: TiempoAdicional() + ' seg.' ">
                                </td>
                            </tr>    
                            
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>