<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('asignaturas'),
    "sidebar" => 'asignaturas/asignaturas'
));
$this->end();?>
<div class="asignaturas index">
	<?php
	/*$elements = $asignaturas;
	$fields = array(
		'form_nombre' => array('Model'=>'Asignatura','field'=>'nombre'),
		'form_descripcion' => array('Model'=>'Asignatura','field'=>'descripcion'),
		'form_asignar_auto_problemas' => array('Model'=>'Asignatura','field'=>'asignar_todos_publicados','formato'=>'booleano'),
		'form_fecha_inicio' => array('Model'=>'Asignatura','field'=>'fecha_inicio','formato'=>'fecha_hora'),
		'form_fecha_fin' => array('Model'=>'Asignatura','field'=>'fecha_fin','formato'=>'fecha_hora'),
	);
	$actions = array(
		'select' => array('label' => 'seleccionar'),
		'edit' => array('label' => 'editar'),
		'delete' => array('label' => 'eliminar', 'post' => true, 'confirm' => 'Are you sure you want to delete # %s?'),
	);
	echo $this->element('listado', compact('elements', 'fields', 'actions'));*/
	$asignaturas_refactored = array();
	foreach ($asignaturas as $asignatura) {
		$asignaturas_refactored[] = array($asignatura['Asignatura']['id'],
									  $asignatura['Asignatura']['nombre'],
								      $asignatura['Asignatura']['descripcion'],
								      $this->Formato->booleano($asignatura['Asignatura']['asignar_todos_publicados']),
								      $this->Formato->fecha_hora($asignatura['Asignatura']['fecha_inicio']),
								      $this->Formato->fecha_hora($asignatura['Asignatura']['fecha_fin']));
	}

	echo $this->Jquery->datatable(TRUE,
					   array(__('nombre'),
							 __('descripcion'),
							 __('form_asignar_auto_problemas'),
							 __('form_fecha_inicio'),
							 __('form_fecha_fin')),
					   $asignaturas_refactored,
					   array(array(1, "asc")),
					   NULL,
					   array("/asignaturas/select", __('seleccionar'), "select", true),
					   TRUE,
					   array(array("Asignatura.id", "text"),
					   	     array("Asignatura.nombre", "text"),
					   	     array("Asignatura.descripcion", "text"),
					   	     array("Asignatura.asignar_todos_publicados", "boolean"),
					   	     array("Asignatura.fecha_inicio", "text"),
					   	     array("Asignatura.fecha_fin", "text")
					   ),
					   "/asignaturas/add",
					   "/asignaturas/edit",
					   "/asignaturas/delete",
					   NULL);
	?>
</div>
