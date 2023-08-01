<?php
/**
 * Subscripcion Model
 * @package models
 */

class Subscripcion extends AppModel
{
	var $name = 'Subscripcion';
	var $useTable = 'subscripciones';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Profesor',
			'Carpeta'
	);

}
?>
