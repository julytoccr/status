<?php
App::uses('Sanitize', 'Utility');
App::import('Vendor', 'estatus/clases/fechas');
App::uses('RestriccionesForm', 'Form');
/**
 * RestriccionesBloque Model
 * @package models
 */

class RestriccionesBloque extends AppModel
{
	var $name = 'RestriccionesBloque';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
		'Bloque',
	);
	
	function __construct(){
		parent::__construct();
		$this->validate = RestriccionesForm::validation();
	}
	
	function setValidation($action){
		$this->validate = RestriccionesForm::$action();
	}

	/**
	 * Retorna un array con las restricciones del bloque
	 *
	 * @param integer $bloque_id Bloque
	 * @return array
	 * 	Ejemplo: $result[$i]['RestriccionBloque']['id']
	 */
	function restricciones_bloque($bloque_id)
	{
		$this->recursive=-1;
		return $this->find('all',array('conditions'=>array('RestriccionesBloque.bloque_id'=>$bloque_id)));
	}


	/**
	 * Guarda una restricción
	 *
	 * @param array $data Datos a guardar ($data['bloque_id'],$data['id'],$data['fecha_inicio']...)
	 * @return boolean Éxito
	 */
	function guardar($data){
		if ($data['fecha_inicio']!==null && $data['fecha_fin']!==null && $data['fecha_inicio']>$data['fecha_fin']){
			$this->invalidate('fecha_inicio',__('inv_fecha_ini_anterior_fin'));
			return false;
		}
		if (!isset($data['id'])){
			$this->create();
			$id=null;
			$bloque_id=$data['bloque_id'];
		}
		else{
			$id=$data['id'];
			$data2=$this->findById($id);
			if (!$data2) return false;
			$bloque_id=$data2['RestriccionesBloque']['bloque_id'];
		}
		$restricciones=$this->restricciones_bloque($bloque_id);
		//Mirar si alguna existente incluye la nueva
		foreach ($restricciones as $r)
		{
			$r=$r['RestriccionesBloque'];
			//Si se trata de una restriccion que se puede solapar...
			if ($r['id'] != $id && ($r['grupo']==null || $r['grupo']==$data['grupo'])){
				if (IntervaloFechas::incluido(array($data['fecha_inicio'],$data['fecha_fin']),array($r['fecha_inicio'],$r['fecha_fin']))){
					//No se ha de guardar nada, está totalmente incluido en otro
					if ($id){
						$this->delete($id);
					}
					return true;
				}
			}
		}

		//Mirar si la nueva incluye a alguna existente
		foreach ($restricciones as $i=>$r)
		{
			$r=$r['RestriccionesBloque'];
			//Si se trata de una restriccion que se puede solapar...
			if ($r['id'] != $id && ($data['grupo']==null || $data['grupo']==$r['grupo'])){
				if (IntervaloFechas::incluido(array($r['fecha_inicio'],$r['fecha_fin']),
				array($data['fecha_inicio'],$data['fecha_fin']))){
					//Hay un intervalo existente que está incluido en el nuevo
					unset($restricciones[$i]);
					$this->delete($r['id']);
				}
			}
		}
		
		//Mirar si se puede juntar con una existente
		do
		{
			$fusionado=false;
			foreach ($restricciones as $i=>$r){
				$r=$r['RestriccionesBloque'];
				//Si se trata de una restriccion que se puede solapar...
				if ($r['id'] != $id && ($data['grupo']==$r['grupo'])){
					$contiguo=IntervaloFechas::contiguo(array($r['fecha_inicio'],$r['fecha_fin']),array($data['fecha_inicio'],$data['fecha_fin']));
					if ($contiguo){
						//Hay un intervalo existente con el que podemos solapar
						$this->delete($r['id']);
						$data['fecha_inicio']=$contiguo[0];
						$data['fecha_fin']=$contiguo[1];
						unset($restricciones[$i]);
						$fusionado=true;
					}
				}
			}
		}while ($fusionado);
		return $this->save($data);
	}



	/**
	 * Devuelve cierto si un grupo tiene acceso a un bloque
	 * en la fecha actual
	 * @param integer $bloque_id Bloque
	 * @param string $grupo Nombre del Grupo
	 *
	 * @return boolean Tiene acceso
	 * TODO: Función que devuelva todos los bloques permitidos
	 */
	function bloque_permiso($bloque_id,$grupo){
		//~ uses('sanitize');
		$bloque_id=Sanitize::paranoid($bloque_id);
		$grupo=Sanitize::paranoid($grupo);

		$cond=array(
			'RestriccionesBloque.bloque_id'=>$bloque_id,
			array(
				'OR' => array(
					'RestriccionesBloque.grupo' => $grupo,
					'RestriccionesBloque.grupo is null',
				)
			),
			array(
				'OR' => array(
					array('RestriccionesBloque.fecha_inicio IS NULL'),
					array('RestriccionesBloque.fecha_inicio<= NOW()'),
				)
			),
			array(
				'OR' => array(
					array('RestriccionesBloque.fecha_fin IS NULL'),
					array('RestriccionesBloque.fecha_fin>=NOW()'),
				)
			)
		);

		if ($this->find('count', array('conditions'=>$cond))!=0){
			return true;
		}
		else{
			//Caso que no hay ninguna restriccion -> acceso para todos
			return ($this->find('count',array('conditions'=>array('RestriccionesBloque.bloque_id'=>$bloque_id)))==0);
		}
	}
}
?>
