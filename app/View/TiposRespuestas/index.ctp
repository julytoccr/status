<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('avanzado'),
    "sidebar" => 'avanzado/tipos_respuestas'
));
$this->end();?>
<div class="tiposRespuestas index">
	<?php
	$elements = $tiposRespuestas;
	$fields = array(
		'form_nombre' => array('Model'=>'TiposRespuesta','field'=>'nombre'),
		'form_descripcion' => array('Model'=>'TiposRespuesta','field'=>'descripcion'),
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
