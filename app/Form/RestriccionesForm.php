<?php
class RestriccionesForm {
	public static $validFields = array(
		'add' => array(
			'RestriccionesBloque' => array('grupo','activar_fecha_inicio','fecha_inicio','activar_fecha_fin','fecha_fin')
		),
		'edit' => array(
			'RestriccionesBloque' => array('id','grupo','activar_fecha_inicio','fecha_inicio','activar_fecha_fin','fecha_fin')
		),
	);
	
    public static function validation() {
        return array(
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
