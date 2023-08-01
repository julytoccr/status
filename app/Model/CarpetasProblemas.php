<?php
/**
 * CarpetasProblemas Model
 * @package models
 */

class CarpetasProblemas extends AppModel
{
	var $name = 'CarpetasProblemas';
	var $useTable='carpetas_problemas';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Carpeta',
			'Problema'
	);

	/**
	 * Asigna un problema a una carpeta
	 *
	 * @param integer $id Problema
	 * @param integer $carpeta_id Carpeta
	 * @param boolean $es_alias El problema se asigna como alias
	 * @return boolean Éxito
	 */
	function asignar($problema_id,$carpeta_id,$es_alias)
	{
		$this->create();
		
		if((!isset($es_alias))||($es_alias == '')){
			$es_alias = 0;
		}
		
		$data['CarpetasProblemas']=array(
				'problema_id'=>$problema_id,
				'carpeta_id'=>$carpeta_id,
				'es_alias'=>$es_alias
		);
		return $this->save($data);
	}

	/**
	 * Elimina una asignación de un problema a una carpeta
	 *
	 * @param integer $id Problema
	 * @param integer $carpeta_id Carpeta
	 * @return boolean Éxito
	 */
	function desasignar($problema_id,$carpeta_id)
	{
		$this->recursive=-1;
		$data=$this->find('first',array('conditions'=>array(
			'CarpetasProblemas.problema_id'=>$problema_id,
			'CarpetasProblemas.carpeta_id'=>$carpeta_id)));
		if (!$data) return false;
		return $this->delete($data['CarpetasProblemas']['id']);
	}


	/**
	 * Devuelve si una asignación es un alias
	 *
	 * @param integer $id Problema
	 * @param integer $carpeta_id Carpeta
	 * @return boolean Cierto=Alias; Falso=No alias ; null=error
	 */
	function es_alias($problema_id,$carpeta_id)
	{
		$this->recursive=-1;
		$data=$this->find('first',array('conditions'=>array(
			'CarpetasProblemas.problema_id'=>$problema_id,
			'CarpetasProblemas.carpeta_id'=>$carpeta_id)));
		if (!$data) return null;
		return $data['CarpetasProblemas']['es_alias'];
	}
}
?>
