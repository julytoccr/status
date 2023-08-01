<?php
class Lenguaje extends AppModel
{
	var $name = 'Lenguaje';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array('Texto' => array('foreignKey' => 'lenguaje_id'));

	/**
	 * Devuelve el nombre estandard de un lenguaje
	 *
	 * @param integer $id del lenguaje
	 *
	 * return string $lenguaje_estandard del lenguaje
	 */
	function lenguaje_estandard($id=null){
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
		return $data['Lenguaje']['lenguaje_estandard'];
	}

	/**
	 * Devuelve el nombre propio de un lenguaje
	 *
	 * @param integer $id del lenguaje
	 *
	 * return string $lenguaje_propio del lenguaje
	 */
	function lenguaje_propio($id=null){
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
		return $data['Lenguaje']['lenguaje_propio'];
	}

	

	/**
	 * Devuelve el identificador del lenguaje del que se heredan textos
	 *
	 * @param integer $id del lenguaje
	 *
	 * return integer $lenguaje_hereda del lenguaje
	 */
	function lenguaje_hereda($id=null){
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
		return $data['Lenguaje']['lenguaje_hereda'];
	}


	/**
	 * Devuelve el nombre estandard del lenguaje del que se heredan textos
	 *
	 * @param integer $id del lenguaje
	 *
	 * return string $lenguaje_estandard del lenguaje
	 */	
	
	function lenguaje_hereda_texto($id=null){	
		$data=$this->findById($id);
		if(!$data) return;
		$id_hereda=$data['Lenguaje']['lenguaje_hereda'];
		if($id_hereda==0){
			$texto_hereda=' ';
		}else{
			$this->recursive=-1;
			$data=$this->findById($id_hereda);
			$texto_hereda=$data['Lenguaje']['lenguaje_estandard'];		
		}
		return $texto_hereda;
	}


	/**
	 * Devuelve el identificador del lenguaje del que recursivamente se acaba heredando
	 *
	 * @param integer $id del lenguaje
	 *
	 * return integer $lenguaje_hereda del lenguaje
	 */
	function lenguaje_hereda_recursivo($id=null){
		$hereda=0;
		while ($id!=0){
			$this->recursive=-1;
			$data=$this->findById($id);
			if(!$data) return;
			$id=$data['Lenguaje']['lenguaje_hereda'];
			if($id!=0){		
				$hereda=$id;
			}	
		}
		return $hereda;
	}
	/**
	 * Devuelve el identificador del lenguaje en función del lenguaje estandard
	 *
	 * @param string $lenguaje del lenguaje
	 *
	 * return integer $id del lenguaje
	 */
	function lenguaje_id($lenguaje){
		$leng=$this->findByLenguaje_estandard($lenguaje);
		$id=$leng['Lenguaje']['id'];
		return $id;


	}
	/**
	 * Devuelve cierto si un lenguaje ya existe en el sistema en funcion de su nombre estandard
	 * @param string lenguaje_estandard
	 * @return boolean Existe
	 */

	function existe_estandard($lenguaje){
		if($this->find('count',array('conditions'=>array('Lenguaje.lenguaje_estandard' => $lenguaje)))==0){
			return false;
		}else{
			return true;
		}
	}

}
?>
