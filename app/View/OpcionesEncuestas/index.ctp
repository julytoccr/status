<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('opciones_encuestas'),
    "sidebar" => 'encuestas/opciones',
    "encuesta_id" => $encuesta_id,
    "pregunta_id" => $pregunta_id,
));
$this->end();?>
<div class="opciones index">
	<h2>
		<b><?php echo $encuesta['nombre']?></b>
		<br/>
		<i><?php echo $encuesta['contenido']?></i>
		<br/>
		<?php echo $pregunta['contenido']?>
	</h2>
	<?php
	$elements = $opciones;
	$fields = array(
		'form_contenido' => array('Model'=>'OpcionesEncuesta','field'=>'contenido')
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	$paginate = false;
	echo $this->element('listado', compact('elements', 'fields', 'actions','paginate'));
	?>
</div>
