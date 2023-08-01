<?php
/**
 * Pregunta Model
 * @package models
 */
App::uses('PreguntaForm', 'Form');
App::import('Vendor', 'estatus/rserve/estatus_rserve');

class Pregunta extends AppModel
{
	var $name = 'Pregunta';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Problema',
			'SintaxisRespuesta',
	);
	
	function __construct(){
		parent::__construct();
		$this->validate = PreguntaForm::validation();
	}
	
	function setValidation($action){
		$this->validate = PreguntaForm::$action();
	}

	/**
	 * Ordena las preguntas de un problema según
	 * el orden indicado en el array
	 *
	 * @param integer $problema_id Problema
	 * @param array $orden Orden de las preguntas. El array contiene los identificadores de las preguntas en orden
	 * @return boolean Éxito
	 */
	function ordenar($problema_id,$orden)
	{
		$i=1;
		$error=false;
		foreach ($orden as $pregunta_id)
		{
			$this->recursive=-1;
			$data=$this->find('first', array('conditions'=>array('id'=>$pregunta_id, 'problema_id'=>$problema_id)));
			if ($data)
			{
				$data['Pregunta']['orden']=$i;
				$ret=$this->save($data);
				if (!$ret) $error=true;
				$i++;
			}
			else $error=true;
		}
		return ! $error;
	}

	/**
	 * Retorna un array con las preguntas del problema
	 *
	 * @param integer $problema_id Problema
	 * @return array
	 * 	Ejemplo: $result[$i]['Pregunta']['id']
	 */
	function preguntas_problema($problema_id)
	{
		$this->recursive=-1;
		$conditions = array('Pregunta.problema_id'=>$problema_id);
		$order = 'Pregunta.orden';
		return $this->find('all', compact('conditions', 'order'));
	}
	
	/**
	 * Copia las preguntas de un problema a otro
	 *
	 * @param integer $problema_id Problema origen
	 * @param integer $nuevoproblema_id Problema destino
	 * @return boolean exito
	 * 
	 */
	function copiar($problema_id, $nuevoproblema_id)
	{
		$this->recursive=-1;
		$data=$this->find('all',array('conditions'=>array('Pregunta.problema_id'=>$problema_id)));
		$res_save = true;
		foreach($data as $row)
		{
			unset($row['Pregunta']['id']);
			$this->create();
			$row['Pregunta']['problema_id'] = $nuevoproblema_id;
			if(!$this->save($row)){
				$res_save = false;
				break;
			}
		}
		return $res_save;		
	}

	/**
	 * Comprueba que el enunciado usa variables existentes en las expresiones del problema
	 */
	function enunciadoValidator($data,$field_name, $validator_params)
	{
		if (isset($data[$this->name]['problema_id']))
		{
			$problema_id=$data[$this->name]['problema_id'];
		}
		else
		{
			$problema_id=$this->problema($data[$this->name]['id']);
		}
		$enunciado=$data[$this->name]['enunciado'];

		$valido=true;
		$lista_vars=$this->Problema->variables($problema_id);
		$lista_vars[]='param_sintaxis_';
		$lista_vars[]='param_expresiones_';

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
	 * Retorna el identificador de problema asociado a una pregunta
	 */
	function problema($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
		return $data['Pregunta']['problema_id'];
	}

	/**
	 * @param integer $id Identificador de pregunta
	 * @param array $respuestas Array de respuestas del usuario [0..num_preguntas_totales - 1]
	 * @param array $resultados_anteriores Array de resultados de preguntas anteriores [0..$num_pregunta - 1]
	 * @param integer $num_pregunta Número de pregunta
	 * @param Object $rserve Sesión rserve
	 *
	 * @return array
	 * 	$result['resultado'] -> Valor entre 0 y 1 que indica el resultado
	 *  $result['mensaje'] -> Mensaje informativo para el usuario.
	 *
	 */
	function correccion_respuesta($preguntas, $respuestas, $resultados, &$rserve, $reintentos) {
		
		for ($i = 0; $i < count($respuestas); $i++) {
			if ($respuestas[$i] == '') $respuestas[$i] = "''";
		}
		$respuestas_list = 'list(' . join(',', $respuestas) . ')';

		$mensajes_preguntas = array();
		$variables_mensajes_preguntas = array();
		
		$rserve -> cache = true;
		$rserve -> recuperar_variable('resultados_', 'c()');
		$rserve -> recuperar_variable('mensaje_acum_', 'list()');
		$rserve -> recuperar_variable('respuestas_', $respuestas_list, false);

		foreach ($preguntas as $i => $preg) {
			if (!isset($resultados[$i])) {
				$id = $preg['id'];
				
				$this -> recursive = -1;
				$data = $this -> findById($id);
				
				$rserve -> recuperar_variable('variables_mensaje_' . $i . "_", 'list()');				
				if ($data) {
					$mensajes_preguntas[$i]['mensaje_correcto']     = $data['Pregunta']['mensaje_correcto'];
					$mensajes_preguntas[$i]['mensaje_incorrecto']   = $data['Pregunta']['mensaje_incorrecto'];
					$mensajes_preguntas[$i]['mensaje_semicorrecto'] = $data['Pregunta']['mensaje_semicorrecto'];
					
					$variables_mensajes_preguntas[$i] = $this -> _encontrar_variables($mensajes_preguntas[$i]['mensaje_correcto']);
					$variables_mensajes_preguntas[$i] = $this -> _encontrar_variables($mensajes_preguntas[$i]['mensaje_incorrecto'], $variables_mensajes_preguntas[$i]);
					$variables_mensajes_preguntas[$i] = $this -> _encontrar_variables($mensajes_preguntas[$i]['mensaje_semicorrecto'], $variables_mensajes_preguntas[$i]);
									
					$parametro   = $data['Pregunta']['expresiones_parametro'];
					$expresiones = $data['Pregunta']['expresiones_respuesta'];
					$solucion    = $data['Pregunta']['respuesta'];
					if (! $solucion) {
						$solucion = "''";
					}
					
					$rserve -> recuperar_variable('solucion_', $solucion, false);
					$rserve -> recuperar_variable('respuesta_', $respuestas[$i], false);
					
					$rserve -> recuperar_variable('parametro_', $parametro);
					$rserve -> recuperar_variable('num_pregunta_', $i + 1);
					$rserve -> recuperar_variable('mensaje_', '');
					$rserve -> recuperar_variable('resultado_', '');

					if ($reintentos != null)
						$rserve -> recuperar_variable('chances_', ($reintentos[$i] > 0) ? ($reintentos[$i] - 1) : 0);

					$res = $rserve -> evaluar($expresiones);
										
					$counter = 0;
					foreach ($variables_mensajes_preguntas[$i] as $nombre => $valor) {
						$destino = "variables_mensaje_" . $i . '_' . $counter . "_";
						
						// Si la variable existe, guardamos su valor. Si no existe, guardamos NULL
						$rserve -> evaluar("if (exists(\"" . $nombre . "\")) {\r\n" .
                                                                   "    " . $destino . " <- " . $nombre . "\r\n" .
                                                                   "} else {\r\n" .
                                                                   "    " . $destino . " <- NULL\r\n" .
                                                                   "}\r\n");
						$counter++;
					}
					$rserve -> recuperar_variable('mensaje_acum_[' . ($i + 1) . ']', 'mensaje_');
					$rserve -> recuperar_variable('resultados_[' . ($i + 1) . ']', 'as.numeric(resultado_)'); //usamos as.numeric por si lo ponen entre comillas
				} else {
					$mensajes_preguntas[$i]['mensaje_correcto']     = '';
					$mensajes_preguntas[$i]['mensaje_incorrecto']   = '';
					$mensajes_preguntas[$i]['mensaje_semicorrecto'] = '';
					$rserve -> recuperar_variable('resultados_[' . ($i + 1) . ']', '0');
				}
			}
		}
		
		$res = $rserve -> evaluar_cache();

		$resultados_old = $resultados;
		$resultados     = $rserve -> obtener_HTML_variable2('resultados_');
		$resultados     = split(", ", $resultados);
		$mensajes       = $rserve -> obtener_valor_variable('mensaje_acum_');
		$mensajes_nota  = array();

		foreach ($preguntas as $i => $preg) {
			if (!isset($mensajes[$i]) || $mensajes[$i] || isset($resultados_old[$i])) {
				$mensajes_nota[$i] = '';
			}
			else {
				$mensajes[$i] = '';
				if ($resultados[$i] == 1) $mens     = $mensajes_preguntas[$i]['mensaje_correcto'];
				elseif ($resultados[$i] == 0) $mens = $mensajes_preguntas[$i]['mensaje_incorrecto'];
				else $mens                          = $mensajes_preguntas[$i]['mensaje_semicorrecto'];

				if (!empty($variables_mensajes_preguntas[$i])) {
					$count      = 0;
					foreach ($variables_mensajes_preguntas[$i] as $nombre => $valor) {
						$var_value = $rserve -> obtener_HTML_variable2('variables_mensaje_' . $i . '_' . $count . '_');
						$mens = str_replace('$' . $nombre . '$', $var_value, $mens);
						$count++;
					}
				}

				$mensajes_nota[$i] = $mens;
			}
		}
		return array ( 'resultados'    => $resultados,
			       'mensajes'      => $mensajes,
			       'mensajes_nota' => $mensajes_nota );
	}
	
	// devuelve un array con los nombres de las variables de R encontradas como i­ndices (y valores vacios)
	function _encontrar_variables(&$pajar, $res = array()) {
		$tmp = array();
		
		preg_match_all("/\\\$[[:alnum:]_\\[\\]]*\\\$/", $pajar, $tmp);
		foreach ($tmp[0] as $var) {
		  $res[substr($var, 1, strlen($var) - 2)] = "";
		}
		
		return $res;
	}

	/**
	 * Devuelve el mensaje correspondiente a una nota con las variables substituidas
	 * @param integer $id Pregunta
	 * @param float $nota Nota entre 0 y 1
	 * @param Object $rserve Sesión rserve
	 * @return string Mensaje del tipo mensaje_correcto/semicorrecto/incorrecto en función de la nota
	 */
	function mensaje($id,$nota,&$rserve)
	{
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;
	}


	/**
	 * Comprueba la sintaxis de una respuesta a una cierta pregunta.
	 * Devuelve la respuesta modificada, y un mensaje si se trata de una respuesta
	 * con sintaxis incorrecta.
	 *
	 * @param integer $id Identificador de pregunta
	 * @param string $respuesta Respuesta a comprobar
	 * @param Object $rserve Sesión rserve
	 *
	 * @return array
	 * 	$result['mensaje'] -> Mensaje informativo para el usuario. Si no es null, implica un 0 en la pregunta
	 *  $result['respuesta'] -> Valor modificado y parseado de la respuesta
	 *
	 */
	function comprobar_sintaxis($preguntas, $respuestas, &$rserve) {
		$rserve -> cache = true;
		$rserve -> recuperar_variable('mensaje_acum_', 'list()');
		$rserve -> recuperar_variable('respuesta_acum_', 'list()');
		$rserve -> recuperar_variable('parametro_acum_', 'list()');
		foreach ($preguntas as $i => $preg) {
			$id = $preg['id'];
			
			$this -> recursive = 0;
			$this -> contain('SintaxisRespuesta');
			$data = $this -> findById($id);
			if ($data) {
				$parametro = $data['Pregunta']['sintaxis_parametro'];
				$expresiones = $data['SintaxisRespuesta']['expresiones'];
				
				if (! isset($respuestas[$i])) {
					$respuestas[$i] = '';
				}

				$rserve -> recuperar_variable('parametro_', $parametro, true);
				$rserve -> recuperar_variable('respuesta_', $respuestas[$i], true);
				$rserve -> recuperar_variable('mensaje_', '');
				$res = $rserve -> evaluar($expresiones, false);
				$rserve -> recuperar_variable('mensaje_acum_[' . ($i + 1) . ']', 'mensaje_');
				$rserve -> recuperar_variable('respuesta_acum_[' . ($i + 1) . ']', 'toString(deparse(respuesta_, width=500))');
				$rserve -> recuperar_variable('parametro_acum_[' . ($i + 1) . ']', 'parametro_');
			}
		}
		$res = $rserve -> evaluar_cache();

		$mensajes   = $rserve -> obtener_valor_variable('mensaje_acum_');
		$respuestas = $rserve -> obtener_valor_variable('respuesta_acum_');
		$parametros = $rserve -> obtener_valor_variable('parametro_acum_');

		foreach ($preguntas as $i => $preg) {
			if ($mensajes[$i]) {
				if ($parametros[$i]) {
					$mensajes[$i] = __($mensajes[$i], array($parametros[$i]));
				}
				else {
					$mensajes[$i] = __($mensajes[$i]);
				}
			}
		}

		return array ( 'respuestas' => $respuestas,
			       'mensajes'   => $mensajes );
	}


	/**
	 * Comprueba si las expresiones son válidas
	 */
	function beforeValidate()
	{
		if (isset($this->request->data[$this->name]['expresiones_respuesta']))
		{
			$rserve = new EstatusRserve();

			if (isset($this->request->data[$this->name]['problema_id']))
			{
				$problema_id=$this->request->data[$this->name]['problema_id'];
			}
			else
			{
				$problema_id=$this->problema($this->request->data[$this->name]['id']);
			}
			$this->Problema->evaluar_problema($problema_id,$rserve);

			$rserve->cache=true;
			$rserve->recuperar_variable('solucion_',0);
			$rserve->recuperar_variable('respuesta_',array_fill(0,100,0));
			$rserve->recuperar_variable('respuestas_',array_fill(0,100,0));
			$rserve->recuperar_variable('resultados_',array_fill(0,100,0));
			$rserve->recuperar_variable('parametro_',0);
			$rserve->recuperar_variable('num_pregunta_',50);
			$rserve->recuperar_variable('mensaje_','');
			$rserve->recuperar_variable('resultado_','');
			$rserve->evaluar_cache();

			if (! $rserve->evaluar_instrucciones($this->request->data[$this->name]['expresiones_respuesta'],true))
			{
				$this->invalidate('expresiones_respuesta',__('inv_expresiones_no_validas',array(h($rserve->ultimo_error()))));
			}

		}
		return true;
	}

}
?>
