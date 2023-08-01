<?php
/**
 * EstRankigPuntuacion Model
 * Model View Class
 * @package models
 */

class EstRankingPuntuacion extends AppModel
{
	var $name = 'EstRankingPuntuacion';
	var $useTable = 'est_ranking_puntuacion';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Asignatura'
	);

}
?>
