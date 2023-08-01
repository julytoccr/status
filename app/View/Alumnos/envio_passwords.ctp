<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('alumnos'),
    "sidebar" => 'alumnos/menu'
));
$this->end();?>
<div class="alumnos index">
	<h2><?php echo __('Alumnos'); ?></h2>
	<?php
	$elements = $alumnos;
	$fields = array(
		'form_nombre' => array('Model'=>'Usuario','field'=>'nombre'),
		'form_apellidos' => array('Model'=>'Usuario','field'=>'apellidos'),
		'form_email' => array('Model'=>'Usuario','field'=>'email'),
		'form_login' => array('Model'=>'Usuario','field'=>'login'),
	);
	$actions = array(
		'enviarpassword' => array('label' => __('enviar')),
	);
	$meta = array('defaultModel' => 'Alumno');
	echo $this->element('listado', compact('elements', 'fields', 'actions', 'meta'));
	?>
</div>
