<?php
class ViewResultadosPreguntasProblema extends AppModel
{
	var $name = 'ViewResultadosPreguntasProblema';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Problema'
	);

	/**
	 * Devuelve los resultados de las preguntas de un problema
	 * @param integer $asignatura_id
	 * @param integer $problema_id
	 * @return array Estadísticas
	 * 	$result[$i]['resultado']
	 *  $result[$i]['numero_pregunta']
	 *
	 */
	function estadisticas($asignatura_id,$problema_id)
	{
		$this->recursive=-1;
		$data=$this->find('all',array('conditions'=>
			array('ViewResultadosPreguntasProblema.asignatura_id'=>$asignatura_id,
				'ViewResultadosPreguntasProblema.problema_id'=>$problema_id)
			));
		$est=array();
		foreach ($data as $row)
		{
			$est[]=$row['ViewResultadosPreguntasProblema'];
		}

		return $est;
	}
}
?>
