<?php
class ViewEstadisticasProblema extends AppModel
{
	var $name = 'ViewEstadisticasProblema';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Asignatura',
			'Problema'
	);

	/**
	 * Devuelve las estadísticas de los problemas
	 * ordenados por $orden
	 * @param integer $asignatura_id Asignatura
	 * @param mixed $orden Orden tipo findAll
	 * @return array
	 * 	$result[$i]['Problema']['nombre']
	 *  $result[$i]['Estadisticas']['numero_ejecuciones']
	 *  $result[$i]['Estadisticas']['nota_media']
	 *
	 */
	function problemas($asignatura_id, $orden)
	{
		$this->recursive=0;
		$this->contain('Problema');
		$data=$this->find('all',array('conditions'=> array('ViewEstadisticasProblema.asignatura_id'=>$asignatura_id),'order' => $orden));
		foreach ($data as $i=>$row)
		{
			$data[$i]['Estadisticas']=$row['ViewEstadisticasProblema'];
			unset ($data[$i]['ViewEstadisticasProblema']);
		}
		return $data;
	}

}
?>
