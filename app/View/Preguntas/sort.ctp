<?php
$this->start('script');
echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
$this->end();
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('menu_preguntas'),
    "sidebar" => 'preguntas/preguntas',
    "problema_id" => $problema_id
));
$this->end();
?>
<ul id="preguntas_sort">
<?php foreach ($data as $row): ?>
	<li id="sort_<?= $row['Pregunta']['id']?>" class="item">
		<?= $row['Pregunta']['enunciado']?>
	</li>
<?php endforeach;?>
</ul>
<?php
$this->Js->get('#preguntas_sort');
$this->Js->sortable(array(
	'complete' => "$.post(\"/preguntas/sort/$problema_id\", $(\"#preguntas_sort\").sortable(\"serialize\"))",
));
echo $this->Js->writeBuffer();
?>
