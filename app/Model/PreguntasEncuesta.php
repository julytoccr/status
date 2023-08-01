<?php
class PreguntasEncuesta extends AppModel
{
	var $name = 'PreguntasEncuesta';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Encuesta'
	);

	var $hasMany = array(
			'OpcionesEncuesta'
	);

	/**
	 * Retorna un array con las preguntas una encuesta
	 *
	 * @param integer $encuesta_id Encuesta
	 * @return array
	 * 	Ejemplo: $result[$i]['id']
	 */
	function preguntas_encuesta($encuesta_id){
		$this->recursive=-1;
		return $this->find('all',array('conditions'=>array('PreguntasEncuesta.encuesta_id'=>$encuesta_id),'order'=>array('PreguntasEncuesta.orden')));
	}

	/**
	 * Ordena las preguntas de una encuesta según
	 * el orden indicado en el array
	 *
	 * @param integer $encuesta_id Encuesta
	 * @param array $orden Orden de las opciones. El array contiene los identificadores de las preguntas en orden
	 * @return boolean Éxito
	 */
	function ordenar($encuesta_id,$orden){
		$i=1;
		$error=false;
		foreach ($orden as $opcion_id){
			$this->recursive=-1;
			$data=$this->find('first',array('conditions'=>
					array(	'PreguntasEncuesta.id'=>$opcion_id,
							'PreguntasEncuesta.encuesta_id'=>$encuesta_id)));

			if ($data){
				$data['PreguntasEncuesta']['orden']=$i;
				$ret=$this->save($data);
				if (!$ret) $error=true;
				$i++;
			}
			else $error=true;
		}
		return ! $error;
	}
}
?>
