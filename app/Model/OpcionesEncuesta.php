<?php
App::uses('OpcionesEncuestaForm', 'Form');
class OpcionesEncuesta extends AppModel
{
	var $name = 'OpcionesEncuesta';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Encuesta',
	);

	var $hasMany = array(
		'VotacionesEncuesta'
	);

	function __construct(){
		parent::__construct();
		$this->validate = OpcionesEncuestaForm::validation();
	}
	
	/**
	 * Retorna un array con las opciones de la pregunta de una encuesta
	 *
	 * @param integer $preguntas_encuesta_id Pregunta de una encuesta
	 * @return array
	 * 	Ejemplo: $result[$i]['id']
	 */
	function opciones_encuesta($preguntas_encuesta_id)
	{
		$this->recursive=-1;
		return $this->find('all',array('conditions'=>array('OpcionesEncuesta.preguntas_encuesta_id'=>$preguntas_encuesta_id),'order'=>array('OpcionesEncuesta.orden')));
	}

	/**
	 * Ordena las opciones de una pregunta de una encuesta según
	 * el orden indicado en el array
	 *
	 * @param integer $preguntas_encuesta_id Pregunta de una encuesta
	 * @param array $orden Orden de las opciones. El array contiene los identificadores de las opciones en orden
	 * @return boolean Éxito
	 */
	function ordenar($preguntas_encuesta_id,$orden){
		$i=1;
		$error=false;
		foreach ($orden as $opcion_id){
			$this->recursive=-1;
			$data=$this->find('first',array('conditions'=>
					array(	'OpcionesEncuesta.id'=>$opcion_id,
							'OpcionesEncuesta.preguntas_encuesta_id'=>$preguntas_encuesta_id)));
			if ($data)
			{
				$data['OpcionesEncuesta']['orden']=$i;
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
