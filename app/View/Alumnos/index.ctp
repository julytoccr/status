<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('alumnos'),
    "sidebar" => 'alumnos/menu'
));
$this->end();?>
<div class="alumnos index">
	<?php
	/*$elements = $alumnos;
	$fields = array(
		'form_nombre' => array('Model'=>'Usuario','field'=>'nombre'),
		'form_apellidos' => array('Model'=>'Usuario','field'=>'apellidos'),
		'form_email' => array('Model'=>'Usuario','field'=>'email'),
		'form_login' => array('Model'=>'Usuario','field'=>'login'),
		'form_repetidor' => array('Model'=>'Alumno','field'=>'repetidor'),
		'form_grupo' => array('Model'=>'Grupo','field'=>'nombre'),
	);
	$actions = array(
		'edit' => array('label' => 'Edit'),
		'delete' => array('label' => 'Delete', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	$meta = array('defaultModel' => 'Alumno');
	echo $this->element('listado', compact('elements', 'fields', 'actions', 'meta'));*/

	$alumnos_refactored = array();
	foreach ($alumnos as $alumno) {
		$alumnos_refactored[] = array($alumno['Alumno']['id'],
									  $alumno['Usuario']['nombre'],
								      $alumno['Usuario']['apellidos'],
								      $alumno['Usuario']['email'],
								      $alumno['Alumno']['repetidor'],
								      $alumno['Usuario']['login'],
								      $alumno['Usuario']['tipo_auth'],
								      $alumno['Grupo']['nombre']);
	}
	echo $this->Jquery->datatable(TRUE,
					   array(__('nombre'),
							 __('apellidos'),
							 __('email'),
							 __('form_repetidor'),
							 __('login'),
							 __('form_tipo_autentificacion'),
							 __('grupo')),
					   $alumnos_refactored,
					   array(array(2, "asc")),
					   NULL,
					   NULL,
					   TRUE,
					   array(array("Alumno.id", "text"),
					   	     array("Usuario.nombre", "text"),
					   	     array("Usuario.apellidos", "text"),
					   	     array("Usuario.email", "text"),
					   	     array("Alumno.repetidor", "boolean"),
					   	     array("Usuario.login", "text"),
					   	     array("Usuario.tipo_auth", "select", $tipo_auth),
					   	     array("Grupo.nombre", "text")
					   ),
					   "/alumnos/add",
					   "/alumnos/edit",
					   "/alumnos/delete",
					   NULL);
	?>
</div>
