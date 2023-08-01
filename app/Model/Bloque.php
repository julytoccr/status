<?php
/**
 * Bloque Model
 * @package models
 */
App::uses('BloqueForm', 'Form');
class Bloque extends AppModel
{
	var $name = 'Bloque';
	var $displayField='nombre';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array('Asignatura');

	var $hasMany = array(
		'Agrupacion',
		'RestriccionesBloque',
	);
	
	function __construct(){
		parent::__construct();
		$this->validate = BloqueForm::validation();
	}
	
	function setValidation($action){
		$this->validate = BloqueForm::$action();
	}

	/**
	 * Devuelve cierto si un grupo tiene acceso a un bloque
	 * en la fecha actual
	 * @param integer $id Bloque
	 * @param string $grupo Grupo
	 *
	 * @return boolean Tiene acceso
	 */
	function permiso($id,$grupo)
	{
		return $this->RestriccionesBloque->bloque_permiso($id,$grupo);
	}


	/**
	 * Devuelve el identificador de asignatura al que
	 * pertenece el bloque
	 * @param integer $id Identificador de bloque
	 * @return integer Identificador de asignatura
	 */
	function asignatura($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if ($data) return $data['Bloque']['asignatura_id'];
		else return null;
	}

	/**
	 * Devuelve los problemas asignados al bloque
	 * @param integer $id Ident. de bloque
	 * @param boolean $solo_visibles Cierto=únicamente muestra los problemas no ocultados
	 *
	 * @return array Array tipo findAll de problemas (a través de agrupación)
	 */
	function problemas($id=null,$solo_visibles=false)
	{
		if ($id===null) $id=$this->id;
		$this->Agrupacion->contain('Problema','ViewAgrupacionesPendientesNotificar');
		$this->Agrupacion->recursive=0;
		$conditions=array('Agrupacion.bloque_id'=>$id);
		if ($solo_visibles)
		{
			$conditions[]=array('Agrupacion.visible'=>1);
		}
		$data=$this->Agrupacion->find('all', compact('conditions'));
		return $data;
	}

	/**
	 * Retorna un array con los bloques de la asignatura
	 *
	 * @param integer $asignatura_id Asignatura
	 * @return array
	 * 	Ejemplo: $result[$i]['Bloque']['id']
	 */
	function bloques_asignatura($asignatura_id)
	{
		$this->recursive=-1;
		return $this->find('all', array(
			'conditions' => array('Bloque.asignatura_id'=>$asignatura_id),
			'order' => 'orden',
		));
	}

	/**
	 * Ordena los bloques de una asignatura según
	 * el orden indicado en el array
	 *
	 * @param integer $asignatura_id Asignatura
	 * @param array $orden Orden de los bloques.
	 * El array contiene los identificadores de los bloques en orden
	 * @return boolean Éxito
	 */
	function ordenar($asignatura_id,$orden){
		$i=1;
		$error=false;
		foreach ($orden as $bloque_id){
			$this->recursive=-1;
			$data=$this->find('first',array('conditions'=>
								array(
									'Bloque.id'=>$bloque_id,
									'Bloque.asignatura_id'=>$asignatura_id
								)
						));
			if ($data){
				$data['Bloque']['orden']=$i;
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
