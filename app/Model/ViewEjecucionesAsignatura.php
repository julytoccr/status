<?php
class ViewEjecucionesAsignatura extends AppModel
{
	var $name = 'ViewEjecucionesAsignatura';
	var $useTable = 'view_ejecuciones_asignatura';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Usuario',
			'Problema'
	);
}
?>
