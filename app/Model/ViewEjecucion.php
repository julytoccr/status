<?php
class ViewEjecucion extends AppModel
{
	var $name = 'ViewEjecucion';
	var $useTable = 'view_ejecuciones';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Ejecucion',
			'Alumno',
			'Agrupacion',
			'Bloque',
			'Problema',
			'Asignatura'
	);

}
?>
