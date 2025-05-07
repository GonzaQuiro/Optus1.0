var ComponentsEditors = function () {

    var handleWysihtml5 = function () {
        if (!jQuery().wysihtml5) {
            return;
        }

        if ($('.wysihtml5').size() > 0) {
            $('.wysihtml5').wysihtml5({
                "stylesheets": ["../assets/global/plugins/bootstrap-wysihtml5/wysiwyg-color.css"]
            });
        }
    }

    var handleSummernote = function () {
        ko.bindingHandlers.summernote = {
            init: function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
                var allBindings = allBindingsAccessor();
                var limiteCaracteres = allBindings.summernote.limit ? allBindings.summernote.limit : 5000;
                // initialize summernote with config from binding
                var toolbar = {
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline']],
                        ['font', ['strikethrough', 'superscript', 'subscript']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        //['table', ['table']],
                        ['insert', ['table', 'link', 'picture', 'hr']],
                        ['insert', ['codeview', 'fullscreen']],
                    ]
                };
                var summernoteConfig = ko.utils.unwrapObservable(allBindings.summernote);
                var callbacks = {
                    callbacks: {
                        onPaste: function (e) {
                            document.queryCommandSupported("insertText");
                            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                            
                            setTimeout(function () {
                                document.execCommand('insertText', false, bufferText);
                            }, 9);
                            e.preventDefault(e);
                        },
                        onChange: function(content) {
                            var lastContent = $(this).summernote()[0].value;

                            if (content && content.length > limiteCaracteres) {
                                $(this).summernote('code', lastContent);
                                allBindings.value(lastContent);
                            } else {
                                allBindings.value(content);
                            }
                        },
                    }
                },            
                summernoteConfig = (typeof summernoteConfig === 'object') ? summernoteConfig : {}
                var options = $.extend(
                    toolbar, 
                    summernoteConfig,
                    callbacks
                );
                $(element).summernote(options);
        
                $(element).summernote('code', allBindings.value());
                summernoteText = allBindings.value();
            }
        };
        //API:
        //var sHTML = $('#summernote_1').code(); // get code
        //$('#summernote_1').destroy(); // destroy
    }

    return {
        //main function to initiate the module
        init: function () {
            handleWysihtml5();
            handleSummernote();
        }
    };

}();

jQuery(document).ready(function () {
    ComponentsEditors.init();
});