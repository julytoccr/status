<?php
/**
 * VotacionesEncuestasController
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class VotacionesEncuestasController extends AppController
{
    var $name = 'VotacionesEncuestas';
    
    var $access = array
	(
	'votar' => array('alumno'),
	'home_encuestas'=>array('alumno'),
	);

    var $uses=array('Alumno','EncuestasAsignatura','VotacionesEncuesta','Encuesta','PreguntasEncuesta','OpcionesEncuesta');

	function votar($encuestas_asignatura_id){
		$this->EncuestasAsignatura->contain('Encuesta');
		$data=$this->EncuestasAsignatura->findById($encuestas_asignatura_id);
		if (! $this->EncuestasAsignatura->es_activa($data['EncuestasAsignatura'])){
			$this->_mensaje(__('error_encuesta_noactiva'),'/',true);
		}
		if ($data['EncuestasAsignatura']['asignatura_id'] != $this->_alumno_asignatura_actual()){
			$this->_mensaje(__('error_asignatura_encuesta'),'/',true);
		}
		$alumno_id=$this->_alumno_actual();
		if ($this->VotacionesEncuesta->votaciones_encuesta_asignatura($encuestas_asignatura_id,$alumno_id)){
			$this->_mensaje(__('mens_votacion'),'/',true);
		}
		$encuesta_id=$data['EncuestasAsignatura']['encuesta_id'];
		if (empty($this->request->data)){
			$preguntas=$this->PreguntasEncuesta->preguntas_encuesta($encuesta_id);
			foreach ($preguntas as $i=>$preg){
				$opciones=$this->OpcionesEncuesta->opciones_encuesta($preg['PreguntasEncuesta']['id']);
				$preguntas[$i]['OpcionesEncuesta']=$opciones;
			}
			$this->set('encuesta',$data['Encuesta']);
			$this->set('encuestas_asignatura_id',$encuestas_asignatura_id);
			$this->set('preguntas',$preguntas);
		}
		else{
			//uses('sanitize');
			$error=false;
			foreach ($this->data['VotacionesEncuesta'] as $pregunta_id=>$opcion_id){
				if (preg_match('/^pregunta_(\d+)$/',$pregunta_id,$matches)){
					$pregunta_id=$matches[1];
					//$opcion_id=Sanitize::paranoid($opcion_id);
					if (!$this->VotacionesEncuesta->crear($encuestas_asignatura_id,$pregunta_id,$alumno_id,$opcion_id)){
						$error=true;
					}
				}
			}
			if ($error){
				$this->_mensaje(__('error_votacion'),'/',true);
			}
			else{
				$this->_mensaje(__('mens_gracias_votacion'),'/');
			}
			$this->set('data',$data);
		}
	}

	/**
	 * Esta función es llamada mediante requestAction para imprimir en la
	 * pantalla principal del alumno las encuestas disponibles para votar
	 */
	function home_encuestas(){
		$alumno_id=$this->_alumno_actual();
		$asignatura_id=$this->Alumno->asignatura($alumno_id);
		if (!$asignatura_id) die("Error encuestas del alumno");
		$tmp=$this->EncuestasAsignatura->encuestas_asignatura($asignatura_id,true);
		$encuestas=array();
		foreach ($tmp as $i=>$enc){
			if (! $this->VotacionesEncuesta->votaciones_encuesta_asignatura($tmp[$i]['EncuestasAsignatura']['id'],$alumno_id)){
				$encuestas[]=$enc;
			}
		}
		$this->set('data',$encuestas);
	}

}
?>
