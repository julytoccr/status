<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('encuestas'),
    "sidebar" => 'encuestas/encuestas'
));
$this->end();?>
<div class="encuestas index">
	<?php
	$elements = $encuestas;
	$fields = array(
		'form_encuesta' => array('Model'=>'Encuesta','field'=>'nombre'),
		'form_fecha_inicio' => array('Model'=>'EncuestasAsignatura','field'=>'fecha_inicio','formato'=>'fecha_hora'),
		'form_fecha_fin' => array('Model'=>'EncuestasAsignatura','field'=>'fecha_fin','formato'=>'fecha_hora'),
		'form_activo' => array('Model'=>'EncuestasAsignatura','field'=>'activo','formato'=>'booleano'),
	);
	$actions = array(
		'mostrar' => array('label' => 'Ver','controller'=>'encuestas'),
		'edit' => array('label' => 'editar'),
		'resultados' => array('label' => 'resultados'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
