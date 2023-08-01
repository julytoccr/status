<?php
App::import('Vendor', 'crypt/simple_crypt');
class VotacionesEncuesta extends AppModel
{
	var $name = 'VotacionesEncuesta';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'PreguntasEncuesta',
			'Alumno'
	);

	/**
	 * Busca una votación según la PreguntasEncuesta y el Alumno
	 * @param integer $encuestas_asignatura_id EncuestaAsignatura
	 * @param integer $preguntas_encuesta Pregunta
	 * @param integer $alumno_id Alumno
	 * @return array
	 * 	Ejemplo: $result['VotacionesEncuesta']['id']
	 * 			o bien null
	 */
	function buscar($encuestas_asignatura_id,$preguntas_encuesta_id,$alumno_id)
	{
		$this->recursive=-1;
		$ident=$this->_encriptar_identificador($alumno_id,$encuestas_asignatura_id);
		return $this->find('first',array('conditions'=>
			array(	'VotacionesEncuesta.preguntas_encuesta_id'=>$preguntas_encuesta_id,
					'VotacionesEncuesta.encuestas_asignatura_id'=>$encuestas_asignatura_id,
					'VotacionesEncuesta.alumno_id_cifrado'=>$ident)));
	}

	/**
	 * Busca las votaciones de un alumno para una cierta EncuestaAsignatura
	 * @param integer $encuestas_asignatura_id EncuestaAsignatura
	 * @param integer $alumno_id Alumno
	 * @return array
	 * 	Ejemplo: $result['VotacionesEncuesta']['id']
	 * 			o bien null
	 */
	function votaciones_encuesta_asignatura($encuestas_asignatura_id,$alumno_id)
	{
		$this->recursive=-1;
		$ident=$this->_encriptar_identificador($alumno_id,$encuestas_asignatura_id);
		return $this->find('all',array('conditions'=>
			array(	'VotacionesEncuesta.encuestas_asignatura_id'=>$encuestas_asignatura_id,
					'VotacionesEncuesta.alumno_id_cifrado'=>$ident)));
	}


	/**
	 * Devuelve el identificador encriptado de alumno con votacion
	 *
	 * @param integer $alumno_id Alumno
	 * @param integer $encuestas_asignatura_id EncuestasAsignatura
	 * @return string Identificador encriptado
	 */
	function _encriptar_identificador($alumno_id, $encuestas_asignatura_id)
	{
		$SimpleCrypt = new SimpleCrypt('Simple');

		$string=$alumno_id;

		/*
		 * añado al principio un numero determinado de ceros,
		 * máximo 9 (según el primer digito del alumno_id) para que la longitud
		 * de la cadena encriptada varíe, sin modificar el valor de $alumno_id (numérico)
		 */
		for($i=0;$i<substr($alumno_id,0,1);$i++) $string='0' . $string;

		$key='VotacionesEncuestas_' . $encuestas_asignatura_id;

		return urlencode($SimpleCrypt->encrypt($key,$string));
	}

	/**
	 * Devuelve el identificador de alumno descifrado
	 * @param string $alumno_id_cifrado
	 * @param integer $encuestas_asignatura_id EncuestasAsignatura
	 * @return integer $alumno_id
	 *
	 */
	function _desencriptar_identificador($alumno_id_cifrado,$encuestas_asignatura_id)
	{
		$SimpleCrypt = new SimpleCrypt('Simple');

		$string=urldecode($alumno_id_cifrado);
		$key='VotacionesEncuestas_' . $encuestas_asignatura_id;
		return floor($SimpleCrypt->decrypt($key,$string));
	}

	/**
	 * Devuelve las estadísticas del alumno asociado a una votación
	 * @param string $votaciones_encuesta_id Votación
	 * @return array Estadísticas del alumno asociado
	 *
	 * @see Alumno::estadisticas()
	 */
	function estadisticas_alumno_votacion($votaciones_encuesta_id)
	{

		$this->recursive=-1;
		$data=$this->findById($votaciones_encuesta_id);
		if (!$data) return null;

		$alumno_id_cifrado=$data['VotacionesEncuesta']['alumno_id_cifrado'];
		$encuestas_asignatura_id=$data['VotacionesEncuesta']['encuestas_asignatura_id'];

		$alumno_id=$this->_desencriptar_identificador($alumno_id_cifrado,$encuestas_asignatura_id);

		$estadisticas=$this->Alumno->estadisticas($alumno_id);
		foreach(array(	'alumno_id',
							'asignatura_id',
							'grupo_nombre',
							'grupo_id') as $remove_field)
		{
			unset($estadisticas[$remove_field]);
		}
		return $estadisticas;
	}


	/**
	 * Crea una votación
	 *
	 * @param integer $encuestas_asignatura_id EncuestasAsignatura
	 * @param integer $preguntas_encuesta_id Pregunta
	 * @param integer $alumno_id Alumno
	 * @param integer $opciones_encuesta_id OpcionesEncuesta
	 * @return boolean Éxito
	 */
	function crear($encuestas_asignatura_id,$preguntas_encuesta_id,$alumno_id,$opciones_encuesta_id)
	{
		$this->create();
		$ident=$this->_encriptar_identificador($alumno_id,$encuestas_asignatura_id );
		$data=array(	'preguntas_encuesta_id'=>$preguntas_encuesta_id,
						'alumno_id_cifrado'=>$ident,
						'encuestas_asignatura_id'=>$encuestas_asignatura_id,
						'opciones_encuesta_id'=>$opciones_encuesta_id);
		return $this->save($data);
	}
}
?>
