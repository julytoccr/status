<?php
/**
 * EstAlumno Model
 * Model View Class
 * @package models
 */

class EstAlumno extends AppModel
{
	var $name = 'EstAlumno';
	var $useTable = 'est_alumnos';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Asignatura'
	);


	/**
	 * Devuelve las estadísticas de varios alumnos en una sola query
	 * @param array $alumnos Array de identificadores de alumnos
	 * @return array
	 *	$result[$i]['EstAlumno']['alumno_id']
	 *  $result[$i]['EstAlumno']['puntuacion_total']
	 */
	function estadisticas_alumnos($alumnos)
	{
		//uses('sanitize');

		$cond='';
		foreach ($alumnos as $i=>$alumno_id)
		{
			//$alumno_id=Sanitize::paranoid($alumno_id);

			if ($i>0)
			{
				$cond.=' OR ';
			}
			$cond.="EstAlumno.alumno_id = '$alumno_id' ";
		}
		$this->recursive=-1;
		$data=$this->find('all',array('conditions'=>$cond));
		return $data;
	}
}
?>
