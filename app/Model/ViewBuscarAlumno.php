<?php
class ViewBuscarAlumno extends AppModel
{
	var $name = 'ViewBuscarAlumno';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Grupo',
			'Asignatura'
	);

	/**
	 * Busca alumnos que cumplan las condiciones expresadas.
	 *
	 * @param string $query Cadena a buscar en los campos $fields
	 * @param array $fields Ejemplo: $fields=array('problema_nombre','profesor_nombre')
	 * @param integer $asignatura_id Buscar solo los alumnos de esta asignatura
	 * @return array Alumnos que cumplen las condiciones.
	 */
	function buscar($query,$fields,$asignatura_id=null)
	{
		if ($query=='' || ! count($fields)) return array();

		$c='';
		$query=addslashes($query);
		foreach ($fields as $k=>$f)
		{
			if($f=='1'){
				if ($c) $c .= ' OR ';
				$c .= " $k like '%$query%'";
			}
		}
		$cond=array();
		if ($asignatura_id!==null) $cond['ViewBuscarAlumno.asignatura_id']=$asignatura_id;

		$cond=am(array($c),$cond);
		return $this->find('all', array('conditions'=>$cond));
	}


}
?>
