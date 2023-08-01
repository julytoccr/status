<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('menu_preguntas'),
    "sidebar" => 'preguntas/preguntas',
    "problema_id" => $problema_id
));
$this->end();?>
<div class="preguntas index">	
	<?php
	$elements = $preguntas;
	$fields = array(
		'form_enunciado' => array('Model'=>'Pregunta','field'=>'enunciado', 'formato' => 'html'),
		'form_respuesta' => array('Model'=>'Pregunta','field'=>'respuesta', 'formato' => 'html'),
		'form_peso' => array('Model'=>'Pregunta','field'=>'peso'),
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
