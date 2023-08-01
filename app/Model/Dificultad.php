<?php
/**
 * Dificultad Model
 * @package models
 */

class Dificultad extends AppModel
{
	var $name = 'Dificultad';
	var $useTable = 'dificultades';
	public $actsAs = array('Containable');

	var $order='id';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
			'Problema' =>
			 array('className' => 'Problema',
					'conditions' => '',
					'order' => '',
					'foreignKey' => '',
					'dependent' => '',
					'exclusive' => '',
					'finderSql' => '',
					'counterSql' => ''),

	);

}
?>
