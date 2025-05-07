{extends 'email/base.tpl'}

{block 'content'}
    Estimado {$company_name},<br><br>

    Le informamos que se ha realizado una modificación en la licitación <b>{$concurso->nombre}</b>.<br><br>

    Para ver los cambios porfavor ingrese al portal Optus.

    Ante cualquier consulta, no dude en contactarnos.<br><br>

{/block}
