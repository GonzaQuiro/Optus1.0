{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name}<br><br>

    Tenemos el agrado de invitarlo a participar del siguiente concurso de precios <b>{$concurso->nombre}</b>, administrado
    por {$concurso->cliente->full_name} en representación de {$concurso->cliente->customer_company->business_name}.<br><br>
    <ul>
        <li><b>Nº Concurso</b>: {$concurso->id}<br><br></li>
        <li><b>Nombre de Concurso</b>: {$concurso->nombre}<br><br></li>
        <li><b>Reseña</b>: {$concurso->resena}<br><br></li>
        <li><b>Zona Horaria</b>: {$timeZone}<br><br></li>
        <li><b>Fecha límite para aceptar invitación</b>: {$concurso->fecha_limite->format('d-m-Y H:i')}<br><br></li>
        <li><b>Fecha cierre de muro de consultas</b>: {$concurso->finalizacion_consultas->format('d-m-Y H:i')}<br><br></li>
        <li><b>Fecha límite para presentación de ofertas técnicas</b>: {$fecha_tecnica}<br><br></li>
        {if $concurso->is_go || $concurso->is_sobrecerrado}
            <li><b>Fecha límite para presentación de ofertas económicas</b>:
                {$concurso->fecha_limite_economicas->format('d-m-Y H:i')}<br><br></li>
        {elseif $concurso->is_online}
            <li><b>Inicio de la subasta</b>: {$concurso->inicio_subasta->format('d-m-Y H:i:s')} hs. </li>
        {/if}
    </ul>
    Para consultas de pliegos, bases y condiciones generales, le solicitamos ingresar a https://optus-portal.com/, donde encontrará
    mayor información.<br><br>
    Contamos con su participación.<br><br>
    <b>¡Una nueva oportunidad lo está esperando!</b>
{/block}