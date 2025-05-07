{extends 'email/base.tpl'}

{block 'content'}
    <b>Estimado {$user->full_name}</b><br><br>
    Recibimos una solicitud para verificar tu inicio de sesión en la cuenta <b>{$user->username}</b>.<br><br>
    Tu código de verificación es: <b>{$twoFactorCode}</b><br><br>
    Este código estará vigente solo durante 10 minutos a partir de la recepción de este correo electrónico.<br><br>
    <b>Si no solicitaste este código, por favor ignora este mensaje. Tu cuenta sigue segura y sin cambios. Si tienes alguna duda, comunícate con nosotros.</b>
{/block}