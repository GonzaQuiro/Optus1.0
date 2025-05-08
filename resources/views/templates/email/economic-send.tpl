{extends 'email/base.tpl'}

{block 'content'}
    Estimado <strong>{$cliente}</strong>,
    <br><br>
    El proveedor invitado, <strong>{$proveedor}</strong>, ha enviado su propuesta económica correspondiente al <b>Nº Concurso</b>: {$concurso->id}, {$concurso->nombre}, la cual se encuentra disponible para su revisión.
    <br><br>
    Puede acceder al sistema ingresando en www.optus.com.ar para proceder con la evaluación.
    <br><br>
    <strong>Notificación generada el día {$hora}, hora local del proveedor que envió la propuesta.</strong>
    <br><br>
    Agradecemos su colaboración.
{/block}
