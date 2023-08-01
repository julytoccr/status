<?php
class ViewNumeroUsuariosGrupo extends AppModel
{
	var $name = 'ViewNumeroUsuariosGrupo';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Grupo',
			'Asignatura'
	);

}
?>
