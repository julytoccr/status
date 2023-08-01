<?php
class BloqueForm {
	public static $validFields = array(
		'add' => array(
			'Bloque' => array('nombre')
		),
		'edit' => array(
			'Bloque' => array('id', 'nombre')
		),
		'buscar_problemas' => array(
			'Bloque' => array('nombre'),
			'Problema' => array('problema_nombre', 'problema_descripcion', 'problema_enunciado', 'pregunta_nombre', 'profesor_nombre')
		),
	);
	
    public static function validation() {
        return array(
			'nombre' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
		);
    }
}
?>
