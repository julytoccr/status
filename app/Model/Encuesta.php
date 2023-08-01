<?php
App::uses('EncuestaForm', 'Form');
class Encuesta extends AppModel
{
	var $name = 'Encuesta';
	
	var $hasOne = array(
		'Profesor' =>
			array('className' => 'Profesor',
				'foreignKey' => 'id'
			)
	);
	
	var $hasMany = array(
			'EncuestasAsignatura',
			'PreguntasEncuesta'
	);
	
	function __construct(){
		parent::__construct();
		$this->validate = EncuestaForm::validation();
	}
}
?>
