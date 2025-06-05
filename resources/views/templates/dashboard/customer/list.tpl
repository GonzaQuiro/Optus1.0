{extends 'dashboard/offerer/main.tpl'}

<!-- ESTILOS -->
{block 'styles'}
    <link href="{asset('/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css')}" rel="stylesheet"type="text/css" />
    <link href="{asset('/global/plugins/morris/morris.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/fullcalendar/fullcalendar.min.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/jqvmap/jqvmap/jqvmap.css')}" rel="stylesheet" type="text/css" />
    <link href="{asset('/global/plugins/fullcalendar/fullcalendar.min.css')}" rel="stylesheet" type="text/css" />


    <!-- This fixes the calendar "jumping" when hovering the buttons to change the month -->
    <style>
        .portlet.calendar.light .fc-button {
            border-bottom: 2px solid transparent;
            transition: border-color 0.2s ease;
        }

        .portlet.calendar.light .fc-button.fc-state-active,
        .portlet.calendar.light .fc-button.fc-state-hover {
            color: #333;
            border-bottom: 2px solid #36c6d3;
        }
    </style>

{/block}

<!-- SCRIPTS PREVIOS A KNOCKOUT -->
{block 'pre-scripts'}
    <script src="{asset('/global/plugins/popper/popper.min.js')}" type="text/javascript"></script>
    <script src="{asset('/global/plugins/tooltip/tooltip.min.js')}" type="text/javascript"></script>
{/block}

<!-- SCRIPTS POSTERIORES A KNOCKOUT -->
{block 'post-scripts'}
{/block}

<!-- VISTA -->
{block 'dashboard-list'}
    <div class="container">
        <div class="row">
            <div class="col-8 col-md-8">
                <div class="portlet light portlet-fit bordered calendar">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class=" icon-layers font-green"></i>
                            <span class="caption-subject font-green sbold uppercase">Calendario</span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <div class="col-4 col-md-4">
                <div class="portlet light bordered" style="width: 80%">
                    <table class="table ">
                        <tbody>
                            <tr>
                                <td> <i class="fa fa-circle" style="color: #D91E18" aria-hidden="true"></i> </td>
                                <td>Invitación Pendiente</td>
                            </tr>
                            <tr>
                                <td> <i class="fa fa-circle" style="color: #80319b" aria-hidden="true"></i> </td>
                                <td>Muro de Consulta</td>
                            </tr>
                            <tr>
                                <td> <i class="fa fa-circle" style="color: #e87e03" aria-hidden="true"></i> </td>
                                <td>Técnica Pendiente</td>
                            </tr>
                            <tr>
                                <td> <i class="fa fa-circle" style="color: #f7ca17" aria-hidden="true"></i> </td>
                                <td>Económica Pendiente</td>
                            </tr>
                            <tr>
                                <td> <i class="fa fa-circle " style="color: #2cc281" aria-hidden="true"></i> </td>
                                <td>Adjudicación Pendiente</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{/block}

<!-- KNOCKOUT JS -->
{block 'knockout' append}
    <script type="text/javascript">
        var Eventos = function(data) {
            var self = this;

            this.Breadcrumbs = ko.observableArray(data.breadcrumbs);

            this.Entity = {

                Invitaciones: ko.observableArray(''),
                Consultas: ko.observableArray(''),
                Tecnicas: ko.observableArray(''),
                Economicas: ko.observableArray(''),
                PorAdjudicar: ko.observableArray('')
            }

            this.setEntity = function(data) {

                self.Entity.Invitaciones(data.list.Invitaciones);
                self.Entity.Consultas(data.list.Consultas);
                self.Entity.Tecnicas(data.list.Tecnicas);
                self.Entity.Economicas(data.list.Economicas);
                self.Entity.PorAdjudicar(data.list.PorAdjudicar);
            }
            self.setEntity(data);
        }

        jQuery(document).ready(function() {
            $.blockUI();
            Services.Get('/dashboard/list', {
                    UserToken: User.Token
                },
                (response) => {
                    if (response.success) {
                        window.E = new Eventos(response.data);
                        AppOptus.Bind(E);
                        var date = new Date();
                        var d = date.getDate();
                        var m = date.getMonth();
                        var y = date.getFullYear();

                        var arr = E.Entity.Invitaciones();
                        var arr1 = E.Entity.Consultas();
                        var arr2 = E.Entity.Tecnicas();
                        var arr3 = E.Entity.Economicas();
                        var arr4 = E.Entity.PorAdjudicar();

                        var array = arr.concat(arr1, arr2, arr3, arr4);

                        var events = new Array();
                        $.each(array, function(i, v) {
                            var eventSource = new Object();
                            eventSource.title = v.nombre + " (" + v.etapa + ")";
                            eventSource.id = v.id + "_" + v.class;
                            eventSource.popoverID = " - ID:" + v.id;
                            eventSource.description = v.nombre;
                            eventSource.content = v.etapa;
                            eventSource.tipo_concurso = v.tipo_concurso;
                            eventSource.start = v.fecha;
                            eventSource.allDay = true;
                            eventSource.className = v.class;
                            events[i] = eventSource;
                        });

                        $('#calendar').fullCalendar({
                            events: events,
                            eventRender: function(info, element) {
                                element.popover({
                                    title: info.content,
                                    content: info.description + info.popoverID,
                                    placement: 'top',
                                    trigger: 'hover',
                                    container: 'body'
                                });
                            },
                            eventClick: function(calEvent, jsEvent, view) {
                            var id = calEvent.id;
                            var tipo = calEvent.tipo_concurso;
                            var parts = id.split('_');
                            var realId = parts[0];

                            // Mapeo de etapas a pasos
                            const etapaToStep = {
                                'Invitación Pendiente': 'convocatoria-oferentes',
                                'Muro de Consulta': 'chat-muro-consultas',
                                'Técnica Pendiente': 'analisis-tecnicas',
                                'Económica Pendiente': 'analisis-ofertas',
                                'Adjudicación Pendiente': 'evaluacion-reputacion'
                            };

                            var step = etapaToStep[calEvent.content] || 'convocatoria-oferentes';
                            $.blockUI();
                            Services.Post('/concursos/guardar-token-acceso', {
                                UserToken: User.Token,
                                id: realId
                            },
                            (response) => {
                                $.unblockUI();
                                if (response.success) {
                                    var url = '/concursos/cliente/' + tipo + '/' + step + '/' + realId;
                                    window.open(url, '_blank');
                                } else {
                                    swal('Error', response.message, 'error');
                                }
                            },
                            (error) => {
                                $.unblockUI();
                                swal('Error', error.message, 'error');
                            });
                        }
                        });
                    }

                    $.unblockUI();
                },
                (error) => {
                    $.unblockUI();
                    swal('Error', error.message, 'error');
                },
                null,
                null
            );
        });

        // Chrome allows you to debug it thanks to this
        {chromeDebugString('dynamicScript')}
    </script>
{/block}