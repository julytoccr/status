<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
?>
<ul id="tipos_sort">
<?php foreach ($data as $row): ?>
	<li id="sort_<?= $row['TiposRespuesta']['id']?>" class="item">
		<?= $row['TiposRespuesta']['nombre']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#tipos_sort');
$this->Js->sortable(array(
	'complete' => '$.post("/tipos_respuestas/sort", $("#tipos_sort").sortable("serialize"))',
));
echo $this->Js->writeBuffer();
?>
