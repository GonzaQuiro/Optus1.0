var AppOptus = function() {
    this.isLoaded = ko.observable(false);

    var init = function() {};
    var bind = function(Obj) {
        if (!window.File && !window.FileReader && !window.FileList && !window.Blob) {
            swal('Alerta!', 'Su navegador no tiene soporte para funciones de HTML5. Por favor actualice su navegador.', 'error');
        } else {
            ko.applyBindings(Obj);
            self.isLoaded(true);
            // iCheck pierde el evento 'hover' en los input.icheck que estén
            // anidados en un elemento que tenga bindeado un loop de Knockout.
            // Se soluciona iniciando iCheck después de ko.applyBindings().
            //App.handleiCheck();
        }
    };
    return {
        Init: init,
        Bind: bind
    };
}();

var UserLogin = function() {
    var self = this;

    this.UserName = ko.observable();
    this.Password = ko.observable();

    this.Login = function() {
        const data = {
            UserName: self.UserName(),
            Password: self.Password()
        };
        Services.Post('/login/send', data,
            (response) => {
                if (response.success) {
                    if (response.data.user.RequiresIpVerification == 'S') {
                        window.location.href = '/verify-code-advice';
                    } else if (response.data.user.PassChange == 'S') {
                        window.location.href = '/verify-code-advice';
                    } else {
                        User.SetValues(response.data.user);
                        setCookie('customer_company_id', response.data.user.customer_company_id, 7);
                        window.location.href = '/dashboard';
                    }
                } else {
                    swal('Error', response.message, 'error');
                }
            },
            (error) => {
                swal('Error', error.message, 'error');
            },
            null,
            null
        );
    };
    
    this.LoginAD = async function() {
        swal({
            title: "Seleccione una opción",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "AD TLC",
            cancelButtonText: "AD LG",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                window.location.href = '/ad/login/TLC';
            } else {
                window.location.href = '/ad/login/LG';
            }
        });
    };
    
    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }
};

window.onload = function() {
    const data = localStorage.getItem('userdata')
    if (data) {
        localStorage.removeItem('userdata');
        const user = JSON.parse(data);
        User.SetValues(user);
        setCookie('customer_company_id', user.customer_company_id, 7);
        window.location.href = '/dashboard';
    }
};

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

var User = function() {
    var _default = {
        Token: '',
        Id: '',
        Nombre: '',
        Apellido: '',
        FullName: '',
        Image: '',
        Email: '',
        Tipo: '',
        PassChange: '',
        Permissions: [],
        isAdmin: false,
        isCustomer: false,
        isOfferer: false
    };

    var SetValue = function(data) {
        _default = $.extend(_default, data);
        localStorage.setItem('User', JSON.stringify(_default));
    };

    var getData = function() {
        if (localStorage.getItem('User') === null) {
            return {};
        }
        return JSON.parse(localStorage.getItem('User'));
    };

    var logOut = function() {
        Services.Post('/logout', {
                UserToken: User.Token
            },
            (response) => {
                if (response.success) {
                    localStorage.clear();
                    window.location.href = '/login';
                } else {
                    swal('Error.', response.message, 'error');
                }
            },
            (error) => {
                swal('Error.', error.message, 'error');
            },
            null,
            null
        );
    };

    var can = (permission) => {
        var permissions = getData().Permissions;
        return typeof permissions != 'undefined' && permissions.length > 0 ? permissions.some(p => p === permission) : false;
    }

    var cannot = (permission) => {
        var permissions = getData().Permissions;
        return typeof permissions != 'undefined' && permissions.length > 0 ? !permissions.some(p => p === permission) : true;
    }

    return {
        Token: getData().Token,
        Id: getData().Id,
        Nombre: getData().Nombre,
        Apellido: getData().Apellido,
        FullName: getData().FullName,
        Image: getData().Image,
        Email: getData().Email,
        Tipo: getData().Tipo,
        PassChange: getData().PassChange,
        isAdmin: getData().isAdmin,
        isCustomer: getData().isCustomer,
        isOfferer: getData().isOfferer,
        SetValues: SetValue,
        LogOut: logOut,
        can: can,
        cannot: cannot
    };
}();