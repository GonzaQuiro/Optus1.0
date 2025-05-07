{extends 'email/base.tpl'}

{block 'content'}
Datos del Cliente que solicita la cotización de custodia armada para su carga:<br><br>
<span class="text-bold">Nombre:</span> {$concurso->cliente->full_name}<br><br>
<span class="text-bold">Razon Social:</span> {$concurso->cliente->customer_company->business_name}<br><br>
<span class="text-bold">Consurso:</span> {$concurso->nombre}<br><br>
{/block}