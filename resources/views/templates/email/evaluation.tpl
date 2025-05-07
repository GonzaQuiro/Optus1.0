{extends 'email/base.tpl'}

{block 'content'}
Estimado {$company_name}<br><br>
Ha sido evaluado en referencia al <b>Nº Concurso</b>: {$concurso->id}, "{$concurso->nombre}" con el siguiente resultado:<br><br>
<table>
    <tr>
        <td><strong>Puntualidad: </strong></td>
        <td>{$values[$evaluation['Puntualidad']]}</td>
    </tr>
    <tr>
        <td><strong>Calidad: </strong></td>
        <td>{$values[$evaluation['Calidad']]}</td>
    </tr>
    <tr>
        <td><strong>Orden y limpieza: </strong></td>
        <td>{$values[$evaluation['OrdenYlimpieza']]}</td>
    </tr>
    <tr>
        <td><strong>Medio ambiente: </strong></td>
        <td>{$values[$evaluation['MedioAmbiente']]}</td>
    </tr>
    <tr>
        <td><strong>Higiene y seguridad: </strong></td>
        <td>{$values[$evaluation['HigieneYseguridad']]}</td>
    </tr>
    <tr>
        <td><strong>Experiencia: </strong></td>
        <td>{$values[$evaluation['Experiencia']]}</td>
    </tr>
</table>

<p><strong>Comentarios:</strong> {$evaluation['Comentario']}</p>
<br>
Esperamos esta retroalimentación aporte al crecimiento y desarrollo de su organización, apostando a nuevas oportunidades de negocio.<br><br>
Le agradecemos su interés y esfuerzo en la participación del proceso.
{/block}