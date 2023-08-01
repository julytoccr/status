<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('avanzado'),
    "sidebar" => 'avanzado/sintaxis_respuesta'
));
$this->end();?>
<div class="sintaxisRespuestas index">
	<?php
	$elements = $sintaxisRespuestas;
	$fields = array(
		'form_nombre' => array('Model'=>'SintaxisRespuesta','field'=>'nombre'),
		'form_descripcion' => array('Model'=>'SintaxisRespuesta','field'=>'descripcion'),
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
