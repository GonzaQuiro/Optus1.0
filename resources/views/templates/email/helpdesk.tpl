{extends 'email/base.tpl'}

{block 'content'}
    Estimado, por medio del presente me dirijo a usted, enviándole un cordial saludo y aprovecho la oportunidad para solicitarle su apoyo referente a:<br><br>
    {$consulta}<br><br>
    Agradezco su atención y pronto servicio.<br><br>
    Atentamente.<br>
    <b>{$user->full_name}</b><br>
    <b>{$user->email}</b><br>
    <b>{$cliente}</b><br><br>
{/block}