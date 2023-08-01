<?php
class EncuestaForm {
	public static $validFields = array(
		'add' => array(
			'Encuesta' => array('nombre', 'contenido')
		),
		'edit' => array(
			'Encuesta' => array('nombre', 'contenido')
		),
	);
	
    public static function validation() {
        return array(
			'nombre' => array(
				'rule' => 'alphaNumeric',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'contenido' => array(
				'rule' => 'alphaNumeric',
				'required' => true,
				'message' => __('form_obligatorio'),
			)
		);
    }
}
?>
