var Services = function() {
    var servicesPath = '/services/';
    var execute = function(type, serviceName, data, functionSuccess, functionError, customService, pTimeout) {
        serviceName = customService !== null ? customService + serviceName : servicesPath + serviceName;
        if (pTimeout === null) {
            pTimeout = 30000;
        }
        $.ajax({
            url: serviceName,
            type: type,
            data: ko.toJS(data),
            //dataType: "json",
            timeout: pTimeout
        }).done(function(data) {
            if (functionSuccess !== null) {
                functionSuccess(data);
            }
        }).fail(function(obj, textStatus) {
            if (functionError === null) {
                if (obj.responseText.trim() === "") return;
                var responseText = JSON.parse(obj.responseText);
                if (responseText["Error"] === 403) {
                    window.location.href = "/login";
                }
                swal('Error.', 'Ha ocurrido un inconveniente al conectar a los servicio, intentar nuevamente!', "error");
            } else {
                functionError(obj, textStatus);
            }
        });
    };

    var get = function(path, data, functionSuccess, functionError, extras = null) {
        var configuration = {
            url: path,
            type: 'GET',
            data: ko.toJS(data),
        };

        if (!extras) {
            extras = {
                dataType: 'json'
            };
        }

        configuration = {...configuration, extras };

        $.ajax(
            configuration
        ).done(function(data) {
            if (functionSuccess !== null) {
                functionSuccess(data);
            }
        }).fail(function(obj, textStatus, error) {
            console.log('GET fail', 'path: ' + path, 'status: ' + obj.status, 'error: ' + error);

            switch (obj.status) {
                case 403:
                    window.location.href = '/login';
                    break;
            }

            if (functionError === null) {
                swal('Error', 'Ha ocurrido un error al acceder al servicio.', 'error');
            } else {
                var message =
                    typeof obj.message != 'undefined' ?
                    obj.message :
                    (
                        typeof obj.responseJSON != 'undefined' ?
                        obj.responseJSON.message :
                        'Ha ocurrido un error al acceder al servicio.'
                    );

                functionError({ message: message }, textStatus);
            }
        });
    };

    var post = function(path, data, functionSuccess, functionError, extras = null) {
        var configuration = {
            url: path,
            type: 'POST',
            data: ko.toJS(data),
        };

        if (!extras) {
            extras = {
                dataType: 'json'
            };
        }

        configuration = {...configuration, extras };

        $.ajax(
            configuration
        ).done(function(data) {
            if (functionSuccess !== null) {
                functionSuccess(data);
            }
        }).fail(function(obj, textStatus, error) {
            console.log('POST fail', 'path: ' + path, 'status: ' + obj.status, 'error: ' + error);
            var responseText = null;
            try {
                responseText = JSON.parse(obj.responseText);
            } catch (e) {}

            switch (obj.status) {
                case 403:
                    window.location.href = '/login';
                    break;
            }

            if (functionError === null) {
                swal('Error', 'Ha ocurrido un error al acceder al servicio.', 'error');
            } else {
                var message =
                    typeof obj.message != 'undefined' ?
                    obj.message :
                    (
                        typeof obj.responseJSON != 'undefined' ?
                        obj.responseJSON.message :
                        'Ha ocurrido un error al acceder al servicio.'
                    );
                functionError({ message: message }, textStatus);
            }
        });
    };

    var patch = function(path, data, functionSuccess, functionError, extras = null) {
        var configuration = {
            url: path,
            type: 'PATCH',
            data: ko.toJS(data),
        };

        if (!extras) {
            extras = {
                dataType: 'json'
            };
        }

        configuration = {...configuration, extras };

        $.ajax(
            configuration
        ).done(function(data) {
            if (functionSuccess !== null) {
                functionSuccess(data);
            }
        }).fail(function(obj, textStatus, error) {
            console.log('Patch fail', 'path: ' + path, 'status: ' + obj.status, 'error: ' + error);
            var responseText = null;
            try {
                responseText = JSON.parse(obj.responseText);
            } catch (e) {}

            switch (obj.status) {
                case 403:
                    window.location.href = '/login';
                    break;
            }

            if (functionError === null) {
                swal('Error', 'Ha ocurrido un error al acceder al servicio.', 'error');
            } else {
                var message =
                    typeof obj.message != 'undefined' ?
                    obj.message :
                    (
                        typeof obj.responseJSON != 'undefined' ?
                        obj.responseJSON.message :
                        'Ha ocurrido un error al acceder al servicio.'
                    );
                functionError({ message: message }, textStatus);
            }
        });
    };

    return {
        Execute: execute,
        Get: get,
        Post: post,
        Patch: patch
    };
}();