<?php
App::uses('UsuarioForm', 'Form');
class AlumnoForm extends UsuarioForm {
	public static $validFields = array(
		'seleccionar' => array(
			'Alumno' => array('asignaturas')
		),
	);
}
?>
