<?php
class TipoRespuestaForm {
	public static $validFields = array(
		'add' => array(
			'TiposRespuesta' => array('nombre', 'descripcion', 'expresiones')
		),
		'edit' => array(
			'TiposRespuesta' => array('id','nombre', 'descripcion', 'expresiones')
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
