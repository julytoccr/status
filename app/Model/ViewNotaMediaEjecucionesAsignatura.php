<?php
class ViewNotaMediaEjecucionesAsignatura extends AppModel
{
	var $name = 'ViewNotaMediaEjecucionesAsignatura';
	var $useTable = 'view_nota_media_ejecuciones_asignatura';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Asignatura'
	);

}
?>
