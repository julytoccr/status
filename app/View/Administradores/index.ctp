<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('administradores'),
    "sidebar" => 'administradores/administradores'
));
$this->end();?>
<div class="administradores index">
	<?php
	/*$elements = $administradores;
	$fields = array(
		'form_nombre' => array('Model'=>'Usuario','field'=>'nombre'),
		'form_apellidos' => array('Model'=>'Usuario','field'=>'apellidos'),
		'form_email' => array('Model'=>'Usuario','field'=>'email'),
		'form_tipo_autentificacion' => array('Model'=>'Usuario','field'=>'tipo_auth'),
		'form_login' => array('Model'=>'Usuario','field'=>'login'),
	);
	$actions = array(
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));*/
	$admins_refactored = array();
	foreach ($administradores as $admin) {
		$admins_refactored[] = array($admin['Administrador']['id'],
									  $admin['Usuario']['nombre'],
								      $admin['Usuario']['apellidos'],
								      $admin['Usuario']['email'],
								      $admin['Usuario']['tipo_auth'],
								      $admin['Usuario']['login'],
								);
	}
	echo $this->Jquery->datatable(TRUE,
					   array(__('nombre'),
							 __('apellidos'),
							 __('email'),
							 __('form_tipo_autentificacion'),
							 __('login')
					   ),
					   $admins_refactored,
					   array(array(2, "asc")),
					   NULL,
					   NULL,
					   TRUE,
					   array(array("Administrador.id", "text"),
					   	     array("Usuario.nombre", "text"),
					   	     array("Usuario.apellidos", "text"),
					   	     array("Usuario.email", "text"),
							 array("Usuario.tipo_auth", "select", $tipo_auth),
					   	     array("Usuario.login", "text"),
					   ),
					   "/administradores/add",
					   "/administradores/edit",
					   "/administradores/delete",
					   NULL);
	?>
</div>
