<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('encuestas'),
    "sidebar" => 'encuestas/encuestas'
));
$this->end();?>
<div class="encuestas index">
	<h2><?php echo __('Encuestas'); ?></h2>
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
		'/encuestas_asignaturas/add/' => array('label' => 'asociar'),
		'clonar_encuesta' => array('label' => 'clonar'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
