<?php
class SintaxisRespuestaForm {
	public static $validFields = array(
		'add' => array(
			'SintaxisRespuesta' => array('nombre', 'descripcion', 'expresiones')
		),
		'edit' => array(
			'SintaxisRespuesta' => array('id','nombre', 'descripcion', 'expresiones')
		),
	);
	
    public static function validation() {
        return array(
			'nombre' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'descripcion' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'expresiones' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
		);
    }
}
?>
