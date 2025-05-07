<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9" style="display: block;">Descripción</label>
            <div class="mt-element-ribbon">
                <div class="ribbon ribbon-right ribbon-shadow ribbon-round ribbon-color-success"
                    data-bind="text: (Entity.DescripcionDescription() ? Entity.DescripcionDescription().length : 0) + '/' + Entity.DescriptionLimit()">
                </div>
                <textarea class="form-control" required data-bind="value: Entity.DescripcionDescription, summernote: {
                    height: 250,
                    disableDragAndDrop: true,
                    shortcuts: false,
                    dialogsInBody: false,
                    dialogsFade: false,
                    limit: Entity.DescriptionLimit()
                }">
                </textarea>
            </div>

        </div>
    </div>

    <div class="col-md-12">
        <!-- ko if: IsSobrecerrado() || IsOnline() -->
        <div class="row" style="margin: 0; padding: 0;">
            <div class="col-md-12">
                <div class="form-group" data-bind="validationElement: Entity.DescripcionImagePath">
                    <label class="control-label visible-ie8 visible-ie9" style="display: block;">Imagen / Icono</label>

                    <input id="input-700" data-bind="fileinput: Entity.DescripcionPortrait, fileinputOptions: {
                        uploadUrl: '/media/file/upload',
                        initialCaption: Entity.DescripcionPortrait().filename() ? Entity.DescripcionPortrait().filename() : [],
                        uploadExtraData: {
                            UserToken: User.Token,
                            path: Entity.DescripcionImagePath(),
                        },
                        initialPreview: Entity.DescripcionPortrait().filename() ? [Entity.DescripcionImagePath() + Entity.DescripcionPortrait().filename()] : [],
                        allowedFileExtensions: ['jpg', 'jpeg', 'png']
                    }" name="file[]" type="file">


                </div>
            </div>
        </div>
        <!-- /ko -->
    </div>
</div>