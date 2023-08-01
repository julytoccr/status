<?php
/**
 * Agrupacion Model
 * @package models
 */
class Agrupacion extends AppModel
{
	var $useTable = 'agrupaciones';
	public $actsAs = array('Containable');

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Bloque',
			'Problema'
	);

	var $hasOne=array(
			'ViewAgrupacionesPendientesNotificar' => array(
				'foreignKey' => 'agrupacion_id',
			)
	);

	var $hasMany = array(
			'Ejecucion'
	);

	/**
	 * Devuelve el bloque al que pertenece la agrupacion
	 *
	 * @param integer $id Agrupacion
	 * @return integer Bloque
	 */
	function bloque($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$data=$this->findById($id);
		if (! $data) return null;

		return $data['Agrupacion']['bloque_id'];
	}


	/**
	 * Devuelve el problema al que pertenece la agrupacion
	 *
	 * @param integer $id Agrupacion
	 * @return integer Identificador de problema
	 */
	function problema($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$data=$this->findById($id);
		if (! $data) return null;

		return $data['Agrupacion']['problema_id'];
	}

	/**
	 * Devuelve la asignatura a la que pertenece la agrupacion
	 *
	 * @param integer $id Agrupacion
	 * @return integer Problema
	 */
	function asignatura($id=null)
	{
		$bloque_id=$this->bloque($id);
		return $this->Bloque->asignatura($bloque_id);
	}

	/**
	 * Devuelve la fecha límite de ejecución a partir de la fecha actual de un nuevo problema.
	 *
	 * @param integer $id Agrupación
	 * @return integer Timestamp de fecha límite de ejecución
	 */
	function fecha_limite($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;

		$limite_minutos=$data['Agrupacion']['limite_minutos'];

		$expiracion=$limite_minutos * 60 + time();
		return $expiracion;
	}

	/**
	 * Asigna un problema a un bloque. Si la asignación ya existe, se reemplaza
	 *
	 * @param integer $bloque_id Bloque
	 * @param integer $problema_id Problema
	 * @param integer $limite_minutos Tiempo límite de ejecución en minutos
	 * @param boolean $visible El problema es visible por los alumnos
	 *
	 * @return boolean Éxito
	 */
	function guardar($bloque_id,$problema_id,$limite_minutos,$visible=true)
	{
		$this->recursive=-1;
		if ($data=$this->find('first',array('conditions'=>array('Agrupacion.bloque_id'=>$bloque_id,
										'Agrupacion.problema_id'=>$problema_id))))
		{
			//ya existe. actualizar limite y visible
			$data['Agrupacion']['limite_minutos']=$limite_minutos;
			$data['Agrupacion']['visible']=$visible;
			unset($data['Agrupacion']['created']);
			unset($data['Agrupacion']['modified']);
			return $this->save($data);
		}
		else
		{
			$data['Agrupacion']=array(
				'bloque_id'=>$bloque_id,
				'problema_id'=>$problema_id,
				'limite_minutos'=>$limite_minutos,
				'visible'=>$visible
			);
			$this->create();
			return $this->save($data);
		}
	}


	/**
	 * Busca la agrupación de bloque y problema indicados
	 * @param integer $bloque_id Bloque
	 * @param integer $problema_id Problema
	 *
	 * @return array Agrupación
	 * 		$result['id']
	 */
	function buscar($bloque_id,$problema_id)
	{
		$this->recursive=-1;
		$data=$this->find('first',array('conditions'=>array('Agrupacion.bloque_id'=>$bloque_id,'Agrupacion.problema_id'=>$problema_id)));
		if (!$data) return null;
		return $data['Agrupacion'];
	}

	/**
	 * Establece la visibilidad de la agrupación
	 * @param integer $bloque_id Bloque
	 * @param integer $problema_id Problema
	 * @param boolean $visible Agrupación visible por el alumno?
	 *
	 * @return boolean Éxito
	 */
	function visible($bloque_id,$problema_id,$visible)
	{
		$data=$this->buscar($bloque_id,$problema_id);
		$data['visible']=$visible;
		unset($data['created']);
		unset($data['modified']);
		return $this->save($data);
	}

	/**
	 * Establece la notificación de la agrupación
	 * @param integer $bloque_id Bloque
	 * @param integer $problema_id Problema
	 * @param boolean $notificado Agrupación ya notificada?
	 *
	 * @return boolean Éxito
	 */
	function notificado($bloque_id,$problema_id,$notificado)
	{
		$data=$this->buscar($bloque_id,$problema_id);
		$data['notificado']=$notificado;
		unset($data['created']);
		unset($data['modified']);
		return $this->save($data);
	}
	
	/**
	 * Cambia la asignación de un problema asignado
	 * @param integer $bloque_id_or Bloque origen
 	 * @param integer $bloque_id_dest Bloque destino
	 * @param integer $problema_id Problema
	 *
	 * @return boolean Éxito
	 */
	function reasignar($bloque_id_or, $bloque_id_dest, $problema_id)
	{
		if($this->buscar($bloque_id_dest, $problema_id) == null)
		{
			$data = $this->buscar($bloque_id_or, $problema_id);
			
			$data['bloque_id'] = $bloque_id_dest;
			unset($data['modified']);
		
			return $this->save($data);
		}
		else
		{
			return false;
		}
	}
}
?>
