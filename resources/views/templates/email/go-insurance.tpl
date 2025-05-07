{extends 'email/base.tpl'}

{block 'content'}
Estimado:<br><br>
Se ha solicitado cotización de SEGURO DE CARGA para el siguente Concurso:<br><br>
<span class="text-bold">Consurso:</span> {$concurso->nombre}<br><br>
<span class="text-bold">Nombre:</span> {$concurso->cliente->full_name}<br><br>
<span class="text-bold">Razon Social:</span> {$concurso->cliente->customer_company->business_name}<br><br>
<span class="text-bold">Email:</span> {$concurso->cliente->email}<br><br>
<span class="text-bold">Suma Asegurada:</span> {$concurso->go->suma_asegurada} {$concurso->tipo_moneda->nombre}<br><br>
{/block}