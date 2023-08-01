<?php
/**
 * Problema Model
 * @package models
 */

App::import('Vendor', 'estatus/rserve/estatus_rserve');

//TODO: Esto no se donde ponerlo
define('REGEXP_VARIABLES','/\$([A-Za-z0-9._^\$]+)\$/');


class Problema extends AppModel
{
	var $name = 'Problema';
	var $useTable = 'problemas';
	var $displayField='nombre';
	public $actsAs = array('Containable');

	var $belongsTo = array(
			'Dificultad',
			'Profesor',
	);

	var $hasOne = array(
		'ViewProblemaEliminable',
		'ViewProblemaNoEliminable',
		'ViewEstadisticasProblema'
	);

	var $hasMany = array(
			'Agrupacion',
			'Pregunta',
			'CarpetasProblemas',
			'ViewResultadosPreguntasProblema',
	);
	var $hasAndBelongsToMany = array('Carpeta');

	/**
	 * Devuelve cierto si el problema está publicado
	 *
	 * @param integer $id Problema
	 * @return boolean Cierto si está publicado
	 */
	function es_publicado($id=null)
	{
		if ($id===null) $id=$this->id;
		$data=$this->findById($id);
		if ($data)
		{
			return ! ($data['Problema']['published']===null);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Publica / despublica un problema.
	 *
	 * @param integer $profesor_id Profesor
	 * @param integer $problema_id Problema
	 * @return boolean Éxito
	 */
	function publicar($problema_id,$profesor_id)
	{
		$this->recursive=-1;
		$data=$this->findById($problema_id);
		if (!$data) return false; //no se encuentra problema
		if ($data['Problema']['profesor_id']!==$profesor_id) return false; //profesor no propietario
		if ($data['Problema']['published']===null){
			//no está publicado. publicar
			$data['Problema']['published']=date('Y-m-d H:i:s');
		}
		else{
			//está publicado. despublicar
			$data['Problema']['published']=null;
		}
		if ($this->save($data)){
			//aqui autoasignar a asignaturas especiales
			$Asignatura = ClassRegistry::init('Asignatura');
			$Asignatura->autoasignar($problema_id);
			return true;
		}
		return false;
	}

	/**
	 * Devuelve todas las asignaciones a carpetas por un problema
	 * @param integer $id Problema
	 * @return array Asignaciones
	 *    $result[$i]['problema_id']
	 *    $result[$i]['carpeta_id']
	 */
	function carpetas($id)
	{
		$this->recursive=-1;
		$data=$this->CarpetasProblemas->findAll(array('CarpetasProblemas.problema_id'=>$id));
		if (!$data) return array();
		$asign=array();
		foreach ($data as $row) $asign[]=$data['CarpetasProblemas'];
		return $asign;
	}



	/**
	 * Crea una copia exacta del problema en una carpeta
	 *
	 * @param integer $id Problema a copiar
	 * @param integer $carpeta_id Carpeta destino
	 * @return boolean Éxito
	 */
	function copiar($id=null,$carpeta_id)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
		unset($data['Problema']['id']);
		$carpeta = $this->Carpeta->buscar($carpeta_id);
		$data['Problema']['profesor_id'] = $carpeta['Carpeta']['profesor_id'];
		$this->create();
		if ($this->save($data)){
			$new_id=$this->getLastInsertID();
			if($this->CarpetasProblemas->asignar($new_id,$carpeta_id,0)){
				return $this->Pregunta->copiar($id, $new_id);
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

	/**
	 * Crea un enlace (alias) al problema en una carpeta
	 *
	 * @param integer $id Problema a copiar
	 * @param integer $carpeta_id Carpeta destino
	 * @return boolean Éxito
	 */
	function copiar_alias($id=null,$carpeta_id)
	{
		if ($id===null) $id=$this->id;
		return $this->CarpetasProblemas->asignar($id,$carpeta_id,true);
	}

	/**
	 * Mueve un problema a otra carpeta
	 *
	 * @param integer $id Problema a clonar
	 * @param integer $carpeta_origen_id Carpeta origen donde está asignado actualmente el problema
	 * @param integer $carpeta_destino_id Carpeta destino del problema
	 * @return boolean Éxito
	 */
	function mover($id=null,$carpeta_origen_id,$carpeta_destino_id)
	{
		if ($id===null) $id=$this->id;
		$es_alias=$this->CarpetasProblemas->es_alias($id,$carpeta_origen_id);
		if ($this->CarpetasProblemas->desasignar($id,$carpeta_origen_id)){
			return $this->CarpetasProblemas->asignar($id,$carpeta_destino_id,$es_alias);
		}
	}

	/**
	 * Comprueba si las expresiones son válidas
	 */
	function beforeValidate()
	{
		if (isset($this->data[$this->name]['expresiones']))
		{
			$rserve = new EstatusRserve();

			$this->_set_datos_extra($rserve, 0, FALSE, FALSE, FALSE);
			$ret=$rserve->evaluar_instrucciones($this->data[$this->name]['expresiones'],false);
			if (! $ret)
				$this->invalidate('expresiones',__('inv_expresiones_no_validas',array(h($rserve->ultimo_error()))));
		}
		return true;
	}

	/**
	 * Retorna un array con las variables definidas en el problema
	 *
	 * @param integer $id Problema
	 * @return array Array de nombres de variables definidas en las expresiones. Ejemplo: array('x','y','z')
	 */
	function variables($id=null)
	{
		if ($id===null) $id=$this->id;
		$rserve = new EstatusRserve();
		$this->evaluar_problema($id,$rserve);
		$lista=$rserve->obtener_lista_variables();
		return $lista;
	}

	function _set_datos_extra($rserve, $usuario_id, $es_alumno, $es_profe, $es_admin)
	{
		$rserve -> cache = true;
		$rserve -> recuperar_variable('usuario_id_', $usuario_id, true);
		$rserve -> recuperar_variable('es_alumno_', ($es_alumno) ? "T" : "F");
		$rserve -> recuperar_variable('es_profesor_', ($es_profe) ? "T" : "F");
		$rserve -> recuperar_variable('es_admin_', ($es_admin) ? "T" : "F");
		$rserve -> evaluar_cache();
	}

	/**
	 * Evalúa un problema en una cierta sesión rserve
	 * @param integer $id Problema
	 * @param Object $rserve Sesión de Rserve
	 * @return boolean Éxito
	 */
	function evaluar_problema($id , $rserve, $usuario_id = 0, $es_alumno = FALSE, $es_profe = FALSE, $es_admin = FALSE)
	{
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return false;

		$expresiones=$data['Problema']['expresiones'];

		$this -> _set_datos_extra($rserve, $usuario_id, $es_alumno, $es_profe, $es_admin);
		return $rserve->evaluar_instrucciones($expresiones);
	}


	/**
	 * Comprueba que el enunciado usa variables existentes en las expresiones del problema
	 */
	function enunciadoValidator($data,$field_name, $validator_params)
	{
		$enunciado=$data[$this->name]['enunciado'];

		$rserve = new EstatusRserve();

		$this -> _set_datos_extra($rserve, 0, FALSE, FALSE, FALSE);

		$rserve->evaluar_instrucciones($data[$this->name]['expresiones']);
		$lista_vars=$rserve->obtener_lista_variables();

		$valido=true;
		preg_match_all(REGEXP_VARIABLES,$enunciado,$matches);
		foreach ($matches[1] as $match)
		{
			if (! in_array($match,$lista_vars))
			{
				$valido=false;
			}
		}
		return $valido;
	}

	/**
	 * Retorna un array con las preguntas del problema
	 * @param integer $id Problema
	 * @return array Preguntas del problema
	 * 	 Ejemplo: $result[$i]['enunciado']
	 */
	function preguntas($id=null)
	{
		$data=$this->Pregunta->preguntas_problema($id);
		$preguntas=array();
		foreach ($data as $row) $preguntas[]=$row['Pregunta'];
		return $preguntas;
	}


	/**
	 * Devuelve el enunciado tanto del problema como de sus preguntas
	 * con todas las variables substituidas.
	 *
	 * @param integer $id Problema
	 * @param Object $rserve Sesión Rserve con las variables instanciadas
	 * @return array
	 * 	$result['Problema']['enunciado']
	 *  $result['Pregunta'][$i]['enunciado']
	 */
	function substituir_variables($id,$rserve)
	{
		$result=array();

		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;

		$problema=$data['Problema'];
		$preguntas=$this->preguntas($id);

		$datos=$rserve->obtener_lista_valores_tipos_variables();
		
		//buscamos qué variables se han instanciado, para no obtener el valor HTML de todas (seria lento)
		$total_matches=array();

		preg_match_all(REGEXP_VARIABLES,$problema['enunciado'],$matches);
		$total_matches=am($total_matches,$matches[1]);
		for ($i=0;$i<count($preguntas);$i++)
		{
			preg_match_all(REGEXP_VARIABLES,$preguntas[$i]['enunciado'],$matches);
			$total_matches=am($total_matches,$matches[1]);

			preg_match_all(REGEXP_VARIABLES,$preguntas[$i]['ayuda'],$matches);
			$total_matches=am($total_matches,$matches[1]);
		}


		foreach ($datos as $nombre_dato=>$info)
		{
			if (in_array($nombre_dato, $total_matches))
			{
				$token='$' . $nombre_dato . '$';

				if ($info['tipo']=='numeric' || $info['tipo']=='integer')
				{
					$valor_dato=$info['valor'];
				}
				else
				{
					$valor_dato=$rserve->obtener_HTML_variable2($nombre_dato);
				}
				$problema['enunciado']=str_replace($token, $valor_dato,$problema['enunciado']);
				for ($i=0;$i<count($preguntas);$i++)
				{
					$preguntas[$i]['enunciado']=str_replace($token, $valor_dato,$preguntas[$i]['enunciado']);
					$preguntas[$i]['ayuda']=str_replace($token, $valor_dato,$preguntas[$i]['ayuda']);
				}
			}
		}

		//substituir constantes de los enunciados de las preguntas
		for ($i=0;$i<count($preguntas);$i++)
		{
			$datos=array(
				'$param_expresiones_$'=>$preguntas[$i]['expresiones_parametro'],
				'$param_sintaxis_$'=>$preguntas[$i]['sintaxis_parametro'],
				);
			foreach ($datos as $token=>$valor_dato)
			{
				$preguntas[$i]['enunciado']=str_replace($token, $valor_dato,$preguntas[$i]['enunciado']);
				$preguntas[$i]['ayuda']=str_replace($token, $valor_dato,$preguntas[$i]['ayuda']);
			}
		}

		return array(
			'Problema'=>$problema,
			'Pregunta'=>$preguntas,
		);
	}

	/**
	 * Realiza la corrección de un problema
	 *
	 * @param integer $id Problema
	 * @param Object $rserve Sesión rserve con el problema evaluado
	 * @param array $respuestas Respuestas del alumno ($respuestas[num_pregunta]=valor )
	 *
	 * @return array Información de la corrección
	 * 	$result['resultados'] => Array de resultados
	 * 		$result['resultados'][$i]['resultado'] =>Resultado de la respuesta (entre 0 y 1)
	 * 		$result['resultados'][$i]['mensaje'] =>Mensaje informativo de la respuesta
	 * 		$result['resultados'][$i]['nota'] =>Nota ponderada de la respuesta
	 *  $result['nota'] => Nota del problema (suma de las notas ponderadas de las respuestas)
	 */
	function correccion($id, $rserve, $respuestas, $reintentos = null) {
		$preguntas = $this -> preguntas($id);

		//calcular el peso total de las preguntas
		$peso_total = 0;
		foreach ($preguntas as $preg) {
			$peso_total += $preg['peso'];
		}
		$resultados = array();
		$datos = $rserve -> obtener_lista_valores_variables();
		$suma_pesos = 0;


		//primera pasada: ejecutar SintaxisPregunta por cada respuesta
		$info_sintaxis = $this -> Pregunta -> comprobar_sintaxis($preguntas, $respuestas, $rserve);
		foreach ($preguntas as $i => $preg) {
			if ($info_sintaxis['mensajes'][$i]) {
				$resultados[$i] = array( 'resultado'      => 0,
							 'mensaje_nota'   => '',
							 'mensaje'        => $info_sintaxis['mensajes'][$i],
							 'error_sintaxis' => true );
			}
			$respuestas[$i] = $info_sintaxis['respuestas'][$i];
		}

		$info_correccion = $this -> Pregunta -> correccion_respuesta($preguntas, $respuestas, $resultados, $rserve, $reintentos);
		foreach ($preguntas as $i => $preg) {
			if (!isset($resultados[$i])) {
				$resultados[$i] = array( 'resultado'      => $info_correccion['resultados'][$i],
							 'mensaje'        => $info_correccion['mensajes'][$i],
							 'mensaje_nota'   => $info_correccion['mensajes_nota'][$i],
							 'error_sintaxis' => false );
			}
			$resultados[$i]['peso']  = $preg['peso'];

			if ($peso_total > 0) {
				$resultados[$i]['nota'] = round($resultados[$i]['resultado'] * 10 * $resultados[$i]['peso'] / $peso_total, 3);
			}
			else {
				//no dividir por cero
				$resultados[$i]['nota'] = 0;
			}
			$suma_pesos += $resultados[$i]['resultado'] * $resultados[$i]['peso'];
		}

		if ($peso_total > 0) {
			$nota = round($suma_pesos* 10 / $peso_total, 3);
		}
		else {
			$nota = 0;
		}

		return array( 'resultados' => $resultados,
			      'nota'       => $nota );
	}

	/**
	 * Devuelve una lista de los problemas que se pueden
	 * eliminar directamente al no tener ejecuciones
	 *
	 * @return array
	 *   $result[$i]['id']
	 */
	function problemas_eliminables()
	{
		$data=$this->ViewProblemaEliminable->find('all');
		$result=array();
		foreach ($data as $row) $result[] = array('Problema'=>$row['Problema']);
		return $result;
	}

	/**
	 * Devuelve una lista de los problemas que no se pueden
	 * eliminar directamente al tener ejecuciones asociadas en alguna asignatura
	 *
	 * @return array
	 *   $result[$i]['id']
	 */
	function problemas_no_eliminables()
	{
		$data=$this->ViewProblemaNoEliminable->find('all');
		$result=array();
		foreach ($data as $row) $result[] = array('Problema'=>$row['Problema']);
		return $result;
	}

	/**
	 * Elimina el problema indicado junto con sus preguntas.
	 * No elimina otras asociaciones
	 *
	 * @param integer $id
	 * @param boolean Falso si ocurrió algún error
	 */
	function eliminar($id){
		$ret = $this->Pregunta->deleteAll(array('Pregunta.problema_id' => $id));
		$ret=$this->delete($id) & $ret;
		return $ret;
	}

	/**
	 * Devuelve estadísticas generales del problema
	 * @param integer $problema_id Problema
	 * @param integer $asignatura_id Asignatura
	 * @return array
	 * 	$result['nota_media' / 'numero_ejecuciones' / 'numero_alumnos' ]
	 */
	function estadisticas($problema_id,$asignatura_id)
	{
		$this->ViewEstadisticasProblema->recursive=-1;
		$data=$this->ViewEstadisticasProblema->find('first',array('conditions'=>
			array(
				'ViewEstadisticasProblema.problema_id'=>$problema_id,
				'ViewEstadisticasProblema.asignatura_id'=>$asignatura_id)));
		if ($data) return $data['ViewEstadisticasProblema'];
		else return null;
	}
}
?>
