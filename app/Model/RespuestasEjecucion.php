<?php
/**
 * RespuestasEjecucion Model
 * @package models
 */

class RespuestasEjecucion extends AppModel
{
	var $name = 'RespuestasEjecucion';
	var $useTable = 'respuestas_ejecuciones';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Ejecucion'
	);

	/**
	 * Elimina todas las respuestas asociadas a una ejecución
	 * @param integer $ejecucion_id Ejecución
	 * @return boolean Éxito
	 */
	function eliminar_todos($ejecucion_id)
	{
		$error=false;
		$this->recursive=-1;
		$data=$this->find('all',
			array('conditions' => array( 'RespuestasEjecucion.ejecucion_id' => $ejecucion_id) )
		);
		foreach ($data as $line)
		{
			if (!$this->del($line['RespuestasEjecucion']['id'])) $error=true;
		}
		return ! $error;
	}

	/**
	 * Busca la respuesta para una ejecución y número de pregunta
	 * @param integer $ejecucion_id Ejecución
	 * @param integer $numero Número de pregunta (la 1ª es la 0)
	 * @return array RespuestaEjecucion
	 * 		$result['valor']
	 */
	function buscar($ejecucion_id, $numero_pregunta = NULL) {
		$data = NULL;
		
		$this -> recursive    = -1;
		$this -> cacheQueries = false;

		if ($numero_pregunta === NULL) {

			$data_ = $this->find('all',
				array('conditions' => array('RespuestasEjecucion.ejecucion_id'=>$ejecucion_id))
			);

			if ($data_) {
				$data = array();
				foreach ($data_ as &$respuesta) {
					$respuesta = $respuesta['RespuestasEjecucion'];
					$data[$respuesta['numero_pregunta']] = $respuesta;
				}
			}
		}
		else {
			$cond = array(	'RespuestasEjecucion.ejecucion_id' => $ejecucion_id,
							'RespuestasEjecucion.numero_pregunta' => $numero_pregunta);

			$data_ = $this->find('first', array('conditions'=>$cond));
			
			if ($data_) $data = $data_['RespuestasEjecucion'];
		}

		$this -> cacheQueries = true;

		return $data;
	}

	/**
	 * Guarda una respuesta para la ejecución indicada.
	 * En caso de existir la respuesta en la BD, la reemplaza.
	 * Además incrementa el número de intentos si $incrementar es cierto.
	 * Si el número de intentos sobrepasa el máximo permitido por la pregunta
	 * entonces no guardará nada y retornará falso
	 *
	 * @param integer $ejecucion_id Ejecución
	 * @param integer $numero Número de pregunta (la 1ª es la 0)
	 * @param string $valor Valor de la respuesta
	 * @param boolean $incrementar Incrementar el número de intentos usados
	 * @param integer $resultado Resultado de la respuesta entre 0 y 1
	 * @return boolean Éxito
	 */
	function guardar($ejecucion_id, $numero_pregunta, $valor, $incrementar, $resultado=null, $data = NULL) {
		if ($data === NULL)
			$data = $this -> buscar($ejecucion_id, $numero_pregunta);

		if (!$data) {
			$intentos = ($incrementar) ? 1 : 0;

			$data['RespuestasEjecucion'] = array(
				'ejecucion_id'    => $ejecucion_id,
				'numero_pregunta' => $numero_pregunta,
				'valor'           => $valor,
				'intento'         => $intentos);

			if ($resultado !== null)
				$data['RespuestasEjecucion']['resultado'] = $resultado;

			$this -> create();
			return $this -> save($data);
		}
		else {
			if ($this -> intentos_disponibles($data['id'])) {
				$data['valor'] = $valor;
				if ($incrementar) {
					$data['intento']++;
				}
				if ($resultado !== null) $data['resultado'] = $resultado;
				return $this -> save($data);
			}
			else {
				return false;
			}
		}
	}

	/**
	 * Devuelve el número de intentos disponibles en una respuesta
	 *
	 * @param integer $id RespuestaEjecucion
	 * @return integer Número de intentos disponibles
	 */
	function intentos_disponibles($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$this->cacheQueries=false;
		$data=$this->findById($id);
		$this->cacheQueries=true;
		if (!$data) return null;
		$ejecucion_id=$data['RespuestasEjecucion']['ejecucion_id'];
		$numero_pregunta=$data['RespuestasEjecucion']['numero_pregunta'];

		$problema_id=$this->Ejecucion->problema($ejecucion_id);
		$preguntas=$this->Ejecucion->Agrupacion->Problema->preguntas($problema_id);
		if (!$preguntas) return null;
		return $preguntas[$numero_pregunta]['intentos'] - $data['RespuestasEjecucion']['intento'];
	}

	/**
	 * Consume todos los intentos disponibles de una respuesta y le asigna el resultado obtenido
	 * @param integer $id RespuestaEjecucion
	 * @return boolean Éxito
	 */
	function finalizar($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$this->cacheQueries=false;
		$data=$this->findById($id);
		$this->cacheQueries=true;
		if (!$data) return null;

		$data['RespuestasEjecucion']['intento'] += $this->intentos_disponibles($id);
		return $this->save($data);
	}
}
?>
