<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
?>
<ul id="sintaxis_sort">
<?php foreach ($data as $row): ?>
	<li id="sort_<?= $row['SintaxisRespuesta']['id']?>" class="item">
		<?= $row['SintaxisRespuesta']['nombre']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#sintaxis_sort');
$this->Js->sortable(array(
	'complete' => '$.post("/sintaxis_respuestas/sort", $("#sintaxis_sort").sortable("serialize"))',
));
echo $this->Js->writeBuffer();
?>
