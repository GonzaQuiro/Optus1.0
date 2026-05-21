{extends 'email/base.tpl'}

{block 'content'}
    Hola {$solped->solicitante->full_name},<br><br>

    Tu solicitud Nro. <strong>#{$solped->id}</strong> <strong>{$solped->nombre}</strong> ha sido <strong>concluida</strong>.<br><br>

    <strong>Motivo:</strong> {$reason}<br>
    <strong>Fecha de conclusion:</strong> {if $solped->updated_at}{$solped->updated_at|date_format:"%d-%m-%Y %H:%M"}{else}-{/if}<br><br>

    Si necesitas mas informacion, ingresa al portal o contacta con el equipo de compras.<br><br>

    <div style="width: 100%; text-align: right;">
        Atentamente,<br>
        {$user->full_name}
    </div>
{/block}
