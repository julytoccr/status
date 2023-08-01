<?php
class ViewPuntuacionesProblemasBloquesAlumno extends AppModel
{
	var $name = 'ViewPuntuacionesProblemasBloquesAlumno';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Asignatura',
			'Grupo',
			'Problema',
			'Bloque'
	);

}
?>
