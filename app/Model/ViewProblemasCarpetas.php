<?php
/**
 * ViewProblemasCarpetas Model
 * Model View Class
 * @package models
 */

class ViewProblemasCarpetas extends AppModel
{
	var $name = 'ViewProblemasCarpetas';

	/**
	 * Devuelve todos los problemas
	 * @param array $cond Condiciones tipo findAll
	 * @return array Ejemplo: $result[$i]['id']
	 */
	function problemas($conditions=null)
	{
		$this->recursive=-1;
		$data=$this->find('all', compact('conditions'));
		$result=array();
		foreach ($data as $line)
		{
			$result[]=$line['ViewProblemasCarpetas'];
		}
		return $result;
	}

	function problemas_publicados()
	{
		//TODO: Is Null
		return $this->problemas(array('ViewProblemasCarpetas.published IS NOT NULL'));

	}


}
?>
