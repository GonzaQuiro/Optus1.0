var ubicacion = new Array();
var ubicacionM = new Array();
let autocomplete;
let addressField;
let map;
let geocoder;

function getManPostionData(latitude, longitude) {
    var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=";
    var latlng = latitude + "," + longitude;
    var key = "&key=AIzaSyCUtr9Ist4jejEMf2czdImyxk_EXoyWBgo";
    $.getJSON(url + latlng + key, function (data) {
        ubicacion["numero"] = "";
        ubicacion["calle"] = "";
        ubicacion["localidad"] = "";
        ubicacion["departamento"] = "";
        ubicacion["provincia"] = "";
        ubicacion["pais"] = "";
        ubicacion["code"] = "";
        ubicacion["postalCode"] = "";
        var valor;
        var types;
        var codigoPais;

        if (data["status"] !== "ZERO_RESULTS") {

            $.each(data.results, function (key, val) {
                for (i = 0; i < $(val.address_components).size(); i++) {

                    types = val.address_components[i].types;
                    valor = val.address_components[i].long_name; //console.log(types + " = " + valor);
                    codigoPais = val.address_components[i].short_name;
                    if (types == "street_number")
                        ubicacion["numero"] = valor;
                    if (types == "route" && valor !== 'Unnamed Road')
                        ubicacion["calle"] = valor;
                    if (types == "locality,political")
                        ubicacion["localidad"] = valor;
                    if (types == "administrative_area_level_2,political")
                        ubicacion["departamento"] = valor;
                    if (types == "administrative_area_level_1,political")
                        ubicacion["provincia"] = valor;
                    if (types == "country,political") {
                        ubicacion["code"] = codigoPais.toUpperCase();
                        ubicacion["pais"] = valor;
                    }
                    if (types == "postal_code")
                        ubicacion["postalCode"] = valor;

                }
            });

        } else {
            ubicacion["numero"] = "";
            ubicacion["calle"] = "";
            ubicacion["localidad"] = "";
            ubicacion["departamento"] = "";
            ubicacion["provincia"] = "";
            ubicacion["pais"] = "";
            ubicacion["code"] = "";
            ubicacion["postalCode"] = "";
        }

        if (typeof E.Entity !== 'undefined') {
            E.Entity.Pais(ubicacion["pais"]);
            E.Entity.Provincia(ubicacion["provincia"]);
            E.Entity.Localidad(ubicacion["localidad"]);
            if(E.Entity.Direccion() === '' || E.Entity.ManOnTheMap()){
                E.Entity.Direccion(ubicacion["calle"] + " " + ubicacion["numero"]);
            }
            E.Entity.CountrySelected(ubicacion["code"]);
            E.Entity.Latitud(latitude);
            E.Entity.Longitud(longitude);
        } else {
            E.Pais(ubicacion["pais"]);
            E.Provincia(ubicacion["provincia"]);
            E.Localidad(ubicacion["localidad"]);
            E.Direccion(ubicacion["calle"] + " " + ubicacion["numero"]);
            E.CountrySelected(ubicacion["code"]);
            E.Latitud(latitude);
            E.Longitud(longitude);
        }
    });
}

function getMultiPostionData(id, latitude, longitude) {
    var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=";
    var latlng = latitude + "," + longitude;
    var key = "&key=AIzaSyD3xU2zO42h1qL1s6bFkHsdhtv_hpvfxBo";
    $.getJSON(url + latlng + key, function (data) {
        ubicacionM["numero"] = "";
        ubicacionM["calle"] = "";
        ubicacionM["localidad"] = "";
        ubicacionM["departamento"] = "";
        ubicacionM["provincia"] = "";
        ubicacionM["pais"] = "";
        var valor;
        var types;

        if (data["status"] !== "ZERO_RESULTS") {

            $.each(data, function (key, val) {
                for (i = 0; i < $(data["results"][0].address_components).size(); i++) {

                    types = data["results"][0].address_components[i].types;
                    valor = data["results"][0].address_components[i].long_name;

                    if (types == "street_number")
                        ubicacionM["numero"] = valor;
                    if (types == "route" && valor !== 'Unnamed Road')
                        ubicacionM["calle"] = valor;
                    if (types == "locality,political")
                        ubicacionM["localidad"] = valor;
                    if (types == "administrative_area_level_2,political")
                        ubicacionM["departamento"] = valor;
                    if (types == "administrative_area_level_1,political")
                        ubicacionM["provincia"] = valor;
                    if (types == "country,political")
                        ubicacionM["pais"] = valor;

                }
            });

        } else {
            ubicacionM["numero"] = "";
            ubicacionM["calle"] = "";
            ubicacionM["localidad"] = "";
            ubicacionM["departamento"] = "";
            ubicacionM["provincia"] = "";
            ubicacionM["pais"] = "";
        }

        var str = ubicacionM["pais"] + "," + ubicacionM["provincia"] + ',' + ubicacionM["localidad"];
        var lastChar = str.substr(str.length - 1);
        str = lastChar === ',' ? str.substr(0, str.length - 1) : str;
        $("#locacion" + id).val(str);

        E.Entity.Pais(ubicacionM["pais"]);
        E.Entity.Provincia(ubicacionM["provincia"]);
        E.Entity.Localidad(ubicacionM["localidad"]);
        E.Entity.Direccion(ubicacionM["calle"] + " " + ubicacionM["numero"]);
        E.Entity.Latitud(latitude);
        E.Entity.Longitud(longitude);
        /*
        E.Entity['Location' + id](ubicacion["pais"] + ',' + ubicacionM["provincia"] + ',' + ubicacionM["localidad"]);
        E.Entity['Latitud' + id](latitude);
        E.Entity['Longitud' + id](longitude);
        */
    });
}

function getManPosition(tipo, val, marker) {
    if (tipo === "event") {
        var latitude = val.latLng.lat();
        var longitude = val.latLng.lng();
        marker.setPosition(val.latLng);
        map.panTo(new google.maps.LatLng(latitude, longitude));
        getManPostionData(latitude, longitude);
    } else if (tipo === "position") {
        getManPostionData(val.lat(), val.lng());
    }
}

function getMultiPosition(tipo, val) {
    if (tipo === "event") {
        var latitude = val.latLng.lat();
        var longitude = val.latLng.lng();
        marker.setPosition(val.latLng);
        multimap.panTo(new google.maps.LatLng(latitude, longitude));
        getMultiPostionData(latitude, longitude);
    } else if (tipo === "position") {
        getMultiPostionData(val.lat(), val.lng());
    }
}

function handleNoGeolocation(errorFlag) {
    if (errorFlag) {
        var content = 'Error: El servicio de GeoLocalización falló ya que para que este servicio funcione correctamente debés permitir que el navegador localice tu ubicación.';
    } else {
        var content = 'Error: Su navegador no soporta la GeoLocalización.';
    }
    var optionsMan = {
        map: map,
        position: new google.maps.LatLng(-31.3987552, -64.1868587),
        content: content
    };

    var infowindow1 = new google.maps.InfoWindow(optionsMan);
    map.setCenter(optionsMan.position);

    var optionsMulti = {
        map: multimap,
        position: new google.maps.LatLng(-31.3987552, -64.1868587),
        content: content
    };

    var infowindow2 = new google.maps.InfoWindow(optionsMulti);
    multimap.setCenter(optionsMulti.position);
}

function setAddress() {
    provinciaField = document.getElementById("provincia");
    localidadField = document.getElementById("localidad");
    addressField = document.getElementById("direccion");

    if(addressField.value == "" ){
        return
    }

    if(E.Entity.Direccion() != "" && !E.Entity.ManOnTheMap()){
        geocoder = new google.maps.Geocoder();
        let pais = E.Entity.Pais();
        let newProvince = provinciaField.value;
        let newLocality = localidadField.value;
        let newAddress = addressField.value;
        let address = `${pais} ${newProvince} ${newLocality} ${newAddress}`
        const request = { address }
        geocoder.geocode(request).then((result) => {
            const { results } = result;
            let location = results[0].geometry.location
            map.setCenter(location);
            markerMan.setPosition(location);
            markerMan.setMap(map);
            E.Entity.Latitud(location.lat());
            E.Entity.Longitud(location.lng());
        }).catch((e) => {
            alert("Geocode was not successful for the following reason: " + e);
        });
    }



    
       // Get the place details from the autocomplete object.
    // const place = autocomplete.getPlace();
    // let lat = place.geometry.location.lat()
    // let lng = place.geometry.location.lng()
    // let pos = new google.maps.LatLng(lat, lng);
    // var image = {
    //     url: HOST + '/assets/pages/img/man.png',
    //     size: new google.maps.Size(47, 37),
    //     origin: new google.maps.Point(0, 0),
    //     anchor: new google.maps.Point(23, 32)
    // };

    // markerMan = new google.maps.Marker({
    //     draggable: true,
    //     position: pos,
    //     map: map,
    //     icon: image,
    //     title: "Ubicación actual",
    //     animation: google.maps.Animation.DROP,
    // });

    // map.setCenter(pos);
    // getManPosition('position', pos, markerMan);
    // 
    // let address = `${pais} ${newAddress}`
    // const request = { address }

    
}


/**
 * MAPA 1 ======================================================================
 */

var multimap;
var markerMan;
/**
 * MAPA 2 ======================================================================
 */
var markerMulti;
var currentId = 0;
var uniqueId = function () {
    return ++currentId;
}
var markers = {};
var createMarker = function (pos) {
    var id = uniqueId();
    if (id > 1) {
        var marker = new google.maps.Marker({
            id: id,
            position: pos,
            map: multimap,
            draggable: true,
            animation: google.maps.Animation.DROP
        });
        markers[id] = marker;
        map.panTo(pos);
        getMultiPostionData(id, pos.lat(), pos.lng());

        // $(".del").each(function () {
        //     $(this).attr('disabled', true);
        // });
        // $("#delete" + id).attr('disabled', false);

        google.maps.event.addListener(marker, 'dragend', function (event) {
            var lat = markers[id].getPosition().lat();
            var lng = markers[id].getPosition().lng();
            getMultiPostionData(id, lat, lng);
        });
    }
}
var deleteMarker = function (id) {
    var marker = markers[id];
    if (marker !== undefined) {
        marker.setMap(null);
        $("#locacion" + id).val('');
        // $("#delete" + currentId).attr('disabled', true);
        // $("#delete" + (currentId - 1)).attr('disabled', false);
        currentId--;
    }
}
//==============================================================================

function initMapEmpresa() {
    setTimeout(function () {
        var element = document.getElementById('map-canvas-1');

        if (element) {
            var mapOptions = {
                zoom: 16
            };
            map = new google.maps.Map(document.getElementById('map-canvas-1'), mapOptions);
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    if (E.Entity.Latitud() && E.Entity.Longitud()) {
                        pos = new google.maps.LatLng(E.Entity.Latitud(), E.Entity.Longitud());
                    } else {
                        pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    }
                    /**
                     * MAPA 1 ==========================================================
                     */
                    var image = {
                        url: HOST + '/assets/pages/img/man.png',
                        size: new google.maps.Size(47, 37),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(23, 32)
                    };

                    markerMan = new google.maps.Marker({
                        draggable: true,
                        position: pos,
                        map: map,
                        icon: image,
                        title: "Ubicación actual",
                        animation: google.maps.Animation.DROP,
                    });

                    map.setCenter(pos);
                    getManPosition('position', pos, markerMan);

                    google.maps.event.addListener(map, 'click', function (event) {
                        getManPosition('event', event, markerMan);
                    });

                    google.maps.event.addListener(markerMan, "click", function (event) {
                        getManPosition('event', event, markerMan);
                    });

                    google.maps.event.addListener(markerMan, 'dragend', function (event) {
                        getManPosition('event', event, markerMan);
                    });

                    /**
                     * MAPA 2 ==========================================================*/

                    if (TipoUsuario === 'oferentes') {

                        if (IdUsuario > 0) {
                            var lat = E.Entity.Latitud();
                            var lon = E.Entity.Longitud();
                            if (lat !== null && lon !== null) {
                                var posi = new google.maps.LatLng(lat, lon);
                                createMarker(posi, map);
                            } else {
                                createMarker(pos, map);
                            }
                        } else {
                            createMarker(pos, map);
                        }

                        google.maps.event.addListener(map, 'click', function (event) {
                            createMarker(event.latLng, map);
                        });
                    }

                }, function () {
                    handleNoGeolocation(true);
                });
            } else {
                handleNoGeolocation(false);
            }
        }
    }, 5000);
}

function initMapEmpresaDetalle() {
    setTimeout(function () {
        var element = document.getElementById('map-canvas-1');

        if (element) {
            var mapOptions = {
                zoom: 16
            };
            map = new google.maps.Map(document.getElementById('map-canvas-1'), mapOptions);
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = E.Latitud();
                    var lon = E.Longitud();
                    if (lat && lon) {
                        pos = new google.maps.LatLng(lat, lon);
                    } else {
                        pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    }
                    /**
                     * MAPA 1 ==========================================================
                     */
                    var image = {
                        url: HOST + '/assets/pages/img/man.png',
                        size: new google.maps.Size(47, 37),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(23, 32)
                    };

                    markerMan = new google.maps.Marker({
                        draggable: true,
                        position: pos,
                        map: map,
                        icon: image,
                        title: "Ubicación actual",
                        animation: google.maps.Animation.DROP,
                    });

                    map.setCenter(pos);
                    getManPosition('position', pos, markerMan);

                    google.maps.event.addListener(map, 'click', function (event) {
                        getManPosition('event', event, markerMan);
                    });

                    google.maps.event.addListener(markerMan, "click", function (event) {
                        getManPosition('event', event, markerMan);
                    });

                    google.maps.event.addListener(markerMan, 'dragend', function (event) {
                        getManPosition('event', event, markerMan);
                    });

                    /**
                     * MAPA 2 ==========================================================*/

                    if (TipoUsuario === 'oferentes') {

                        if (IdUsuario > 0) {
                            var lat = E.Latitud();
                            var lon = E.Longitud();
                            if (lat !== null && lon !== null) {
                                var posi = new google.maps.LatLng(lat, lon);
                                createMarker(posi, map);
                            } else {
                                createMarker(pos, map);
                            }
                        } else {
                            createMarker(pos, map);
                        }

                        google.maps.event.addListener(map, 'click', function (event) {
                            createMarker(event.latLng, map);
                        });
                    }

                }, function () {
                    handleNoGeolocation(true);
                });
            } else {
                handleNoGeolocation(false);
            }
        }
    }, 2000);
}

//==============================================================================

function initMapConcurso() {
    setTimeout(function () {
        var element = document.getElementById('map-canvas-1');
        

        if (element) {
            var mapOptions = {
                zoom: 16
            };
            map = new google.maps.Map(element, mapOptions);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var pos;

                    if (Accion === 'edicion-online' || Accion === 'edicion-sobrecerrado') {
                        pos = new google.maps.LatLng(E.Entity.Latitud(), E.Entity.Longitud());
                    } else {
                        pos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    }

                    /**
                     * MAPA 1 ==========================================================
                     */
                    var image = {
                        url: HOST + '/assets/pages/img/man.png',
                        size: new google.maps.Size(47, 37),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(23, 32)
                    };

                    markerMan = new google.maps.Marker({
                        draggable: true,
                        position: pos,
                        map: map,
                        icon: image,
                        title: "Ubicación actual",
                        animation: google.maps.Animation.DROP,
                    });

                    map.setCenter(pos);
                    getManPosition('position', pos, markerMan);

                    google.maps.event.addListener(map, 'click', function (event) {
                        console.log('click map', E.Entity.ManOnTheMap())
                        E.Entity.ManOnTheMap(true);
                        getManPosition('event', event, markerMan);
                    });

                    google.maps.event.addListener(markerMan, "click", function (event) {
                        console.log('click man', E.Entity.ManOnTheMap())
                        E.Entity.ManOnTheMap(true);
                        getManPosition('event', event, markerMan);
                    });

                    google.maps.event.addListener(markerMan, 'dragend', function (event) {
                        console.log('dragend', E.Entity.ManOnTheMap())
                        E.Entity.ManOnTheMap(true);
                        getManPosition('event', event, markerMan);
                    });

                }, function () {
                    handleNoGeolocation(true);
                });


            } else {
                handleNoGeolocation(false);
            }
        }
    }, 10000);
}

//==============================================================================

function initMapConcursoInvitacion() {
    setTimeout(function () {
        var element = document.getElementById('map-canvas-1');

        if (element) {
            var mapOptions = {
                zoom: 16
            };
            map = new google.maps.Map(document.getElementById('map-canvas-1'), mapOptions);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var pos = new google.maps.LatLng(E.Latitud(), E.Longitud());

                    /**
                     * MAPA 1 ==========================================================
                     */
                    var image = {
                        url: HOST + '/assets/pages/img/man.png',
                        size: new google.maps.Size(47, 37),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(23, 32)
                    };

                    markerMan = new google.maps.Marker({
                        draggable: false,
                        position: pos,
                        map: map,
                        icon: image,
                        title: "Ubicación actual",
                        animation: google.maps.Animation.DROP,
                    });

                    map.setCenter(pos);
                    getManPosition('position', pos, markerMan);

                }, function () {
                    handleNoGeolocation(true);
                });
            } else {
                handleNoGeolocation(false);
            }
        }
    }, 500);
}