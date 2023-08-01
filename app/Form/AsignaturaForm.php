<?php
class AsignaturaForm {
	public static $validFields = array(
		'add' => array(
			'Asignatura' => array('nombre','descripcion','asignar_todos_publicados','fecha_inicio','fecha_fin','activar_fecha_inicio','activar_fecha_fin')
		),
		'edit' => array(
			'Asignatura' => array('id','nombre','descripcion','asignar_todos_publicados','fecha_inicio','fecha_fin','activar_fecha_inicio','activar_fecha_fin')
		),
	);
	
    public static function validation() {
        return array(
			'nombre' => array(
				'ruleName' => array(
					'rule' => 'notEmpty',
					'required' => true,
					'message' => __('form_obligatorio'),
				),
				'ruleName2' => array(
					'rule' => 'isUnique',
					'message' => __('form_unico'),
				)
			),
			'descripcion' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'fecha_inicio' => array(
				'rule' => array('datetime', 'ymd'),
				'allowEmpty' => true,
				'message' => __('form_fecha_no_valida'),
			),
			'fecha_fin' => array(
				'rule' => array('datetime', 'ymd'),
				'allowEmpty' => true,
				'message' => __('form_fecha_no_valida'),
			),
		);
    }
}
?>
