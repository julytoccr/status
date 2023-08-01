<?php
/**
 * EstRankingNota Model
 * Model View Class
 * @package models
 */
class EstRankingNota extends AppModel
{
	var $name = 'EstRankingNota';
	var $useTable = 'est_ranking_nota';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Asignatura'
	);

}
?>
