<?php
class ViewBuscarProblema extends AppModel
{
	var $name = 'ViewBuscarProblema';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Problema'
	);


	/**
	 * Busca problemas que cumplan las condiciones expresadas.
	 *
	 * @param string $query Cadena a buscar en los campos $fields
	 * @param array $fields Ejemplo: $fields=array('problema_nombre','profesor_nombre')
	 * @return array Problemas que cumplen las condiciones.
	 */
	function buscar($query,$fields)
	{
		if ($query=='' || ! count($fields)) return array();

		$c=array();
		$query=addslashes($query);
		foreach ($fields as $k=>$f)
		{
			if($f=='1'){
				$c[] = array($k.' LIKE'=>'%'.$query.'%');
			}
		}
		$cond = array('OR'=>$c);
		return $this->find('all',array('conditions'=>$cond));
	}
}
?>
