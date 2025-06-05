<div class="row">
    <div class="col-sm-12">
        <div class="m-heading-1 border-default m-bordered text-left">
            <h4 class="block bold" style="margin-top: 0; padding-top: 0;">Muro de consultas</h4>
            <div class="portlet light bordered bg-inverse">
                <div class="row">
                    <div class="col-md-6 text-left">
                        <h5 class="block bold">Nueva Consulta</h5>
                    </div>
                    <div class="col-md-6 text-right">
                        <button data-title="Volver a la lista" class="btn green compose-btn"
                            data-bind="click: ListChat.bind($data, showNewChat())">
                            <i class="fa fa-list"></i> Volver a la lista </button>
                    </div>
                </div>
                <!-- ko if: IsClient() -->
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9"
                            style="display: block;">
                                Tipo de mensaje
                            </label>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="radio-inline">
                                <input name="MensajeIndividual" type="radio" value="si"
                                    data-bind="checked: MensajeIndividual, disable: !ChatEnable()"> Individual
                            </label>
                            <label class="radio-inline">
                                <input name="MensajeIndividual" type="radio" value="no"
                                    data-bind="checked: MensajeIndividual"> Grupal
                            </label>
                        </div>
                    </div>

                    <!-- ko if: MensajeIndividual() == 'si' -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="control-label visible-ie8 visible-ie9"
                                style="display: block;">Proveedores</label>
                            <select
                                data-bind="value: Proveedor, 
                                valueAllowUnset: true, 
                                options: Proveedores, 
                                optionsText: 'text', 
                                optionsValue: 'id', 
                                select2: { placeholder: 'Seleccionar...', allowClear: false}">
                            </select>
                        </div>
                    </div>
                    <!-- /ko -->
                </div>
                <!-- /ko -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="chat-form">
                            <textarea class="form-control msjText" placeholder="Escriba su mensaje aquí..."
                                data-bind="textInput: NewMessage().message" rows="15" style="resize: none;">
                                </textarea>

                            <div style="display:flex; margin-top: 1em; justify-content: end">
                                <input id="input-700" data-bind="fileinput: NewMessage().file, fileinputOptions: {
                                    uploadUrl: '/media/file/upload',
                                    initialCaption: NewMessage().file().filename() ? NewMessage().file().filename() : [],
                                    uploadExtraData: {
                                        UserToken: User.Token,
                                        path: Path(),
                                    },
                                    initialPreview: NewMessage().file().filename() ? [Path() + NewMessage().file().filename()] : [],
                                }" name="file[]" type="file">

                                <button class="btn blue icn-only" title="Enviar"
                                    data-bind="click: sendMessage.bind($data, 0), disable: !NewMessage().message()">
                                    <i class="fa fa-check icon-white"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>