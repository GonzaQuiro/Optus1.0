{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$user->first_name},<br><br>

    Nos complace informarle que su cuenta ha sido creada exitosamente.<br><br>
    
    A continuación, encontrará sus credenciales de acceso:<br><br>
    <ul>
        <li><b>Usuario</b>: {$user->username}</li>
        <li><b>Contraseña</b>: {$password}</li>
    </ul>
    
    Para iniciar sesión, por favor visite nuestro sitio web en <a href="{$url}">portal.optus.com.ar/login</a> y utilice las credenciales proporcionadas.<br><br>
    
    Si tiene alguna pregunta o necesita asistencia, no dude en contactarnos.<br><br>
    
    ¡Bienvenido a nuestro servicio!<br><br>
    
    Atentamente,<br>
    El equipo de soporte
{/block}
