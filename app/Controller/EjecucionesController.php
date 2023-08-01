<?php
App::uses('AppController', 'Controller');
App::uses('Sanitize', 'Utility');
App::import('Vendor', 'estatus/rserve/graficos');
App::import('Vendor', 'estatus/rserve/minitab');

class EjecucionesController extends AppController {
	var $name = 'Ejecuciones';
    var $uses=array('Alumno','Asignatura','Bloque','Problema','Agrupacion','Ejecucion','PuntuacionProblema','ViewEjecucionesDatosExtra');
	
	var $helpers = array('Ajax','StarRating');
	
	/*var $access = array
	(
		'alumno' => 'iniciar_agrupacion, alumno_mostrar_resultado',
		'profesor' => 'iniciar_problema, profesor_mostrar_resultado',
	);*/
	var $access = array(
		'*' => array('alumno','profesor'),
		'iniciar_agrupacion'=>array('alumno'),
		'iniciar_problema'=>array('profesor'),
		'profesor_mostrar_resultado'=>array('profesor'),
		'alumno_mostrar_resultado'=>array('alumno'),
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}

	function _comprobar_asignatura($agrupacion_id)
	{
		$asignatura_id=$this->Agrupacion->asignatura($agrupacion_id);
		$asignatura_actual=$this->_alumno_asignatura_actual();
		return ($asignatura_id === $asignatura_actual);
	}
	
	function grafico($ejecucion_id,$nombre)
    {
    	$this->Ejecucion->recursive=-1;
		$data=$this->Ejecucion->findById($ejecucion_id);
		if (
				($data['Ejecucion']['alumno_id']
				&&	$data['Ejecucion']['alumno_id'] != $this->_alumno_actual()
				)
				|| $this->Ejecucion->expirado($ejecucion_id))
		{
			die(__('error_no_acceso'));
		}

		$data=$this->_set_datos_ejecucion($ejecucion_id,
						  $this -> _usuario_actual(),
						  $this -> _es_alumno(),
						  $this -> _es_profesor(),
						  $this -> _es_administrador());
		$rserve=$data['rserve'];
		if (!$data || ! $rserve->obtener_tipo_variable($data['datos'][$nombre])=='est.imagen')
		{
			die('No se encuentra el gráfico');
		}
    	$graficos= new GraficosRserve($rserve);
    	$graficos->mostrar($nombre);
    }
	
	function iniciar_agrupacion($agrupacion_id){
		//uses('sanitize');
		//Sanitize::paranoid($agrupacion_id);
	
		if (!$this->_comprobar_asignatura($agrupacion_id)){
			$this->_mensaje(__('error_no_acceso'),'/',true);
		}
		$alumno_id=$this->_alumno_actual();
		$ejecucion_id=$this->Ejecucion->en_curso($alumno_id,$agrupacion_id);
		if ($ejecucion_id===null)
		{
			$ejecucion_id=$this->Ejecucion->crear_ejecucion_agrupacion($alumno_id,$agrupacion_id);
		}
		//$this->setAction('mostrar',$ejecucion_id);
		$this->redirect('/ejecuciones/mostrar/' . $ejecucion_id);
	}
	
	function mostrar($ejecucion_id, $reintentando=false){
		if ($this->Ejecucion->expirado($ejecucion_id)){
			$this->_mensaje(__('mens_tiempo_ejecucion'),'/');
		}
		if (! $this->Ejecucion->es_ejecucion_en_curso($ejecucion_id)){
			$this->_mensaje(__('error_general'),'/',true);
		}
		if (empty($this->request->data)){
			//recuperamos respuestas guardadas en tiempo real si es que hay
			$respuestas=$this->Ejecucion->respuestas($ejecucion_id);
			foreach ($respuestas as $resp){
				$this->request->data['Ejecucion']['respuesta' . $resp['numero_pregunta']]=$resp['valor'];
			}
		}
		$reintentos=$this->Ejecucion->intentos_disponibles($ejecucion_id);
		$this->set('reintentos',$reintentos);
		$this->set('data',$this->data);
		$info = $this->_set_datos_ejecucion($ejecucion_id,
					    $this -> _usuario_actual(),
					    $this -> _es_alumno(),
					    $this -> _es_profesor(),
					    $this -> _es_administrador());
		$this->set('title_for_layout',__('ejecucion_problema',$info['problema']['nombre']));
	}

	function iniciar_problema($problema_id,$minutos_limite,$semilla=null){
		Sanitize::paranoid($problema_id);
		Sanitize::paranoid($minutos_limite);
		if (!is_numeric($minutos_limite)) $minutos_limite=0;

		$ejecucion_id=$this->Ejecucion->crear_ejecucion_problema($problema_id,$minutos_limite,$semilla);

		if ($this->Ejecucion->expirado($ejecucion_id)){
			$this->_mensaje(__('mens_tiempo_ejecucion'),'/');
		}

		if (! $this->Ejecucion->es_ejecucion_en_curso($ejecucion_id)){
			$this->_mensaje(__('error_general'),'/',true);
		}
		if (empty($this->request->data)){
			//recuperamos respuestas guardadas en tiempo real si es que hay
			$respuestas=$this->Ejecucion->respuestas($ejecucion_id);
			foreach ($respuestas as $resp){
				$this->request->data['Ejecucion']['respuesta' . $resp['numero_pregunta']]=$resp['valor'];
			}
		}
		$reintentos=$this->Ejecucion->intentos_disponibles($ejecucion_id);
		$this->set('reintentos',$reintentos);
		$this->set('data', $this->request->data);
		$this->_set_datos_ejecucion($ejecucion_id,
					    $this -> _usuario_actual(),
					    $this -> _es_alumno(),
					    $this -> _es_profesor(),
					    $this -> _es_administrador());
	}

	/**
	 * Indica el tiempo límite restante de un problema
	 *
	 */
	function tiempo_limite($ejecucion_id)
	{
		if ($this->Ejecucion->expirado($ejecucion_id))
		{
			$expirado=true;
		}
		else
		{
			if (! $this->Ejecucion->es_ejecucion_en_curso($ejecucion_id))
			{
				$expirado=true;
			}
			else
			{
				$expirado=false;
			}
		}

		if (!$expirado)
		{
			$this->Ejecucion->recursive=-1;
			$ejecucion=$this->Ejecucion->findById($ejecucion_id);
			$this->set('fecha_limite',$ejecucion['Ejecucion']['fecha_limite']);
		}
		$this->set('expirado',$expirado);
	}
	
	function _set_datos_ejecucion($ejecucion_id, $usuario_id = 0, $es_alumno = FALSE, $es_profe = FALSE, $es_admin = FALSE)
	{

		$data=$this->Ejecucion->ejecutar($ejecucion_id, $usuario_id, $es_alumno, $es_profe, $es_admin);
		$this->set('problema',$data['Problema']);
		$this->set('preguntas',$data['Pregunta']);
		$this->set('sintaxis',$data['SintaxisRespuesta']);
		$this->set('ejecucion',$data['Ejecucion']);
		$this->set('datos',$data['Dato']);
		$this->set('ejecucion_id',$ejecucion_id);
		return array(
			'ejecucion'=>$data['Ejecucion'],
			'problema'=>$data['Problema'],
			'preguntas'=>$data['Pregunta'],
			'rserve'=>$data['Rserve'],
			'datos'=>$data['Dato']
		);
	}
	
	function correccion() {
		if (empty($this -> request->data)) {
			$this -> _mensaje(__('error_no_acceso'), '/', true);
		}
		$ejecucion_id = Sanitize::paranoid($this->request->data['Ejecucion']['id']);
		//$this ->request-> data = Sanitize::paranoid($this->request->data, $this -> _sanitize_numeric);
		if ($this -> Ejecucion -> expirado($ejecucion_id)) {
			$this -> _mensaje(__('mens_tiempo_ejecucion'), '/');
		}
 		if (! $this -> Ejecucion -> es_ejecucion_en_curso($ejecucion_id)) {
			$this -> _mensaje(__('error_general'), '/', true);
		}
		$info      = $this -> _set_datos_ejecucion($ejecucion_id,
							   $this -> _usuario_actual(),
							   $this -> _es_alumno(),
							   $this -> _es_profesor(),
							   $this -> _es_administrador());
		$this->set('title_for_layout',__('correccion_problema',$info['problema']['nombre']));
		$preguntas = $info['preguntas'];
		$rserve    = $info['rserve'];
		$ejecucion = $info['ejecucion'];
		if ($ejecucion['alumno_id'] && $ejecucion['alumno_id'] != $this -> _alumno_actual()) {
			$this -> _mensaje(__('error_permiso_denegado'), '/', true);
		}
		$reintentos = $this -> Ejecucion -> intentos_disponibles($ejecucion_id);
		$respuestas = array();
		$respuestas_guardadas = NULL;
		foreach ($preguntas as $i => $preg) {
			if ($reintentos[$i] > 0) {
				if (isset($this->request->data['Ejecucion']['respuesta' . $i])
				&& $this -> data['Ejecucion']['respuesta' . $i] !== '') {
					$respuestas[$i] = $this->request->data['Ejecucion']['respuesta' . $i];
				}
				else {
					//ha respuesto en blanco
					$respuestas[$i] = '';
				}
			}
			else {
				//no podi­a responder porque no le quedan reintentos
				if (!$respuestas_guardadas) {
				    $respuestas_guardadas = $this -> Ejecucion -> RespuestasEjecucion -> buscar($ejecucion_id);
				}
				$resp = $respuestas_guardadas[$i];
				if ($resp != NULL) {
					$respuestas[$i] = $resp['valor'];
				}
				else {
					$respuestas[$i] = '';
				}
			}
		}
		$problema_id = $this -> Ejecucion -> problema($ejecucion_id);
		$rserve->recuperar_variable('chances_', $chances);
		$info = $this->Problema->correccion($problema_id, $rserve, $respuestas, $reintentos);
		$nota = $info['nota'];
		$resultados = $info['resultados'];

		$soluciones = array();
		//guardar las respuestas junto a sus resultados
		$this -> Ejecucion -> guardar_respuestas($ejecucion_id, $respuestas, $resultados, true);
		$reintentos = $this -> Ejecucion -> intentos_disponibles($ejecucion_id);
		foreach ($resultados as $i => $res) {
			$resultado = $res['resultado'];

			if ($resultado == 1 && $reintentos[$i] > 0) {
				//es totalmente correcta, consumir reintentos
				if (isset($respuestas_guardadas[$i])) $resp = $respuestas_guardadas[$i];
				else $resp = $this -> Ejecucion -> RespuestasEjecucion -> buscar($ejecucion_id, $i);
				$this -> Ejecucion -> RespuestasEjecucion -> finalizar($resp['id']);
				$reintentos[$i] = 0;
			}
			elseif ($resultado < 1 && $reintentos[$i] == 0 && $preguntas[$i]['mostrar_solucion']) {
				// Se ha quedado sin reintentos, no es totalmente correcta y se ha de mostrar la solucion
				$soluciones[$i] = $rserve -> obtener_valor_variable($preguntas[$i]['respuesta']);
			}
		}
		$estan_intentos_consumidos = true;
		foreach ($reintentos as $value) {
			if ($value > 0) {
				$estan_intentos_consumidos = false;
				break;
			}
		}

		if ($estan_intentos_consumidos) {
			//se guarda finalmente la nota
			$this -> Ejecucion -> guardar_nota($ejecucion_id, $nota);
			$puntuacion = $this -> Ejecucion -> puntuacion($ejecucion_id);
			$this -> set('reintentar', false);
			$this -> set('puntuacion', $puntuacion);

			if ($ejecucion['alumno_id']) {
				$alumno_id = $ejecucion['alumno_id'];
				$punt      = $this -> PuntuacionProblema -> buscar($alumno_id, $problema_id);

				if ($punt) $estrellas = $punt['puntuacion'];
				else $estrellas = 0;

				$this -> set('estrellas', $estrellas);
			}
		}
		else {
			//~ $this -> _sidebar('tablas_estadisticas/sidebar');
			$this -> set('reintentar', true);
			$this -> set('reintentos', $reintentos);

		}

		$this -> set('nota', $nota);
		$this -> set('resultados', $resultados);
		$this -> set('respuestas', $respuestas);
		$this -> set('soluciones', $soluciones);
	}
	
	function alumno_mostrar_resultado($ejecucion_id)
	{
		if ($this -> Ejecucion -> es_ejecucion_en_curso($ejecucion_id)) {
			$this -> _mensaje(__('error_general'), '/', true);
		}

		$alumno_id = $this -> _alumno_actual();

		$this -> set('data', $this->request->data);
		$info = $this -> _set_datos_ejecucion($ejecucion_id,
						      $this -> _usuario_actual(),
						      $this -> _es_alumno(),
						      $this -> _es_profesor(),
						      $this -> _es_administrador());
		$this->set('title_for_layout',__('correccion_problema',array($info['problema']['nombre'])));
		$ejecucion = $info['ejecucion'];

		if ($ejecucion['alumno_id'] != $alumno_id) {
			$this -> _mensaje(__('error_permiso_denegado'), '/', true);
		}

		$preguntas = $info['preguntas'];
		$rserve    = &$info['rserve'];

		$respuestas_guardadas = $this -> Ejecucion -> RespuestasEjecucion -> buscar($ejecucion_id);
		$reintentos           = array();
		foreach ($preguntas as $i => $preg) {
			if (isset($respuestas_guardadas[$i])) {
				$respuestas_guardadas[$i] = $respuestas_guardadas[$i]['valor'];
			}
			else {
				$respuestas_guardadas[$i] = '';
			}
			$reintentos[$i] = 0;
		}

		$problema_id = $this -> Ejecucion -> problema($ejecucion_id);
		$info        = $this -> Problema -> correccion($problema_id, $rserve, $respuestas_guardadas, $reintentos);
		$nota        = $info['nota'];
		$resultados  = $info['resultados'];

		$soluciones  = array();
		foreach ($resultados as $i => $res) {
			if ($preguntas[$i]['respuesta']) {
				$soluciones[$i] = $rserve -> obtener_valor_variable($preguntas[$i]['respuesta']);
			}
		}

		$this -> set('resultados', $resultados);
		$this -> set('respuestas', $respuestas_guardadas);
		$this -> set('soluciones', $soluciones);
		$this -> set('nota', $nota);
		$this -> set('semilla', $ejecucion['semilla']);
	}
	
	function profesor_mostrar_resultado($ejecucion_id)
	{
		if ($this -> Ejecucion -> es_ejecucion_en_curso($ejecucion_id)) {
			$this -> _mensaje(__('error_general'), '/', true);
		}

		$r = $this -> Ejecucion -> respuestas($ejecucion_id);
		$respuestas           = array();
		$resultados_guardados = array();
		$reintentos           = array();
		foreach ($r as $resp) {
			$respuestas[$resp['numero_pregunta']]           = $resp['valor'];
			$resultados_guardados[$resp['numero_pregunta']] = $resp['resultado'];
			$reintentos[$resp['numero_pregunta']]           = 0;
		}

		$this -> set('data', $this->request->data);

    	$this -> ViewEjecucionesDatosExtra -> recursive = -1;
		$extra_data = $this -> ViewEjecucionesDatosExtra -> findByEjecucionId($ejecucion_id);

		if (empty($extra_data)) {
			$this -> _mensaje(__('error_general'), '/', true);
		}
		$extra_data = $extra_data['ViewEjecucionesDatosExtra'];

		$info = $this -> _set_datos_ejecucion($ejecucion_id,
						      $extra_data['usuario_id'],
						      TRUE,
						      ($extra_data['es_profe'] == 1),
						      ($extra_data['es_admin'] == 1));
						      
		$this->set('title_for_layout',__('correccion_problema',array($info['problema']['nombre'])));

		$ejecucion = $info['ejecucion'];
		$preguntas = $info['preguntas'];
		$rserve    = & $info['rserve'];

		$problema_id = $this -> Ejecucion -> problema($ejecucion_id);
		$info        = $this -> Problema -> correccion($problema_id, $rserve, $respuestas, $reintentos);
		$nota        = $info['nota'];
		$resultados  = $info['resultados'];

		$soluciones=array();
		//guardar las respuestas junto a sus resultados
		foreach ($resultados as $i=>$res) {
			if ($preguntas[$i]['respuesta']) {
				$soluciones[$i] = $rserve -> obtener_valor_variable($preguntas[$i]['respuesta']);
			}
		}
		$this -> set('resultados', $resultados);
		$this -> set('resultados_guardados', $resultados_guardados);
		$this -> set('respuestas', $respuestas);
		$this -> set('soluciones', $soluciones);
		$this -> set('nota', $nota);
		$this -> set('semilla', $ejecucion['semilla']);
	}
	
	function aceptar_nota($ejecucion_id) {
		//uses('sanitize');

		//$ejecucion_id = Sanitize::paranoid($ejecucion_id);

		// eliminado para poder 'recuperar' una ejecucion perdida
/*		if ($this -> Ejecucion -> expirado($ejecucion_id)) {
			$this -> _mensaje(__('mens_tiempo_ejecucion'), '/');
		}

 		if (! $this -> Ejecucion -> es_ejecucion_en_curso($ejecucion_id)) {
			$this -> _mensaje(__('error_general'), '/', true);
		} */

		$expirado = FALSE;
		$en_curso = TRUE;
		$ok = TRUE;
		if ($this -> Ejecucion -> expirado($ejecucion_id)) {
			$expirado = TRUE;
			$ok = FALSE;
		}
		if (! $this -> Ejecucion -> es_ejecucion_en_curso($ejecucion_id)) {
			$en_curso = FALSE;
			$ok = FALSE;
		}

		if (!$ok) {
			$ej = $this -> Ejecucion -> findById($ejecucion_id);
			if ($ej['Ejecucion']['nota'] !== NULL) {
				if ($expirado) {
					$this -> _mensaje(__('mens_tiempo_ejecucion'), '/');
				}
				if (!$en_curso) {
					$this -> _mensaje(__('error_general'), '/', true);
				}
			}
			else {
				$ok = TRUE;
            }
		}

		// Por si un profe intenta aceptar la nota de una ejecucion perdida de un alumno
    	$this -> ViewEjecucionesDatosExtra -> recursive = -1;
		$extra_data = $this -> ViewEjecucionesDatosExtra -> findByEjecucionId($ejecucion_id);

		if (empty($extra_data)) {
			$this -> _mensaje(__('error_general'), '/', true);
		}
		$extra_data = $extra_data['ViewEjecucionesDatosExtra'];

		$info = $this -> _set_datos_ejecucion($ejecucion_id,
						      $extra_data['usuario_id'],
						      TRUE,
						      ($extra_data['es_profe'] == 1),
						      ($extra_data['es_admin'] == 1));

		$preguntas = $info['preguntas'];
		$rserve    = & $info['rserve'];
		$ejecucion = $info['ejecucion'];

		if ($ejecucion['alumno_id']) {
            $ok = TRUE;
			if ($this->_es_alumno()) {
				if ($ejecucion['alumno_id'] != $this -> _alumno_actual()) {
					$ok = FALSE;
                }
            } else {
				$ok = FALSE;
            }

			if (!$ok) {
				if ($this->_es_profesor()) {
					$asignatura_id=$this->Alumno->asignatura($ejecucion['alumno_id']);

					if ($asignatura_id == $this->_profesor_asignatura_actual()){
						$ok = TRUE;
					}
				}
				if (!$ok) $this -> _mensaje(__('error_permiso_denegado'), '/', true);
			}
		}
		$respuestas = array();
		$respuestas_guardadas = $this -> Ejecucion -> RespuestasEjecucion -> buscar($ejecucion_id);

		foreach ($preguntas as $i => $preg) {
			$resp = NULL;
			if (isset($respuestas_guardadas[$i])) $resp = $respuestas_guardadas[$i];
			
			if ($resp != NULL) $respuestas[$i] = $resp['valor'];
			else $respuestas[$i] = '';
		}

		$problema_id = $this -> Ejecucion -> problema($ejecucion_id);
		$reintentos  = $this -> Ejecucion -> intentos_disponibles($ejecucion_id);
		$info        = $this -> Problema -> correccion($problema_id, $rserve, $respuestas, $reintentos);
		$nota        = $info['nota'];
		$resultados  = $info['resultados'];
		$soluciones  = array();

		//guardar las respuestas junto a sus resultados
		$this -> Ejecucion -> guardar_respuestas($ejecucion_id, $respuestas, $resultados, true);
		$reintentos = $this -> Ejecucion -> intentos_disponibles($ejecucion_id);

		foreach ($resultados as $i => $res) {
			$resultado = $res['resultado'];
			if ($resultado < 1 && $reintentos[$i] == 0 && $preguntas[$i]['mostrar_solucion']) {
				// Se ha quedado sin reintentos, no es totalmente correcta y se ha de mostrar la solucion
				$soluciones[$i] = $rserve -> obtener_valor_variable($preguntas[$i]['respuesta']);
			}
			$this -> Ejecucion -> RespuestasEjecucion -> finalizar($respuestas_guardadas[$i]['id']);
		}

		//se guarda finalmente la nota
		$this -> Ejecucion -> guardar_nota($ejecucion_id, $nota, ($en_curso == TRUE && $expirado == FALSE) );

		$puntuacion = $this -> Ejecucion -> puntuacion($ejecucion_id);
		$this -> set('reintentar', false);
		$this -> set('puntuacion', $puntuacion);
		if ($ejecucion['alumno_id']) {
			$alumno_id = $ejecucion['alumno_id'];
			$punt      = $this -> PuntuacionProblema -> buscar($alumno_id, $problema_id);
			if ($punt) $estrellas = $punt['puntuacion'];
			else $estrellas = 0;

			$this -> set('estrellas', $estrellas);
		}
		$this -> set('nota', $nota);
		$this -> set('resultados', $resultados);
		$this -> set('respuestas', $respuestas);
		$this -> set('soluciones', $soluciones);

		$this -> render('correccion');
	}
	
	function minitab($ejecucion_id){
    	$info=$this->_set_datos_ejecucion($ejecucion_id);
    	$rserve = $info['rserve'];
    	$variables=split(',',$info['problema']['variables_mostrar']);
    	$minitab = new MinitabRserve($rserve);

		if (($info['ejecucion']['alumno_id']) && $info['ejecucion']['alumno_id'] != $this->_alumno_actual()){
			die(__('error_no_acceso'));
		}
		if (empty($this->request->data)){
			$minitab->simbolo_decimal='.';
		}
		else{
			if ($this->request->data['Ejecucion']['simbolo_decimal']==1){
				$minitab->simbolo_decimal=',';
			}
			else{
				$minitab->simbolo_decimal='.';
			}
			if ($this->request->data['Ejecucion']['separador_columnas']==1){
				$minitab->separador_columnas='&nbsp;';
			}
			else if ($this->request->data['Ejecucion']['separador_columnas']==2){
				$minitab->separador_columnas=';';
			}
			else if ($this->data['Ejecucion']['separador_columnas']==3){
				$minitab->separador_columnas=':';
			}
			else if ($this->request->data['Ejecucion']['separador_columnas']==4)
			{
				$minitab->separador_columnas='$';
			}
			else if ($this->request->data['Ejecucion']['separador_columnas']==5)
			{
				$minitab->separador_columnas='|';
			}
		}

		$method="obtener_html_explorer_minitab";
		$html=$minitab->$method($variables);
		$this->set('tabla_html',$html);
		$this->set('ejecucion_id',$ejecucion_id);
		$this->set('separador',$minitab->separador_columnas);
    }
    
    function guardar_respuestas(){
		$this->autoRender=false;
		if (!isset($this->request->data['Ejecucion']['id'])) return;
		$data=$this->request->data['Ejecucion'];
		//uses('sanitize');
		//$data=Sanitize::paranoid($data,$this->_sanitize_numeric);
		$ejecucion_id=$data['id'];
		if (! $this->Ejecucion->es_ejecucion_en_curso($ejecucion_id)){
			return;
		}
		$respuestas=array();
		foreach ($data as $name=>$value){
			if (preg_match('/^respuesta(\d+)$/',$name,$matches)){
				$indice=$matches[1];
				if (is_numeric($value)){
					$respuestas[$indice]=$value;
				}
			}
		}
		if ($this->Ejecucion->guardar_respuestas($ejecucion_id,$respuestas,array(),false)){
			echo 'ok';
		}
		else{
			echo 'err';
		}
	}
	
	function puntuar($ejecucion_id,$puntuacion){
		$this->autoRender = false;
		if ($puntuacion<1 || $puntuacion>5){
			die('Acceso denegado 1' . $puntuacion);
		}
		$puntuacion=floor($puntuacion);
    	$this->Ejecucion->recursive=-1;
		$data=$this->Ejecucion->findById($ejecucion_id);
		$alumno_id=$this->_alumno_actual();
		if ($data['Ejecucion']['alumno_id'] != $alumno_id){
			die('Acceso denegado 2');
		}
		$problema_id=$this->Ejecucion->problema($ejecucion_id);
		if (!$problema_id) die('Acceso denegado 3');
		$this->PuntuacionProblema->guardar($alumno_id,$problema_id,$puntuacion);
		echo $puntuacion;
	}
}
