<?php
class ViewAlumnosUsuario extends AppModel
{
	var $name = 'ViewAlumnosUsuario';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Usuario',
			'Grupo',
			'Asignatura'
	);


	/**
	 * Devuelve un array con todos los alumnos asociados a un usuario
	 *
	 * @param integer $usuario_id Usuario
	 * @return array
	 * 	$result[$i]['Asignatura']['id'] Asignatura
	 *  $result[$i]['Alumno']['id'] Alumno
	 *  $result[$i]['Grupo']['id'] Grupo
	 *
	 */
	function alumnos_usuario($usuario_id)
	{
		$this->recursive=1;

		$data=$this->find('all',array('conditions'=>array('ViewAlumnosUsuario.usuario_id'=>$usuario_id)));
		return $data;
	}
	/**
	 * Devuelve un array con todos los alumnos matriculados a una asignatura
	 *
	 * @param integer $asignatura_id Asignatura
	 * @param mixed $orden Orden tipo findAll
	 *
	 * @return array
	 * 	$result[$i]['Asignatura']['id'] Asignatura
	 *  $result[$i]['Alumno']['id'] Alumno
	 *  $result[$i]['Grupo']['id'] Grupo
	 *
	 */

	function alumnos_asignatura($asignatura_id,$orden=array('Usuario.apellidos','Usuario.nombre'))
	{
		$this->recursive=1;
		$data=$this->find('all',array('conditions'=>array('ViewAlumnosUsuario.asignatura_id'=>$asignatura_id),'order'=>$orden));
		return $data;
	}
	
	function alumnos_asignatura_password_plano($asignatura_id, $orden=array('Usuario.apellidos', 'Usuario.nombre'))
	{
		$this->recursive=0;
		$data=$this->find('all',array('conditions'=>array('ViewAlumnosUsuario.asignatura_id'=>$asignatura_id,'ViewAlumnosUsuario.tipo_auth'=>'plain'),'order'=>$orden));
		return $data;
	}

}
?>
