<?php
App::import('Vendor','crypt/simple_crypt');

class PuntuacionProblema extends AppModel
{
	var $name = 'PuntuacionProblema';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Problema',
			'Alumno'
	);

	/**
	 * Devuelve la puntuación otorgada por un alumno a un problema si existe
	 * @param integer $alumno_id Alumno
	 * @param integer $problema_id Problema
	 * @return array Puntuación:
	 *      $result['id'] Identificador
	 *      $result['puntuacion'] Valor de 1-5
	 */
	function buscar($alumno_id,$problema_id)
	{
		$this->recursive=-1;

		$ident=$this->_encriptar_identificador($alumno_id ,$problema_id);

		$data=$this->find('first',array('condiitons'=>array('PuntuacionProblema.alumno_id_cifrado'=>$ident,
									'PuntuacionProblema.problema_id'=>$problema_id)));
		if (!$data) return null;

		return $data['PuntuacionProblema'];
	}

	/**
	 * Devuelve el identificador encriptado de alumno con problema
	 *
	 * @param integer $alumno_id Alumno
	 * @param integer $problema_id Problema
	 * @return string Identificador encriptado
	 */
	function _encriptar_identificador($alumno_id, $problema_id)
	{
		$SimpleCrypt = new SimpleCrypt('Simple');
		$string=$alumno_id;

		/*
		 * añado al principio un numero determinado de ceros,
		 * máximo 9 (según el primer digito del alumno_id) para que la longitud
		 * de la cadena encriptada varíe, sin modificar el valor de $alumno_id (numérico)
		 */
		for($i=0;$i<substr($alumno_id,0,1);$i++) $string='0' . $string;

		$key='PuntuacionProblema_' . $problema_id;

		return urlencode($SimpleCrypt->encrypt($key,$string));
	}

	/**
	 * Devuelve el identificador de alumno descifrado
	 * @param string $alumno_id_cifrado
	 * @param integer $problema_id Problema
	 * @return integer $alumno_id
	 *
	 */
	function _desencriptar_identificador($alumno_id_cifrado,$problema_id)
	{
		$SimpleCrypt = new SimpleCrypt('Simple');
		$string=urldecode($alumno_id_cifrado);
		$key='PuntuacionProblema_' . $problema_id;
		return floor($SimpleCrypt->decrypt($key,$string));
	}

	/**
	 * Guarda la puntuación otorgada por un alumno a un problema.
	 * No pueden haber dos puntuaciones con el mismo alumno y problema.
	 * Se reemplaza la anterior.
	 *
	 * @param integer $alumno_id Alumno
	 * @param integer $problema_id Problema
	 * @param integer $puntuacion Puntuación del 1 al 5
	 *
	 * @return boolean Éxito
	 */
	function guardar($alumno_id,$problema_id,$puntuacion)
	{
		$asignatura_id=$this->Alumno->asignatura($alumno_id);

		$puntuacion=floor($puntuacion);
		if ($puntuacion<1 || $puntuacion>5) return false;

		$data=$this->buscar($alumno_id,$problema_id);
		if ($data===null)
		{
			$ident=$this->_encriptar_identificador($alumno_id ,$problema_id);

			$data=array(	'alumno_id_cifrado'=>$ident,
							'asignatura_id'=>$asignatura_id,
							'problema_id'=>$problema_id);
		}
		$data['puntuacion']=$puntuacion;

		return $this->save($data);
	}

	/**
	 * Devuelve la puntuación media de un problema
	 *
	 * @param integer $problema_id Problema
	 * @return float Puntuación o null si no tiene asignada
	 */
	function media($problema_id)
	{
		$data=$this->find('all',array('conditions'=>array('PuntuacionProblema.problema_id'=>$problema_id),'fields'=>array('avg(puntuacion) as media')));
		if (!$data) return null;

		return $data['PuntuacionProblema']['media'];
	}

	/**
	 * Devuelve un listado con todas las puntuaciones de los alumnos
	 * hacia un problema
	 *
	 * @param integer $problema_id Problema
	 * @param integer $asignatura_id Asignatura
	 *
	 * @return array $result[$i]['puntuacion']
	 * @return array $result[$i]['alumno_id']
	 */
	function puntuaciones_problema($problema_id,$asignatura_id)
	{
		$this->recursive=-1;
		$data=$this->find('all',array('conditions'=>array('PuntuacionProblema.asignatura_id'=>$asignatura_id,'PuntuacionProblema.problema_id'=>$problema_id)));
		$result=array();
		foreach ($data as $row)
		{
			$alumno_id_cifrado=$row['PuntuacionProblema']['alumno_id_cifrado'];
			$alumno_id=$this->_desencriptar_identificador($alumno_id_cifrado,$problema_id);
			$result[]=am($row['PuntuacionProblema'], array('alumno_id'=>$alumno_id));
		}
		return $result;
	}

}
?>
