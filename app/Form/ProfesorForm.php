<?php
App::uses('UsuarioForm', 'Form');
class ProfesorForm extends UsuarioForm {
	public static $validFields = array(
		'seleccionar' => array(
			'Profesor' => array('asignaturas')
		)
	);
}
?>
