<?php
/**
 * Profesor Model
 * @package models
 */

class Profesor extends AppModel
{
	var $name = 'Profesor';
	public $actsAs = array('Containable');
	var $belongsTo = array('Usuario' =>array('foreignKey' => 'id'));

	var $hasMany = array(
		'Carpeta',
		//'Subscripcion' => array('foreignKey' => 'profesor_id'),
		'Problema',
	);

	var $hasAndBelongsToMany = array(
			'Asignatura' =>
			 array(	'foreignKey' => 'profesor_id',
					'joinTable' => 'docencia'),
			'Carpeta' =>
			 array(	'foreignKey' => 'profesor_id',
					'joinTable' => 'subscripciones',
					'dependent' => true,
					'associationForeignKey' => 'carpeta_id'),
			'CarpetaNoSubscrita' =>
			 array('className' => 'Carpeta',
					'joinTable' => 'carpetas_no_subscritas',
					'dependent' => false,
					'deleteQuery' => '')
	);

	/**
	 * Retorna un array con las carpetas del profesor y cada una
	 * de ellas con sus problemas asignados
	 *
	 * @param integer $id Profesor
	 * @return array
	 * 	Ejemplo retorno:
	 * 	$result[$i]['id']
	 *  $result[$i]['Problema'][0]['id']
	 */
	function carpetas_propias($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->contain('Carpeta');
		$this->recursive=1;
		$data=$this->findById($id);
		return $data['Carpeta'];
	}


	/**
	 * Retorna un array con las carpetas externas al profesor
	 *
 	 * @param integer $id Profesor
	 * @return array
	 * 	Ejemplo retorno: $result[$i]['id']
	 */
	function carpetas_externas($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->contain('Carpeta');
		$this->recursive=1;
		$data=$this->findAll(array('Profesor.id'=>"<> $id"));
		$result=array();
		foreach ($data as $line)
		{
			$result=am($result,$line['Carpeta']);
		}
		return $result;
	}

	/**
	 * Retorna un array con las carpetas externas al profesor
	 * que están subscritas
	 *
 	 * @param integer $id Profesor
	 * @return array
	 * 	Ejemplo retorno: $result[$i]['id']
	 */
	function carpetas_externas_subscritas($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->contain('Carpeta');
		$this->recursive=1;
		$data=$this->findById($id);
		return $data['Carpeta'];
	}

	/**
	 * Retorna un array con las carpetas externas al profesor
	 * que NO están subscritas y que son públicas
	 *
 	 * @param integer $id Profesor
	 * @return array
	 * 	Ejemplo retorno: $result[$i]['id']
	 */
	function carpetas_externas_no_subscritas($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->contain('CarpetaNoSubscrita');
		$this->recursive=1;
		$data=$this->findById($id);
		return $data['CarpetaNoSubscrita'];
	}

	/**
	 * Si la carpeta indicada no está subscrita, la subscribe.
	 * De lo contrario elimina dicha subscripción.
	 * La carpeta debe ser ajena
	 *
	 * @param integer $id Profesor
	 * @param integer $carpeta_id Carpeta
	 * @return boolean Éxito
	 */
	function subscribir($id,$carpeta_id)
	{
		//$this->cacheQueries=false;
		$this->Carpeta->recursive=-1;
		$data=$this->Carpeta->findById($carpeta_id);
		if (!$data) return false; //no existe carpeta
		if ($data['Carpeta']['profesor_id']===$id) return false; //la carpeta no es ajena

		$this->contain('Carpeta');
		$this->setConditions(array('Carpeta.id'=>$carpeta_id),'Carpeta','hasAndBelongsToMany');
		$this->recursive=1;
		$data=$this->findById($id);
		if ($data['Carpeta']){
			//la carpeta ya está subscrita. Elimina la subscripción
			return $this->deleteAssoc($id,'Carpeta',$carpeta_id);
		}
		else{
			//$data = array('Profesor'=>array('id'=>$id),'Carpeta'=>array('Carpeta'=>array('carpeta_id'=>$carpeta_id)));
			//return $this->save($data);
			return $this->addAssoc($id,'Carpeta',$carpeta_id);
		}
	}


	/**
	 * Devuelve las asignaturas en las que da docencia el profesor
	 *
	 * @param integer $id Profesor
	 * @return array Asignaturas
	 * 	$result[$i]['nombre']
	 */
	function asignaturas($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=1;
		$this->contain('Asignatura');
		$data=$this->findById($id);
		return $data['Asignatura'];
	}

	/**
	 * Establece la docencia del profesor
	 * @param integer $profesor_id Profesor
	 * @param array $asignaturas Array de identificadores de asignatura ($asignaturas[$i])
	 *
	 * @return boolean Éxito
	 */
	function guardar_docencia($profesor_id,$asignaturas)
	{
		$this->recursive=-1;
		$data=$this->findById($profesor_id);
		if (!$data) return false;

		$data['Asignatura']['Asignatura']=$asignaturas;
		return $this->save($data);
	}

	/**
	 * Crea un nuevo profesor
	 *
	 * @param array $data Datos de usuario ($data['login'])
	 * @return integer $profesor_id Profesor
	 */
	function crear($usuario)
	{
		if (! $usuario_id=$this->Usuario->buscar($usuario['Usuario']['login']))
		{
			$usuario_id=$this->Usuario->guardar($usuario['Usuario']);
			if (! $usuario_id)
			{
				$this->invalidate('_crear',__('inv_usuario_no_creado'));
				return null;
			}
		}
		$this->recursive=-1;
		if ($this->findById($usuario_id))
		{
			$this->invalidate('_crear',__('inv_profesor_existe'));
			return null;
		}
		$this->create();
		$data=array('id'=>$usuario_id);
		if ($this->save($data))
		{
			return $usuario_id;
		}
		return null;
	}


	/**
	 * Modifica los datos de profesor / usuario
	 *
	 * @param integer $profesor_id Profesor
	 * @param array $usuario Datos del usuario ($usuario['login'])
	 *
	 * @return boolean Éxito
	 *
	 */
	function modificar($profesor_id,$usuario)
	{
		$this->recursive=0;
		$this->contain('Usuario','Grupo');
		$data=$this->findById($profesor_id);
		if (!$data)
		{
			$this->invalidate('_modificar',__('inv_profesor_no_existe'));
			return false;
		}

		$usuario=am($data['Usuario'],$usuario);
		if (!$this->Usuario->save($usuario))
		{
			$this->invalidate('_modificar',__('inv_usuario_no_guardado'));
			return false;
		}
		return true;
	}

	function beforeDelete()
	{
		$this->contain('Asignatura','Carpeta');
		//Revisar aixo
		$this->unbindModel(array('hasAndBelongsToMany'=>array('CarpetaNoSubscrita')),false);
		return true;
	}

	function afterDelete()
	{
		$this->bindModel(array('hasAndBelongsToMany'=>array('CarpetaNoSubscrita')));
		$usuario_id=$this->id;
		if (! $this->Usuario->roles($usuario_id))
		{	//el usuario ya no tiene roles, eliminar
			$this->Usuario->delete($usuario_id);
		}
	}


}
?>
