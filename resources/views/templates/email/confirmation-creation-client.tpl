{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$user->full_name},<br><br>

    Se ha creado exitosamente el siguiente concurso:<br><br>

    <b>ID:</b> {$concurso->id}<br>
    <b>Nombre:</b> {$concurso->nombre}<br>
    <b>Fecha Límite De Aceptación De Fechas:</b> {$concurso->fecha_limite|date_format:"%d-%m-%Y %H:%M"}<br>
    <b>Fecha Límite Para Realizar Consultas En El Muro De Consultas:</b> {$concurso->finalizacion_consultas|date_format:"%d-%m-%Y %H:%M"}<br><br>

    <b>Oferentes Invitados:</b><br>
    <ul>
        {foreach from=$oferentes item=oferente}
            <li>{$oferente}</li>
        {/foreach}
    </ul>
    <br>

    Puede continuar el proceso desde la plataforma.<br><br>

    Saludos cordiales.<br>
{/block}
