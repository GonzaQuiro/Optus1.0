{extends 'email/base.tpl'}

{block 'content'}
    Hola {$nuevo_comprador->full_name},<br><br>

    Te informamos que se ha delegado una solicitud en tu usuario.<br><br>

    <strong>Solicitud N°:</strong> #{$solped->id}<br>
    <strong>Nombre:</strong> {$solped->nombre}<br>
    <strong>Delegada por:</strong> {$delegador->full_name}<br>
    <strong>Solicitante:</strong> {$solped->solicitante->full_name}<br><br>

    Accede al sistema para revisar y gestionar esta solicitud.<br><br>

    <div style="width: 100%; text-align: right;">
        Saludos,<br>
        Sistema Optus
    </div>
{/block}
