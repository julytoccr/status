<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('profesores'),
    "sidebar" => 'profesores/profesores'
));
$this->end();?>
<div class="profesores index">	
	<?php
	/*$elements = $profesores;
	$fields = array(
		'form_nombre' => array('Model'=>'Usuario','field'=>'nombre'),
		'form_apellidos' => array('Model'=>'Usuario','field'=>'apellidos'),
		'form_email' => array('Model'=>'Usuario','field'=>'email'),
		'form_login' => array('Model'=>'Usuario','field'=>'login'),
		'form_tipo_autentificacion' => array('Model'=>'Usuario','field'=>'tipo_auth'),
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'docencia' => array('label' => 'docencia'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));*/
	$profes_refactored = array();
	foreach ($profesores as $profesor) {
		$profes_refactored[] = array($profesor['Profesor']['id'],
									  $profesor['Usuario']['nombre'],
								      $profesor['Usuario']['apellidos'],
								      $profesor['Usuario']['email'],
								      $profesor['Usuario']['tipo_auth'],
								      $profesor['Usuario']['login'],
								);
	}
	echo $this->Jquery->datatable(TRUE,
					   array(__('nombre'),
							 __('apellidos'),
							 __('email'),
							 __('form_tipo_autentificacion'),
							 __('login')
					   ),
					   $profes_refactored,
					   array(array(2, "asc")),
					   NULL,
					   array("/profesores/docencia", __("docencia"), "teaching", false),
					   TRUE,
					   array(array("Profesor.id", "text"),
					   	     array("Usuario.nombre", "text"),
					   	     array("Usuario.apellidos", "text"),
					   	     array("Usuario.email", "text"),
							 array("Usuario.tipo_auth", "select", $tipo_auth),
					   	     array("Usuario.login", "text"),
					   ),
					   "/profesores/add",
					   "/profesores/edit",
					   "/profesores/delete",
					   NULL);
	?>
</div>
