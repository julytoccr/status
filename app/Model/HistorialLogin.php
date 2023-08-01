<?php
class HistorialLogin extends AppModel
{
	var $name = 'HistorialLogin';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Usuario'
	);

	/**
	 * Crea un nuevo logon
	 * @param integer $usuario_id Usuario que se ha logueado
	 * @param string $ip IP desde donde se loguea
	 *
	 * @return boolean Éxito
	 */
	function crear($usuario_id,$ip)
	{
		$this->create();
		$data['usuario_id']=$usuario_id;
		$data['ip']=$ip;
		return $this->save($data);
	}

}
?>
