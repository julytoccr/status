<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
?>
<ul id="programas_sort">
<?php foreach ($data as $row): ?>
	<li id="sort_<?= $row['Programa']['id']?>" class="item">
		<?= $row['Programa']['nombre']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#programas_sort');
$this->Js->sortable(array(
	'complete' => '$.post("/programas/sort", $("#programas_sort").sortable("serialize"))',
));
echo $this->Js->writeBuffer();
?>
