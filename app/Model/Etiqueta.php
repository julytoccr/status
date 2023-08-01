<?php
class Etiqueta extends AppModel
{
	var $name = 'Etiqueta';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array(
			'Texto' =>
			 array('className' => 'Texto',
					'conditions' => '',
					'order' => '',
					'foreignKey' => 'etiqueta_id',
					'dependent' => true,
					'exclusive' => '',
					'finderSql' => '',
					'counterSql' => ''),

	);

	/**
	 * Devuelve cierto si existe una etiqueta con esta etiqueta identificadora
	 * @param string cadena
	 * @return boolean Existe
	 */

	function existe($cadena){
		$this->recursive=-1;
		$data=$this->findByCadena($cadena);
		if(!$data){
			return false;
			
		}else{
			return true;
		}

	}

	/**
	 * Devuelve el identificador de la etiqueta en función de la cadena
	 *
	 * @param string $cadena de la etiqueta
	 *
	 * return integer $id de la etiqueta
	 */
	function etiqueta_id($cadena){
		$etiqueta=$this->findByCadena($cadena);
		$id=$etiqueta['Etiqueta']['id'];
		return $id;


	}

	/**
	 * Devuelve el la cadena identificatiba de la etiqueta 
	 *
	 * @param integer $id de la etiqueta
	 *
	 * return string $cadena de la etiqueta
	 */
	function etiqueta_cadena($id=null)
	{
				
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;

		return $data['Etiqueta']['cadena'];
	}

}
?>
