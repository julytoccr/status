<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
?>
<h2>
	<b><?php echo $encuesta['nombre']?></b>
	<br/>
	<i><?php echo $encuesta['contenido']?></i>
</h2>
<ul id="preguntas_sort">
<?php foreach ($preguntas as $row): ?>
	<li id="sort_<?= $row['PreguntasEncuesta']['id']?>" class="item">
		<?= $row['PreguntasEncuesta']['contenido']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#preguntas_sort');
$this->Js->sortable(array(
	'complete' => '$.post("/preguntas_encuestas/sort/'.$encuesta_id.'", $("#preguntas_sort").sortable("serialize"))',
));
echo $this->Js->writeBuffer();
?>
<?php //echo $sortable->sortable('bloques_sort','item')?>
<?php //echo $sortable->submit(__('guardar_orden'),'/bloques/sort')?>
