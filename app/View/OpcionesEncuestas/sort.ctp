<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
?>
<h2>
	<b><?php echo $encuesta['nombre']?></b>
	<br/>
	<i><?php echo $encuesta['contenido']?></i>
	<br/>
	<?php echo $pregunta['contenido']?>
</h2>
<ul id="opciones_sort">
<?php foreach ($opciones as $row): ?>
	<li id="sort_<?= $row['OpcionesEncuesta']['id']?>" class="item">
		<?= $row['OpcionesEncuesta']['contenido']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#opciones_sort');
$this->Js->sortable(array(
	'complete' => '$.post("/opciones_encuestas/sort/'.$pregunta_id.'", $("#opciones_sort").sortable("serialize"))',
));
echo $this->Js->writeBuffer();
?>
<?php //echo $sortable->sortable('bloques_sort','item')?>
<?php //echo $sortable->submit(__('guardar_orden'),'/bloques/sort')?>
