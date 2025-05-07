<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <tbody>
            <tr>
                <td class="col-md-8">
                    <p class="bold">Instrucciones concurso Online</p>
                    <p>1. Ofertar en cada una de las items que usted considere oportuno</p>
                    <p>2. Lea atentamente la moneda y unidad de medida definidas para este concurso, ya que éstas forman parte de sus ofertas</p>
                    <p>3. Cuenta regresiva inicio: indica cuanto tiempo resta para que se de inicio al concurso, no se pueden realizar ofertas aún</p>
                    <p>4. Cuenta regresiva finalización: Indica cuanto tiempo resta para la finalización del concurso y en consecuenta para realizar oferta</p>
                    <p>5. Puede mejorar las ofertas cuantas veces desee o bien dejar de ofertar cuando considere necesario</p>
                    <p>6. Sugerimos permanecer conectado hasta la finalización del concurso</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>

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
                                
                                <!-- ko if: VerTiempoRestante() -->
                                <div class="mt-step-title bold font-grey-cascade" data-bind="text: Countdown() ? Countdown() : (Timeleft() ? Timeleft() : 'Finalizada')"></div>

                                <div class="mt-step-content font-grey-cascade" data-bind="text: Countdown() ? 'Tiempo hasta finalizar subasta' : (Timeleft() ? 'Tiempo hasta comenzar subasta' : 'La subasta ha finalizado.')"></div>
                                <!-- /ko -->

                                <!-- ko if: !VerTiempoRestante() -->
                                <div class="mt-step-title bold font-grey-cascade" data-bind="text: Countdown() ? 'En curso' : (Timeleft() ? Timeleft() : 'Finalizada:')"></div>

                                <div class="mt-step-content font-grey-cascade" data-bind="text: Countdown() ? 'La cuenta regresiva ha sido desactivada para este concurso.' : (Timeleft() ? 'Tiempo hasta comenzar subasta' : 'La subasta ha finalizado.')"></div>
                                <!-- /ko -->
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
                                <td data-bind="text: Countdown() && VerNumOferentesParticipan() ? (Conectados() + '/' + CantidadOferentes()) : 'No disponible'">
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
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>