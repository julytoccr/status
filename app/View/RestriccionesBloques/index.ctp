<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('restricciones_bloque'),
    "sidebar" => 'bloques/restricciones'
));
$this->end();?>
<div class="bloques index">
	<h2><?php echo __('Restricciones Bloques'); ?></h2>
	<?php
	$elements = $restricciones;
	$fields = array(
		'form_grupo' => array('Model'=>'RestriccionesBloque','field'=>'grupo','formato'=>'grupo'),
		'form_fecha_inicio' => array('Model'=>'RestriccionesBloque','field'=>'fecha_inicio','formato'=>'fecha_hora'),
		'form_fecha_fin' => array('Model'=>'RestriccionesBloque','field'=>'fecha_fin','formato'=>'fecha_hora')
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));
	?>
</div>
