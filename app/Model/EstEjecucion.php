<?php
/**
 * EstEjecucion Model
 * Model View Class
 * @package models
 */
class EstEjecucion extends AppModel
{
	var $name = 'EstEjecucion';
	var $useTable = 'est_ejecuciones';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Ejecucion',
			'Alumno',
			'Usuario',
			'Agrupacion',
			'Bloque',
			'Problema',
			'Asignatura',
			'Dificultad',
	);
}
?>
