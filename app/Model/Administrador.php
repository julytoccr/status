<?php
/**
 * Administrador Model
 * @package models
 */
class Administrador extends AppModel
{
	var $name = 'Administrador';
	var $useTable = 'administradores';
	public $actsAs = array('Containable');
	var $belongsTo = array('Usuario' =>array('foreignKey' => 'id'));
			 
	function crear($usuario){
		if (!$usuario_id=$this->Usuario->buscar($usuario['Usuario']['login'])){
			$usuario_id=$this->Usuario->guardar($usuario['Usuario']);
			if (! $usuario_id){
				$this->invalidate('_crear',__('inv_usuario_no_creado'));
				return null;
			}
		}
		$this->recursive=-1;
		if ($this->findById($usuario_id)){
			$this->invalidate('_crear',__('inv_administrador_existe'));
			return null;
		}
		$this->create();
		$data=array('id'=>$usuario_id);
		if ($this->save($data)){
			return $usuario_id;
		}
		return null;
	}
	
	function afterDelete()
	{
		$usuario_id=$this->id;
		$this->Usuario->delete($usuario_id);
	}
}
?>
