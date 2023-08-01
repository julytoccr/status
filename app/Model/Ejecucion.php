<?php
App::import('Vendor', 'estatus/rserve/estatus_rserve');
/**
 * Ejecución (hasMany Agrupacion) XOR (hasMany Problema)
 * Agrupacion si se trata de una ejecución de un alumno
 * Problema si se trata de una prueba de un profesor (en cuyo caso alumno es null)
 *
 * @package models
 */
class Ejecucion extends AppModel
{
	var $name = 'Ejecucion';
	var $useTable = 'ejecuciones';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Alumno',
			'Agrupacion',
			'Problema',

	);

	var $hasMany = array(
			'RespuestasEjecucion' => array(
				'joinTable'              => 'respuestas_ejecuciones',
                'foreignKey'             => 'ejecucion_id',
				'order'                  => 'numero_pregunta',
			),
	);

	var $hasOne = array(
			'EstEjecucion' => array('foreignKey' => 'ejecucion_id'),
			'ViewEjecucion' => array('foreignKey' => 'ejecucion_id'),
	);

	/**
	 * Crea una nueva ejecución de un problema a partir
	 * de una agrupación (ejecuciones de alumnos)
	 *
	 * @param integer $alumno_id Alumno
	 * @param integer $agrupacion_id Agrupacion
	 *
	 * @return integer Identificador de la ejecución
	 *
	 * TODO: Solo ejecutar los publicados
	 */
	function crear_ejecucion_agrupacion($alumno_id,$agrupacion_id)
	{
		$this->Agrupacion->recursive=-1;
		if (! $this->Agrupacion->findById($agrupacion_id))
		{
			return false;
		}
		$timestamp_limite=$this->Agrupacion->fecha_limite($agrupacion_id);
		//TODO: Agrupacion->fecha_limite() directamente date('') ?
		$fecha_limite=date('Y-m-d H:i:s',$timestamp_limite);

		$rserve = new EstatusRserve();

		$fecha=date('Y-m-d H:i:s');

		$data['Ejecucion']=array(
			'alumno_id'=>$alumno_id,
			'agrupacion_id'=>$agrupacion_id,
			'fecha_limite'=>$fecha_limite,
			'fecha_final'=>$fecha,
			'semilla'=>$rserve->establecer_semilla(),
			);

		$this->create();
		if ($this->save($data))
		{
			return $this->getInsertID();
		}
		return null;
	}

	/**
	 * Crea una nueva ejecución de un problema a partir
	 * de un problema (pruebas de los profesores)
	 *
	 * @param integer $problema_id Problema
	 * @param integer $minutos_limite Tiempo límite en minutos
	 *
	 * @return integer Identificador de la ejecución
	 */
	function crear_ejecucion_problema($problema_id,$minutos_limite,$semilla=null)
	{
		$timestamp_limite=time() + $minutos_limite * 60;

		$fecha_limite=date('Y-m-d H:i:s',$timestamp_limite);
		$fecha=date('Y-m-d H:i:s');

		$rserve= new EstatusRserve();

		$data['Ejecucion']=array(
			'problema_id'=>$problema_id,
			'fecha_limite'=>$fecha_limite,
			'fecha_final'=>$fecha,
			'semilla'=>$rserve->establecer_semilla($semilla),
		);

		$this->create();
		if ($this->save($data))
		{
			return $this->getInsertID();
		}
		return null;
	}

	/**
	 * Devuelve el identificador de problema asociado a una ejecución
	 * @param integer $id Ejecución
	 * @return integer $problema_id Identificador de problema
	 */
	function problema($id=null)
	{
		if ($id===null) $id = $this->id;
		$this->recursive=0;
		$this->contain('ViewEjecucion');
		$data = $this->findById($id);
		if (!$data) return null;
		if ($data['ViewEjecucion']['alumno_problema_id'])
		{
			//ejecución de alumnos
			return $data['ViewEjecucion']['alumno_problema_id'];
		}
		else
		{
			//ejecución de profesores
			return $data['ViewEjecucion']['profesor_problema_id'];
		}
	}

	/**
	 * Retorna un array con los datos de la ejecución
	 * @param integer $id Ejecución
	 * @return array Datos de la ejecución.
	 * 	 Ejemplo: $result['varX']=>4.74
	 */
	function datos($id=null)
	{
		$this->recursive=-1;
		$data=$this->findById($id);
		if (! $data) return null;
		$semilla=$data['Ejecucion']['semilla'];

		$problema_id=$this->problema($id);

		$rserve=& new EstatusRserve();
		$rserve->establecer_semilla($semilla);
		$this->Problema->evaluar_problema($problema_id,$rserve);
		return $rserve->obtener_lista_valores_variables();
	}

	/**
	 * Devuelve cierto si la ejecución ha expirado
	 *
	 * @param integer $id Ejecución
	 * @return boolean Cierto si ha expirado
	 */
	function expirado($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;

		return(time()>strtotime($data['Ejecucion']['fecha_limite']));
	}

	/**
	 * Devuelve cierto si la ejecución indicada
	 * es la ejecución en curso para el alumno y
	 * problema-bloque
	 * @param integer $id Ejecución
	 * @return boolean Es la ejecución en curso.
	 */
	function es_ejecucion_en_curso($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);

		if (!$data) return false;
		if ($data['Ejecucion']['alumno_id']===null)
		{
			//las ejecuciones de profesores son pruebas
			return true;
		}
		return ($id === $this->en_curso(
				$data['Ejecucion']['alumno_id'],
				$data['Ejecucion']['agrupacion_id']));
	}

	/**
	 * Devuelve la ejecución en curso para un alumno y
	 * un problema-bloque (agrupación)
	 *
	 * @param integer $alumno_id Alumno
	 * @param integer $agrupacion_id Agrupación
	 * @return integer $ejecucion_id Si existe, de lo contrario null,
	 */
	function en_curso($alumno_id,$agrupacion_id)
	{
		$this->recursive=-1;
		/*
		 * La última siempre será la que tenga mayor ID
		 * para ese alumno y agrupacion. Falta comprobar
		 * que tenga nota NULL y no esté expirado.
		 */
		$data=$this->find('first',array('conditions'=>array('alumno_id'=>$alumno_id, 'agrupacion_id'=>$agrupacion_id),'order'=>array('id'=>'desc')));
		if ($data && !$this->expirado($data['Ejecucion']['id']) && $data['Ejecucion']['nota']==null)
		{
			return $data['Ejecucion']['id'];
		}
		return null;
	}

	/**
	 * Devuelve la nota de una ejecución
	 */
	function devolver_nota($ejecucion_id, $problema_id, $usuario_id, $es_alumno, $es_profe, $es_admin)
	{
		$respuestas_guardadas = $this -> RespuestasEjecucion -> buscar($ejecucion_id);

		if ($problema_id === NULL) $problema_id = $this -> problema($ejecucion_id);

		// Para calcular una cota superior, usar lo de debajo comentado
/*		$preguntas = $this -> Problema -> preguntas($problema_id);

		$suma  = 0;
		$total = 0;

		foreach ($preguntas as $index => $preg) {
			if ($respuestas_guardadas && !empty($respuestas_guardadas[$index])) {
				if ($respuestas_guardadas[$index]['intento'] == 0 &&
                                    $respuestas_guardadas[$index]['valor'] == '') $suma += $preg['peso'];
				else $suma += $respuestas_guardadas[$index]['resultado']*$preg['peso'];
			}
			$total += $preg['peso'];
		}

		return $suma*10/$total;*/
		$data       = $this -> ejecutar($ejecucion_id, $usuario_id, $es_alumno, $es_profe, $es_admin);
		$rserve     = $data['Rserve'];
		$preguntas  = $data['Pregunta'];
		$reintentos = array();
		foreach ($preguntas as $i => $preg) {
			if (IsSet($respuestas_guardadas[$i])) {
				$respuestas_guardadas[$i] = $respuestas_guardadas[$i]['valor'];
			}
			else {
				$respuestas_guardadas[$i] = '';
			}
			$reintentos[$i] = 0;
		}

		$info = $this -> Problema -> correccion($problema_id, $rserve, $respuestas_guardadas, $reintentos);
		return $info['nota'];
	}

	/**
	 * Guarda la nota de una ejecución
	 *
	 * @param integer $id Ejecución
	 * @param real $nota Nota a guardar
	 * @return boolean Éxito
	 */
	function guardar_nota($id, $nota, $actualizar_final = TRUE)
	{
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return false;
		$data['Ejecucion']['nota']=$nota;

		if ($actualizar_final) {
			$fecha=date('Y-m-d H:i:s');
			$data['Ejecucion']['fecha_final']=$fecha;
		}

		return $this->save($data);
	}

	/**
	 * Devuelve la puntuación otorgada a una ejecución
	 *
	 * @param integer $id Ejecución
	 * @return float Puntuación
	 */
	function puntuacion($id)
	{
		$data=$this->EstEjecucion->find('first',array('conditions'=>array('EstEjecucion.ejecucion_id'=>$id)));
		if (!$data) return null;

		return $data['EstEjecucion']['puntuacion'];
	}

	/**
	 * Guarda las respuestas de una ejecución. Si el vector respuestas
	 * no contiene todas las respuestas a las preguntas, únicamente se guardarán
	 * éstas.
	 *
	 * @param integer $id Ejecución
	 * @param array $respuestas. Array asociativo #pregunta=>respuesta (la primera pregunta es la 0)
	 * @param array $resultados. Array asociativo #pregunta=>info_resultado Si un resultado no existe, no se guardará info relacionada
	 * 			info_resultado['resultado'] nota entre 0 y 1.
	 * 			info_resultado['error_sintaxis'] si es cierto, nunca incrementará los intentos sobre esta pregunta
	 * @param boolean $incrementar. Incrementar los intentos realizados sobre las preguntas (nunca si info_resultado['error_sintaxis']==true)
	 * @return boolean Éxito
	 */
	function guardar_respuestas($id, $respuestas, $resultados, $incrementar) {
		$error = false;
		$respuestas_ejecucion = $this -> RespuestasEjecucion -> buscar($id);
		foreach ($respuestas as $numero => $valor) {
			$incrementar_preg = $incrementar;
			if (IsSet($resultados[$numero]) && $resultados[$numero]['error_sintaxis']) {
				$incrementar_preg = false;
			}
			$res = null;
			if (IsSet($resultados[$numero]))
				$res = $resultados[$numero]['resultado'];
			$return = NULL;
			if (isset($respuestas_ejecucion[$numero]))
				$return = $this -> RespuestasEjecucion -> guardar($id, $numero, $valor, $incrementar_preg, $res, $respuestas_ejecucion[$numero]);
			else
				$return = $this -> RespuestasEjecucion -> guardar($id, $numero, $valor, $incrementar_preg, $res);
			
			$error = $error || !$return;
		}

		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return false;

		$fecha=date('Y-m-d H:i:s');
		$data['Ejecucion']['fecha_final']=$fecha;
		$this->save($data);

		return !$error;
	}

	/**
	 * Obtiene las respuestas de la ejecución indicada
	 * @param integer $id Ejecución
	 * @return array Respuestas
	 *   $result[$i]['valor'] Valor de la respuesta
	 * 	 $result[$i]['numero_pregunta'] Número de pregunta (la primera es la 0)
	 *   $result[$i]['intento'] Intentos consumidos
	 */
	function respuestas($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=1;
		$this->contain('RespuestasEjecucion');
		$data=$this->findById($id);
		$data=$data['RespuestasEjecucion'];
		foreach ($data as $i=>$respuesta)
		{
			$data[$i]['intentos_disponibles']=$this->RespuestasEjecucion->intentos_disponibles($respuesta['id']);
		}
		return $data;
	}

	/**
	 * Devuelve cierto si todos los intentos de todas
	 * las preguntas se han consumido. De ser así, la
	 * ejecución se entiende "finalizada"
	 *
	 * @param integer $id Ejecución
	 * @return boolean Cierto si todos los intentos de todas las preguntas
	 * se han consumido
	 */
	function estan_intentos_consumidos($id=null)
	{
		$intentos=$this->intentos_disponibles($id);
		foreach ($intentos as $intento)
		{
			if ($intento>0) return false;
		}
		return true;
	}

	/**
	 * Ejecuta un problema y devuelve el enunciado
	 * tanto del problema como de sus preguntas, con
	 * todas las variables substituidas.
	 *
	 * @param integer $id Ejecución
	 * @return array
	 * 	$result['Problema']['enunciado']
	 *  $result['Pregunta'][$i]['enunciado']
	 *  $result['Dato']['nombreDato']=>valorDato
	 *  $result['Rserve'] (sesión de rserve)
	 *  $result['Ejecucion']
	 */
	function ejecutar($id=null, $usuario_id, $es_alumno, $es_profe, $es_admin)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
		$semilla = $data['Ejecucion']['semilla'];

		$rserve = new EstatusRserve();
		$rserve->establecer_semilla($semilla);
		$problema_id=$this->problema($id);

		if (! $this->Problema->evaluar_problema($problema_id, $rserve, $usuario_id, $es_alumno, $es_profe, $es_admin))
		{
			return null;
		}

		$result=$this->Problema->substituir_variables($problema_id,$rserve);

		// SintaxisRespuesta se necesita en la vista, para mostrar las casillas con diferente anchura
		$result['SintaxisRespuesta'] = $this -> Problema -> Pregunta -> SintaxisRespuesta -> obtener_resumen();
        $result['Dato'] = $rserve->obtener_lista_valores_variables();
		$result['Ejecucion'] = $data['Ejecucion'];
		$result['Rserve'] = $rserve;
		return $result;
	}



	/**
	 * Devuelve el número de intentos disponibles en las respuestas
	 * de la ejecución
	 *
	 * @param integer $id Ejecución
	 * @return array Array de intentos disponibles.
	 * 	$result[2]=#intentos de la tercera pregunta (la primera es la 0)
	 *
	 * TODO: Optimizar
	 */
	function intentos_disponibles($id = null) {
		$problema_id = $this -> problema($id);
		$preguntas   = $this -> Problema -> preguntas($problema_id);
		$intentos    = array();
		$respuestas  = $this -> RespuestasEjecucion -> buscar($id);
		foreach ($preguntas as $numero_pregunta => $preg) {				
			$respuesta = NULL;
			if (isset($respuestas[$numero_pregunta])) $respuesta = $respuestas[$numero_pregunta];

			if ($respuesta) {
				$intentos[$numero_pregunta] = $this -> RespuestasEjecucion -> intentos_disponibles($respuesta['id']);
			}
			else {
				$intentos[$numero_pregunta] = $preg['intentos'];
			}
		}
		return $intentos;
	}

	/**
	 * Devuelve todas las ejecuciones de un problema por alumnos de una cierta asignatura
	 * @param integer $asignatura_id
	 * @param integer $problema_id
	 * @param array $order Orden de las ejecuciones. Tipo findAll
	 * @return array Ejecuciones
	 * 		$result[$i]['Ejecucion']['id']
	 * 		$result[$i]['Problema']['nombre']
	 */
	function ejecuciones_problema($asignatura_id,$problema_id,$order=null)
	{
		$cond=array('EstEjecucion.asignatura_id'=>$asignatura_id,
					'EstEjecucion.problema_id'=>$problema_id);
		$this->EstEjecucion->recursive=0;
		return $this->EstEjecucion->find('all',array('conditions'=>$cond,'order'=>$order));
	}
}
?>
