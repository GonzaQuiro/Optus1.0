{extends 'email/base.tpl'}

{block 'content'}
<b>Estimado {$user->full_name}</b><br><br>
Recibimos una solicitud de cambio de contraseña para el usuario <b>{$user->username}</b><br><br>
Haga click en el siguiente <a href="{$url}">enlace</a> para cambiar su contraseña<br><br>
Considera, por favor, que el siguiente enlace estará vigente solo durante 20 minutos, a partir de la recepción de este correo electrónico<br><br>
<b>Si no solicitaste un cambio de contraseña ignora el siguiente mensaje. Tu contraseña seguirá siendo la misma. Si tienes alguna duda, comunícate con nosotros</b>
{/block}