<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('encuestas'),
    "sidebar" => 'encuestas/encuestas'
));
$this->end();?>
<div class="encuestas index">
	<?php 
		echo $this->Html->link(__('mis_encuestas'), '/encuestas');
		echo "<br/>";
		echo $this->Html->link(__('todas_las_encuestas'), '/encuestas/todas');
	?>
	<?php
	$elements = $encuestas;
	$fields = array(
		'nombre' => array('Model'=>'Encuesta','field'=>'nombre'),
		'contenido' => array('Model'=>'Encuesta','field'=>'contenido'),
	);
	$actions = array(
		'mostrar' => array('label' => 'Ver'),
		'add' => array('label' => 'asociar','controller'=>'encuestas_asignaturas'),
		'index' => array('label' => 'menu_preguntas','controller'=>'preguntas_encuestas'),
		'edit' => array('label' => 'editar'),
		'clonar_encuesta' => array('label' => 'clonar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
