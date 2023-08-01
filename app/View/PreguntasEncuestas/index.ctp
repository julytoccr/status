<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('preguntas_encuesta'),
    "sidebar" => 'encuestas/preguntas',
    "encuesta_id" => $encuesta_id,
));
$this->end();?>
<div class="encuestas index">
	<h2>
		<b><?php echo $encuesta['nombre']?></b>
		<br/>
		<i><?php echo $encuesta['contenido']?></i>
	</h2>
	<?php
	$elements = $preguntas;
	$fields = array(
		'form_contenido' => array('Model'=>'PreguntasEncuesta','field'=>'contenido')
	);
	$actions = array(
		'index' => array('label' => 'opciones','controller'=>'opciones_encuestas'),
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	$paginate = false;
	echo $this->element('listado', compact('elements', 'fields', 'actions','paginate'));
	?>
</div>
