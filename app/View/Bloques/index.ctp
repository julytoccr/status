<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('bloques'),
    "sidebar" => 'bloques/bloques'
));
$this->end();?>
<div class="bloques index">
	<?php
	$elements = $bloques;
	$fields = array(
		'form_nombre' => array('Model'=>'Bloque','field'=>'nombre')
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'index' => array('label' => 'restricciones','controller'=>'restricciones_bloques'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
