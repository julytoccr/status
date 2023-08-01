<?php
class PreguntaForm {
	public static $validFields = array(
	);
	
    public static function validation() {
        return array(
			'enunciado' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'expresiones_respuesta' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'intentos' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'peso' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
		);
    }
}
?>
