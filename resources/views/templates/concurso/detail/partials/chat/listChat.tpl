<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Muro de consultas</h4>
            <div class="portlet light bordered bg-inverse">
                <h6 class="block bold" style="margin-top: 0; padding-top: 0;">Filtrar mensajes</h6>
                <div class="row text-left">
                    <div class="col-md-12">
                        <form class="form-inline">
                            <div class="col-md-3 form-group">
                                <label for="filters_tipo" class="control-label">
                                    Tipo de pregunta
                                </label>
                                <select class="form-control" id="filters_tipo"
                                    data-bind="value:TipoSelected, options: Tipos, valueAllowUnset: true, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...', allowClear: true }">
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="filters_categoria" class="control-label">
                                    Respondidas
                                </label>
                                <select class="form-control" id="filters_categoria"
                                    data-bind="value:CategoriaSelected, options: Categorias, valueAllowUnset: true, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...', allowClear: true }">
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="filters_estado" class="control-label">
                                    Estado
                                </label>
                                <select class="form-control" id="filters_estado"
                                    data-bind="value:EstadoSelected, options: Estados, valueAllowUnset: true, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...', allowClear: true }">
                                </select>
                            </div>
                            <!-- ko if: User.can('chat-admin') -->
                            <div class="col-md-3 form-group">
                                <label for="filters_estado" class="control-label">
                                    Mensajes
                                </label>
                                <select class="form-control" id="filters_estado"
                                    data-bind="value:MensajeProvSelected, options: MensajesPorProv, valueAllowUnset: true, optionsText: 'text', optionsValue: 'id', select2: { placeholder: 'Seleccionar...', allowClear: true }">
                                </select>
                            </div>
                            <!-- /ko -->
                        </form>
                    </div>
                    <div class="col-md-12 text-right" style="margin-top: 20px;">
                        <button class="btn red" data-bind="click: cleanFilters">Limpiar filtros
                        </button>
                        <button class="btn blue" data-bind="click: exportMuro">Descargar Preguntas
                        </button>
                    </div>
                </div>
            </div>

            <div class="portlet light bordered bg-inverse">
                <h5 class="block bold" style="margin-top: 0; padding-top: 0;">Consultas y Comentarios</h5>

            <!-- ko if: ChatEnable() || IsClient() -->
            <div class="row" style="margin: 15px 0;">
                <div class="col-md-12 text-right">
                    <div class="form-inline">
                        <input type="text" class="form-control" placeholder="Buscar en preguntas..."
                            data-bind="textInput: searchMessage" style="margin-right: 10px;" />
                        <button data-title="Nuevo mensaje" class="btn green compose-btn"
                            data-bind="click: NewChat.bind($data, showNewChat())">
                            <i class="fa fa-edit"></i> Nuevo Mensaje
                        </button>
                    </div>
                </div>
            </div>
            <!-- /ko -->


                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table  table-striped table-advance table-hover table-condensed inbox">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Pregunta/comentario</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Respondida</th>
                                    <th>Estado</th>
                                    <!-- ko if: User.can('chat-admin') -->
                                    <th>De</th>
                                    <th>Para</th>
                                    <!-- /ko -->
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- 🔁 Se cambió 'Messages' por 'filteredMessages' -->
                                <!-- ko foreach: filteredMessages -->
                                <tr data-bind="css: !leido() || !answerLeido() ? 'unread':'', click: $parent.Chat.bind($data, id())" style="cursor: pointer">
                                    <td class="inbox-small-cells" data-bind="text: id()"></td>
                                    <td class="inbox-small-cells" data-bind="text: mensaje()"
                                        style="max-width: 100px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    </td>
                                    <td class="inbox-small-cells" data-bind="text: tipo_pregunta()"></td>
                                    <td class="inbox-small-cells" data-bind="text: fecha()"></td>
                                    <td class="inbox-small-cells" data-bind="text: respondida()"></td>
                                    <td class="inbox-small-cells"
                                        data-bind="text: estado() == 1 ? 'Aprobada': (estado() == 3 ? 'Rechazada' : 'Por Aprobar')"></td>
                                    <!-- ko if: User.can('chat-admin') -->
                                    <td class="inbox-small-cells" data-bind="text: remitente()"></td>
                                    <td class="inbox-small-cells" data-bind="text: to()"></td>
                                    <!-- /ko -->
                                    <td class="inbox-small-cells" data-bind="text: tipo_name()"></td>
                                </tr>
                                <!-- /ko -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
