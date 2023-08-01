<?php
/**
 * Carpeta Model
 * @package models
 */

class Carpeta extends AppModel
{
	var $name = 'Carpeta';
	var $useTable = 'carpetas';
	public $actsAs = array('Containable');
	var $belongsTo = array(
			'Profesor',
			'Usuario' => array('foreignKey' => 'profesor_id'),
	);

	var $hasAndBelongsToMany = array(
			'Profesor' => array(
				'joinTable' => 'subscripciones',
				'foreignKey' => 'carpeta_id',
				'associationForeignKey' => 'profesor_id'
			),
			'Problema'
	);

	/**
	 * Devuelve los problemas asignados a la carpeta indicada
	 * @param integer $id Carpeta
	 * @return array Problemas asignados
	 * @return array
	 * 	Ejemplo retorno: $result[$i]['id']
	 */
	function problemas($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->contain('Problema');
		$this->recursive=1;
		$data=$this->findById($id);
		return $data['Problema'];
	}


	/**
	 * Devuelve los problemas asignados a las carpetas indicadas
	 * @param array $carpetas Array de carpetas
	 * 	Ejemplo: $carpetas[0]['id']
	 * @return array Problemas asignados
	 * @return array
	 * 	Ejemplo retorno: TODO:
	 */
	function problemas_carpetas($carpetas=array())
	{
		$this->contain('Problema');
		$this->recursive=1;
		$cond=array('OR'=>array());
		foreach ($carpetas as $carp)
		{
			$carpeta_id=$carp['id'];
			$cond['OR'][]=array('Carpeta.id'=>$carpeta_id);
		}
		$data=$this->findAll($cond);
		return $data;
	}

	/**
	 * Retorna todas las carpetas junto con los datos del profesor propietario
	 * @return array $data
	 * 	$data[$i]['Carpeta']['id']
	 *  $data[$i]['Carpeta']['nombre']
	 *  $data[$i]['Usuario']['id']
	 *  $data[$i]['Usuario']['nombre']
	 */
	function carpetas()
	{
		$this->recursive=1;
		$this->contain('Usuario');
		return $this->find('all');
	}
	
	/**
	 * Cambia el estado de una carpeta a compartida/descompartida segun su estado actual 
	 * @param $id id de la carpeta a tratar
	 * @return boolean exito
	 */
	function cambiar_compartido($id)
	{
		$this->recursive=-1;
		$data = $this->findById($id);
		if($data['Carpeta']['compartida']){
			$data['Carpeta']['compartida'] = 0;
		}
		else{
			$data['Carpeta']['compartida'] = 1;
		}
		return $this->save($data);
	}
	
	/**
	 * Dice si la carpeta pertenece al profesor 
	 * @param $id id de la carpeta a tratar
	 * @param $id_profesor id del profesor
	 * @return boolean es propia del profesor
	 */
	function propia($id, $id_profesor)
	{
		$this->recursive=-1;
		$data = $this->findById($id);
		return $data['Carpeta']['profesor_id'] == $id_profesor;
	}
	
	function buscar($id){
		//Revisar aixo, si treiem el recursive, l'opcio de copiar problemes al editor no funciona, falla la relacio amb susbscripcions
		$this->recursive = -1;
		return $this->findById($id);
	}

}
?>
