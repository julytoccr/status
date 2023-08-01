<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('bloques'),
    "sidebar" => 'bloques/bloques'
));
$this->end();
?>
<ul id="bloques_sort">
<?php foreach ($data as $row): ?>
	<li id="sort_<?= $row['Bloque']['id']?>" class="item">
		<?= $row['Bloque']['nombre']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#bloques_sort');
$this->Js->sortable(array(
	'complete' => '$.post("/bloques/sort", $("#bloques_sort").sortable("serialize"))',
));
echo $this->Js->writeBuffer();
?>
<?php //echo $sortable->sortable('bloques_sort','item')?>
<?php //echo $sortable->submit(__('guardar_orden'),'/bloques/sort')?>
