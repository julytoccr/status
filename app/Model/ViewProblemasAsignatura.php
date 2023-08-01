<?php
/**
 * Subscripcion Model
 * Model View Class
 *
 * Problemas asignados a una asignatura
 * (parejas asignatura_id, problema_id sin repeticiones)
 *
 * @package models
 */
class ViewProblemasAsignatura extends AppModel
{
	var $name = 'ViewProblemasAsignatura';
	public $actsAs = array('Containable');

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Asignatura',
			'Problema'
	);

	/**
	 * Devuelve todos los problemas de la asignatura indicada
	 * @param integer $asignatura_id Asignatura
	 * @param array $orden Orden tipo findAll
	 * @return array Problemas
	 * 	$result[$i]['Problema']['enunciado']
	 */
	function problemas_asignatura($asignatura_id,$orden=null,$recursive=null)
	{
		if ($recursive===null) $this->recursive=0;
		else $this->recursive=$recursive;
		
		$data=$this->find('all',array('conditions'=>array('ViewProblemasAsignatura.asignatura_id'=>$asignatura_id),'order'=>$orden));
		if (isset($data['ViewProblemasAsignatura']))
		{
			unset($data['ViewProblemasAsignatura']);
		}
		return $data;
	}
}
?>
