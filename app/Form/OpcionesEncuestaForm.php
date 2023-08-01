<?php
class OpcionesEncuestaForm {
	public static $validFields = array(
		'add' => array(
			'OpcionesEncuesta' => array('contenido','preguntas_encuesta_id')
		),
		'edit' => array(
			'OpcionesEncuesta' => array('contenido','id')
		),
	);
	
    public static function validation() {
        return array(
			'contenido' => array(
				'rule' => 'alphaNumeric',
				'required' => true,
				'message' => __('form_obligatorio'),
			)
		);
    }
}
?>
