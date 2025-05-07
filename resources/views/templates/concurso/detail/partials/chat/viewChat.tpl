<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Muro de consultas</h4>
            <div class="portlet light bordered bg-inverse">
                <div class="col-md-10 text-left">
                    <h5 class="block bold" data-bind="text: MessageSelected().mensaje()"></h5>
                    <!-- ko if: User.cannot('chat-admin') -->
                    <!-- ko if: MessageSelected().estado() == 2 -->
                    <span class="badge badge-warning">Comentario pendiente de
                        aprobación</span>
                    <!-- /ko -->
                    <!-- ko if: MessageSelected().estado() == 0 -->
                    <span class="badge badge-danger">Comentario rechazado</span>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko if: User.can('chat-admin') -->
                    <!-- ko if: !MessageSelected().is_admin() && MessageSelected().estado() == 2 -->
                    <div class="text-right">
                        <a class="btn btn-xs green" data-bind="click: approveOrReject.bind($data,
                        MessageSelected().id(), 'approve')">
                            Aprobar
                        </a>
                        <a class="btn btn-xs red" data-bind="click: approveOrReject.bind($data,
                        MessageSelected().id(), 'reject')">
                            Rechazar
                        </a>
                    </div>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko if:  MessageSelected().filename().filename() -->
                    <a data-bind="click: downloadAdj.bind($data, MessageSelected().filename().filename(), 'concurso', MessageSelected().concurso())"
                        download class="btn btn-xl green" title="Descargar">
                        Descargar adjunto
                        <i class="fa fa-download"></i>
                    </a>
                    <!-- /ko -->
                </div>
                <div class="col-md-2 text-right">
                    <button data-title="Volver a la lista" class="btn green compose-btn"
                        data-bind="click: ListChat.bind($data, showNewChat())">
                        <i class="fa fa-list"></i> Volver a la lista </button>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="slimScrollDiv"
                            style="position: relative; overflow: hidden; width: auto; height: 500px; overflow-y: scroll">
                            <div class="scroller" overflow: hidden; width: auto; data-always-visible="1"
                                data-rail-visible1="1" data-initialized="1">
                                <ul class="chats" style="width: 98%;"
                                    data-bind="foreach: { data: MessageSelected().respuestas() }">
                                    <li data-bind="css: UserId() == {$smarty.session['user_id']} ? 'out' : 'in'">
                                        <img class="avatar" data-bind="attr: {
                                    src: usuario_imagen
                                }">
                                        <div class="message"
                                            data-bind="style: {literal}{background: UserId() == {/literal}{$smarty.session['user_id']}{literal} ? '#FFAAAA' : '#FAFAFA'}{/literal}">
                                            <span class="arrow"> </span>
                                            <!-- ko if: User.can('chat-admin') -->
                                            <span href="/usuario" class="name" data-bind="text: usuario()"></span>
                                            <!-- /ko -->
                                            <span class="datetime" data-bind="text: fecha()"> </span><br>
                                            <span class="badge badge-info" data-bind="text: tipo_name()"></span>

                                            <span class="body text-left"
                                                data-bind="text: estado() === 0 || estado() === 1 || estado() === 2 || User.can('chat-admin') ? mensaje() : '&nbsp;'"></span>

                                            <!-- ko if: User.cannot('chat-admin') -->
                                            <!-- ko if: estado() == 2 -->
                                            <span class="badge badge-warning">Comentario pendiente de
                                                aprobación</span>
                                            <!-- /ko -->
                                            <!-- ko if: estado() == 0 -->
                                            <span class="badge badge-danger">Comentario rechazado</span>
                                            <!-- /ko -->
                                            <!-- /ko -->

                                            <!-- ko if: User.can('chat-admin') -->
                                            <!-- ko if: !is_admin() && estado() == 2 -->
                                            <div class="text-right">
                                                <a class="btn btn-xs green" data-bind="click: $parent.approveOrReject.bind($data,
                                                    id(), 'approve')">
                                                    Aprobar
                                                </a>
                                                <a class="btn btn-xs red" data-bind="click: $parent.approveOrReject.bind($data,
                                                    id(), 'reject')">
                                                    Rechazar
                                                </a>
                                            </div>
                                            <!-- /ko -->
                                            <!-- /ko -->
                                            <!-- ko if:  filename().filename() -->
                                            <a data-bind="click: $parent.downloadAdj.bind($data, filename().filename(), 'concurso', concurso())"
                                                download class="btn btn-xl green" title="Descargar">
                                                Descargar adjunto
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <!-- /ko -->
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="slimScrollBar"></div>
                            <div class="slimScrollRail"></div>
                        </div>
                    </div>
                    <!-- ko if: ChatEnable() || IsClient() -->
                    <div class="col-md-12">
                        <div class="chat-form">
                            <textarea class="form-control msjText" placeholder="Escriba su mensaje aquí..."
                                data-bind="textInput: NewResp().message" rows="3" style="resize: none;">
                            </textarea>

                            <div style="display:flex; margin-top: 1em; justify-content: end">
                                <input id="input-700" data-bind="fileinput: NewResp().file, fileinputOptions: {
                                    uploadUrl: '/media/file/upload',
                                    initialCaption: NewResp().file().filename() ? NewResp().file().filename() : [],
                                    uploadExtraData: {
                                        UserToken: User.Token,
                                        path: Path(),
                                    },
                                    initialPreview: NewResp().file().filename() ? [Path() + NewResp().file().filename()] : [],
                                }" name="file[]" type="file">

                                <button class="btn blue icn-only" title="Enviar"
                                    data-bind="click: sendMessage.bind($data, MessageSelected().id), disable: !NewResp().message()">
                                    <i class="fa fa-check icon-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                     <!-- /ko -->
                </div>
            </div>
        </div>
    </div>
</div>