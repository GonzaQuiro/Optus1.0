//<input type="hidden" data-bind="value=Id, select2: { placeholder: AppResources.Select, minimumInputLength: 1, url: "", allowClear: true, dataFunction: null,functionFormatResult: function (item),functionFormatSelection: function, text: "" }" />
ko.bindingHandlers.select2 = {
    init: function (el, valueAccessor, allBindingsAccessor, viewModel) {

        $.fn.select2.defaults.set("theme", "bootstrap");
        var _default = {
            placeholder: "Seleccionar",
            minimumInputLength: 4,
            url: "",
            allowClear: true,
            dataFunction: null,
            functionFormatResult: null,
            functionFormatSelection: null,
            text: "",
            width: 'auto',
            language: 'es',
            rol: "",
            org: "",
            esAdmin: ""
        }
        ko.utils.domNodeDisposal.addDisposeCallback(el, function () {
            $(el).select2('destroy');
        });

        var allBindings = allBindingsAccessor();
        var select2 = ko.utils.unwrapObservable(allBindings.select2);
        select2 = $.extend(_default, select2)
        $(el).select2({
            ajax: {
                url: Services.ServicePrincipal + select2.url,
                dataType: 'json',
                type: 'GET',
                data: function (params) {
                    if (select2.dataFunction != null)
                        return select2.dataFunction(params.term);
                    return (select2.rol != null) ? {term: params.term, rol: select2.rol, org: select2.org, esAdmin: select2.esAdmin} : {term: params.term};
                },
                processResults: function (data, page) {
                    return {results: data.List};
                },

                headers: {Authorization: User.Hash},
            },
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            allowClear: select2.allowClear,
            placeholder: select2.placeholder,
            minimumInputLength: select2.minimumInputLength,
            width: select2.width,
            language: select2.language,
            templateResult: function (item) {
                if (item.loading)
                    return item.text;
                return select2.functionFormatResult == null ? item.text : select2.functionFormatResult(item);
                return item.Cli_Id + " - " + item.Cli_Nombre;
            },
            templateSelection: function (item) {
                allBindings.value(item.id);
                select2.text(item.text);
                return select2.functionFormatSelection == null ? "<p>" + item.text + "</p>" : select2.functionFormatSelection(item);
            }
        });
        if (allBindings.value() != null && allBindings.value() != "") {
            $(el).select2("val", {id: allBindings.value(), text: select2.text()});
        }
    },
    update: function (el, valueAccessor, allBindingsAccessor, viewModel) {
        var allBindings = allBindingsAccessor();
        var select2 = ko.utils.unwrapObservable(allBindings.select2);
        if ("value" in allBindings) {
            if ($(el).select2("val") != allBindings.value())
                $(el).select2("val", {id: allBindings.value(), text: select2.text()});
        }
    }
};

//<select multiple data-bind="selectPicker: teamID, optionsText: 'text', optionsValue : 'id', selectPickerOptions: { optionsArray: teamItems }"></select>
//data-bind="selectPicker: BSteamMemberID, optionsText: 'text', optionsValue : 'id', value: KOteamMemberID, selectPickerOptions: { optionsArray: teamItems }"
ko.bindingHandlers.selectPicker = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var value = valueAccessor();
        if ($(element).is('select')) {
            var selectPickerOptions = allBindingsAccessor().selectPickerOptions;
            if (typeof selectPickerOptions !== 'undefined' && selectPickerOptions !== null) {
                $(element).attr('title', selectPickerOptions.optionsCaption);
                var options = ko.unwrap(selectPickerOptions.optionsArray),
                        isDisabled = selectPickerOptions.disabledCondition || false,
                        resetOnDisabled = selectPickerOptions.resetOnDisabled || false;
                if (options.length > 0) {
                    var ops = options;
                    $(element).html('');
                    for (var i in ops) {
                        var selected = ops[i].id == value() ? " selected " : "";
                        $(element).append("<option value='" + ops[i].id + "'" + selected + ">" + ops[i].text + "</option>");
                    }
                }
                if (isDisabled && resetOnDisabled) {
                    // the dropdown is disabled and we need to reset it to its first option
                    $(element).selectpicker('val', $(element).children('option:first').val());
                }
                $(element).prop('disabled', isDisabled);
            }
            $(element).on('change', function () {
                if ($(element).prop('multiple')) {
                    value = new ko.observableArray();
                    var text = [];
                    $(this).find('option:selected').each(function (index, element) {
                        value.push($(this).val());
                        text.push($(this).text());
                    });
                    if (typeof selectPickerOptions.propertyText !== 'undefined' && selectPickerOptions.propertyText !== null) {
                        selectPickerOptions.propertyText(text.join());
                    }
                } else {
                    value($(this).find('option:selected').val());
                    if (typeof selectPickerOptions.propertyText !== 'undefined' && selectPickerOptions.propertyText !== null) {
                        selectPickerOptions.propertyText($(this).find('option:selected').text());
                    }
                }
                if (selectPickerOptions.functiononchange != undefined && selectPickerOptions.functiononchange != null) {
                    selectPickerOptions.functiononchange();
                }
            });
            if ($(element).prop('multiple')) {
                // in the case of a multiple select where the valueAccessor() is an observableArray, call the default Knockout selectedOptions binding
                ko.bindingHandlers.selectedOptions.init(element, valueAccessor, allBindingsAccessor);
            } else {
                // regular select and observable so call the default value binding
                ko.bindingHandlers.value.init(element, valueAccessor, allBindingsAccessor);
            }

            $(element).addClass('selectpicker').selectpicker({size: 7});
            //$(element).selectpicker('val', valueAccessor());
        }
    },
    update: function (element, valueAccessor, allBindingsAccessor) {
        var value = valueAccessor();
        if ($(element).is('select')) {
            var selectPickerOptions = allBindingsAccessor().selectPickerOptions;
            if (typeof selectPickerOptions !== 'undefined' && selectPickerOptions !== null) {
                $(element).attr('title', selectPickerOptions.optionsCaption);
                var options = ko.unwrap(selectPickerOptions.optionsArray),
                        isDisabled = selectPickerOptions.disabledCondition || false,
                        resetOnDisabled = selectPickerOptions.resetOnDisabled || false;
                if (options.length > 0) {
                    var ops = options;
                    $(element).html('');
                    for (var i in ops) {
                        var selected = ops[i].id == value() ? " selected " : "";
                        $(element).append("<option value='" + ops[i].id + "' " + selected + ">" + ops[i].text + "</option>");
                    }
                }
                if (isDisabled && resetOnDisabled) {
                    // the dropdown is disabled and we need to reset it to its first option
                    $(element).selectpicker('val', $(element).children('option:first').val());
                }
                $(element).prop('disabled', isDisabled);
            }
            if ($(element).prop('multiple')) {
                // in the case of a multiple select where the valueAccessor() is an observableArray, call the default Knockout selectedOptions binding
                ko.bindingHandlers.selectedOptions.update(element, valueAccessor, allBindingsAccessor);
            } else {
                // regular select and observable so call the default value binding
                ko.bindingHandlers.value.update(element, valueAccessor, allBindingsAccessor);
            }
            //$(element).selectpicker('val', valueAccessor());
            $(element).selectpicker('refresh');
        }
    }
};

//<select multiple data-bind="selectPicker: teamID, optionsText: 'text', optionsValue : 'id', selectPickerOptions: { optionsArray: teamItems }"></select>
//data-bind="selectPicker: BSteamMemberID, optionsText: 'text', optionsValue : 'id', value: KOteamMemberID, selectPickerOptions: { optionsArray: teamItems }"
ko.bindingHandlers.select2local = {
    init: function (el, valueAccessor, allBindingsAccessor) {
        ko.utils.domNodeDisposal.addDisposeCallback(el, function () {
            $(el).select2('destroy');
        });
        var formatResult = function (item) {
            return '<span>' + item.text + '</span>';
        };
        var formatSelection = function (item) {
            return '<span>' + item.text + '</span>';
        };

        if (valueAccessor().formatResult != null && valueAccessor().formatResult !== 'undefined')
            formatResult = valueAccessor().formatResult;
        if (valueAccessor().formatSelection != null && valueAccessor().formatSelection !== 'undefined')
            formatSelection = valueAccessor().formatSelection;

        var allBindings = allBindingsAccessor(),
                select2 = ko.toJS(allBindings.select2local);
        select2.formatResult = formatResult;
        select2.formatSelection = formatSelection;

        $(el).select2(select2);
        $(el).select2("val", ko.utils.unwrapObservable(allBindings.value()).toString().split(","));

    },
    update: function (el, valueAccessor, allBindingsAccessor) {
        var allBindings = allBindingsAccessor();

        if ("value" in allBindings) {
            if (allBindings.select2local.multiple && allBindings.value().constructor != Array) {
                $(el).select2("val", allBindings.value().split(","));
            } else {
                $(el).select2("val", allBindings.value());
            }
        } else if ("selectedOptions" in allBindings) {
            var converted = [];
            var textAccessor = function (value) {
                return value;
            };
            if ("optionsText" in allBindings) {
                textAccessor = function (value) {
                    var valueAccessor = function (item) {
                        return item;
                    }
                    if ("optionsValue" in allBindings) {
                        valueAccessor = function (item) {
                            return item[allBindings.optionsValue];
                        }
                    }
                    var items = $.grep(allBindings.options(), function (e) {
                        return valueAccessor(e) == value
                    });
                    if (items.length == 0 || items.length > 1) {
                        return "UNKNOWN";
                    }
                    return items[0][allBindings.optionsText];
                }
            }
            $.each(allBindings.selectedOptions(), function (key, value) {
                converted.push({id: value, text: textAccessor(value)});
            });
            $(el).select2("data", converted);
        }
    }
};
//<select multiple data-bind="selectPicker: teamID, optionsText: 'text', optionsValue : 'id', selectPickerOptions: { optionsArray: teamItems }"></select>
//data-bind="selectPicker: BSteamMemberID, optionsText: 'text', optionsValue : 'id', value: KOteamMemberID, selectPickerOptions: { optionsArray: teamItems }"
ko.bindingHandlers.selectPickerGrouped = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var value = valueAccessor();
        if ($(element).is('select')) {
            var selectPickerOptions = allBindingsAccessor().selectPickerOptions;
            if (typeof selectPickerOptions !== 'undefined' && selectPickerOptions !== null) {
                $(element).attr('title', selectPickerOptions.optionsCaption);
                var options = selectPickerOptions.optionsArray,
                        isDisabled = selectPickerOptions.disabledCondition || false,
                        resetOnDisabled = selectPickerOptions.resetOnDisabled || false;
                if (options.length > 0) {
                    var ops = options;
                    // call the default Knockout options binding
                    $(element).html('');
                    for (var i in ops) {
                        if (ops[i].items != null && ops[i].items.length > 0) {
                            $(element).append("<optgroup value='" + ops[i].id + "' label='" + ops[i].text + "'></optgroup>");
                            for (var j in ops[i].items) {
                                $(element).find("optgroup[value='" + ops[i].id + "']").append("<option value='" + ops[i].items[j].id + "'>" + ops[i].items[j].text + "</option>");
                            }
                        } else {
                            $(element).append("<option value='" + ops[i].id + "'>" + ops[i].text + "</option>");
                        }
                    }
                }
                if (isDisabled && resetOnDisabled) {
                    // the dropdown is disabled and we need to reset it to its first option
                    $(element).selectpicker('val', $(element).children('option:first').val());
                }
                $(element).prop('disabled', isDisabled);
            }
            $(element).on('change', function () {
                if ($(element).prop('multiple')) {
                    value = new ko.observableArray();
                    var text = [];
                    $(this).find('option:selected').each(function (index, element) {
                        value.push($(this).val());
                        text.push($(this).text());
                    });
                    if (typeof selectPickerOptions.propertyText !== 'undefined' && selectPickerOptions.propertyText !== null) {
                        selectPickerOptions.propertyText(text.join());
                    }
                } else {
                    value($(this).find('option:selected').val());
                    if (typeof selectPickerOptions.propertyText !== 'undefined' && selectPickerOptions.propertyText !== null) {
                        selectPickerOptions.propertyText($(this).find('option:selected').text());
                    }
                }
                if (selectPickerOptions.functiononchange != undefined && selectPickerOptions.functiononchange != null) {
                    selectPickerOptions.functiononchange();
                }
            });
            if ($(element).prop('multiple')) {
                // in the case of a multiple select where the valueAccessor() is an observableArray, call the default Knockout selectedOptions binding
                ko.bindingHandlers.selectedOptions.init(element, valueAccessor, allBindingsAccessor);
            } else {
                // regular select and observable so call the default value binding
                ko.bindingHandlers.value.init(element, valueAccessor, allBindingsAccessor);
            }

            $(element).addClass('selectpicker').selectpicker({size: 7});
        }
    },
    update: function (element, valueAccessor, allBindingsAccessor) {
        if ($(element).is('select')) {
            var selectPickerOptions = allBindingsAccessor().selectPickerOptions;
            if (typeof selectPickerOptions !== 'undefined' && selectPickerOptions !== null) {
                $(element).attr('title', selectPickerOptions.optionsCaption);
                var options = selectPickerOptions.optionsArray,
                        isDisabled = selectPickerOptions.disabledCondition || false,
                        resetOnDisabled = selectPickerOptions.resetOnDisabled || false;
                if (options.length > 0) {
                    var ops = options;
                    // call the default Knockout options binding
                    $(element).html('');
                    for (var i in ops) {
                        if (ops[i].items != null && ops[i].items.length > 0) {
                            $(element).append("<optgroup value='" + ops[i].id + "' label='" + ops[i].text + "'></optgroup>");
                            for (var j in ops[i].items) {
                                $(element).find("optgroup[value='" + ops[i].id + "']").append("<option value='" + ops[i].items[j].id + "'>" + ops[i].items[j].text + "</option>");
                            }
                        } else {
                            $(element).append("<option value='" + ops[i].id + "'>" + ops[i].text + "</option>");
                        }
                    }
                }
                if (isDisabled && resetOnDisabled) {
                    // the dropdown is disabled and we need to reset it to its first option
                    $(element).selectpicker('val', $(element).children('option:first').val());
                }
                $(element).prop('disabled', isDisabled);
            }
            if ($(element).prop('multiple')) {
                // in the case of a multiple select where the valueAccessor() is an observableArray, call the default Knockout selectedOptions binding
                ko.bindingHandlers.selectedOptions.update(element, valueAccessor, allBindingsAccessor);
            } else {
                // regular select and observable so call the default value binding
                ko.bindingHandlers.value.update(element, valueAccessor, allBindingsAccessor);
            }
            $(element).selectpicker('refresh');
        }
    }
};

ko.bindingHandlers.multiselect = {
    init: function (element, valueAccessor, allBindingsAccessor) {
        var values = valueAccessor();
        var options = allBindingsAccessor().multiSelectOptions;
        var list = options.optionsArray;
        if ($(element).is('select')) {
            $(element).html("");
            for (var i in list) {
                var existe = "";
                for (var j in values()) {
                    if (values()[j] == list[i].id)
                        existe = "selected";
                }
                $(element).append("<option value='" + list[i].id + "' " + existe + ">" + list[i].text + "</option>")
            }
        }
        $(element).multiSelect({
            afterSelect: function (value) {
                var values = valueAccessor();
                values.push(value[0]);
            },
            afterDeselect: function (value) {
                var values = valueAccessor();
                values.remove(value[0]);
            }
        });
    },
    update: function (element, valueAccessor, allBindingsAccessor) {
        var values = valueAccessor();
        var options = allBindingsAccessor().multiSelectOptions;
        var list = options.optionsArray;
        if ($(element).is('select')) {
            $(element).html("");
            for (var i in list) {
                var existe = "";
                for (var j in values()) {
                    if (values()[j] == list[i].id)
                        existe = "selected";
                }
                $(element).append("<option value='" + list[i].id + "' " + existe + ">" + list[i].text + "</option>")
            }
        }
        $(element).multiSelect('refresh');
    }
};