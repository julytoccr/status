<?php
class ViewPuntuacionesProblemasAlumno extends AppModel
{
	var $name = 'ViewPuntuacionesProblemasAlumno';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Asignatura',
			'Grupo',
			'Problema'
	);

}
?>
