<?php
class ViewMediaResultadosPreguntasProblema extends AppModel
{
	var $name = 'ViewMediaResultadosPreguntasProblema';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Problema',
			'Asignatura'
	);

	/**
	 * Devuelve las estadísticas de los resultados de las preguntas de un problema
	 * @param integer $asignatura_id
	 * @param integer $problema_id
	 * @return array Estadísticas
	 * 	$result[$i]['numero_pregunta']==$i
	 *  $result[$i]['media_resultado']
	 */
	function estadisticas($asignatura_id,$problema_id)
	{
		$this->recursive=-1;
		$data=$this->find('all',array('conditions'=>
			array(	'ViewMediaResultadosPreguntasProblema.asignatura_id'=>$asignatura_id,
					'ViewMediaResultadosPreguntasProblema.problema_id'=>$problema_id)
			));
		$est=array();
		foreach ($data as $row)
		{
			$num_preg=$row['ViewMediaResultadosPreguntasProblema']['numero_pregunta'];
			$est[$num_preg]=$row['ViewMediaResultadosPreguntasProblema'];
		}

		return $est;
	}

}
?>
