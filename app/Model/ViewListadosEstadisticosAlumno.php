<?php
class ViewListadosEstadisticosAlumno extends AppModel
{
	var $name = 'ViewListadosEstadisticosAlumno';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Problema',
			'Asignatura'
	);

}
?>
