<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('avanzado'),
    "sidebar" => 'avanzado/programas'
));
$this->end();?>
<div class="programas index">
	<?php
	$elements = $programas;
	$fields = array(
		'form_nombre' => array('Model'=>'Programa','field'=>'nombre'),
		'form_publico_profesores' => array('Model'=>'Programa','field'=>'publico','formato'=>'booleano'),
	);
	if(isset($profesor)){
		$actions = array(
			'run' => array('label' => 'ejecutar'),
			'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
		);
	}
	else{
		$actions = array(
			'run' => array('label' => 'ejecutar'),
			'edit' => array('label' => 'editar'),
			'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
		);
	}
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
