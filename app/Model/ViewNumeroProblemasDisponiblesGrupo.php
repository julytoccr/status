<?php
class ViewNumeroProblemasDisponiblesGrupo extends AppModel
{
	var $name = 'ViewNumeroProblemasDisponiblesGrupo';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Asignatura',
			'Grupo'
	);

}
?>
