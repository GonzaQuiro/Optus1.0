(function() {

    var toMoney = function(num) {
        return '$' + (num.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'));
    };

    var handler = function(element, valueAccessor, allBindings) {
        var $el = $(element);
        var method;

        // Gives us the real value if it is a computed observable or not
        var valueUnwrapped = ko.unwrap(valueAccessor());

        if ($el.is(':input')) {
            method = 'val';
        } else {
            method = 'text';
        }
        return $el[method](toMoney(valueUnwrapped));
    };

    ko.bindingHandlers.money = {
        update: handler
    };
})();

ko.bindingHandlers.number = {
    update: function(element, valueAccessor, allBindingsAccessor) {
        var defaults = ko.bindingHandlers.number.defaults,
            aba = allBindingsAccessor,
            unwrap = ko.utils.unwrapObservable,
            value = unwrap(valueAccessor()) || valueAccessor(),
            result = '',
            numarray;

        var separator = unwrap(aba().separator) || defaults.separator,
            decimal = unwrap(aba().decimal) || defaults.decimal,
            precision = unwrap(aba().precision) || defaults.precision,
            symbol = unwrap(aba().symbol) || defaults.symbol,
            after = unwrap(aba().after) || defaults.after;

        value = parseFloat(value) || 0;

        if (precision > 0)
            value = value.toFixed(precision)

        numarray = value.toString().split('.');

        for (var i = 0; i < numarray.length; i++) {
            if (i == 0) {
                result += numarray[i].replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1' + separator);
            } else {
                result += decimal + numarray[i];
            }
        }

        result = (after) ? result += symbol : symbol + result;

        ko.bindingHandlers.text.update(element, function() { return result; });
    },
    defaults: {
        separator: '.',
        decimal: ',',
        precision: 2,
        symbol: '',
        after: false
    }
};