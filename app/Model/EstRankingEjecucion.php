<?php
/**
 * EstRankingEjecucion Model
 * Model View Class
 * @package models
 */
class EstRankingEjecucion extends AppModel
{
	var $name = 'EstRankingEjecucion';
	var $useTable = 'est_ranking_ejecuciones';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Asignatura'
	);

}
?>
