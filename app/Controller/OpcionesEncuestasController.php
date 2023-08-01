<?php
/**
 * Opciones Encuestas Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class OpcionesEncuestasController extends AppController
{
    var $name = 'OpcionesEncuestas';
    var $uses=array('OpcionesEncuesta','PreguntasEncuesta','Encuesta');
	var $access = array
	(
	'*' => array('profesor'),
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_opciones_preguntas'));
	}
	
	function index($preguntas_encuesta_id){
		$this->_set_vars($preguntas_encuesta_id);
	}

	function _set_vars($preguntas_encuesta_id){
		$pregunta=$this->PreguntasEncuesta->findById($preguntas_encuesta_id);
		if (!$pregunta) $this->_mensaje(__('error_pregunta'),'/encuestas/',true);

		$encuesta=$this->Encuesta->findById($pregunta['PreguntasEncuesta']['encuesta_id']);
		if (!$encuesta) $this->_mensaje(__('error_encuesta'),'/encuestas/',true);

		$this->set('encuesta',$encuesta['Encuesta']);
		$this->set('encuesta_id',$encuesta['Encuesta']['id']);

		$this->set('pregunta',$pregunta['PreguntasEncuesta']);
		$this->set('pregunta_id',$preguntas_encuesta_id);

		$data=$this->OpcionesEncuesta->opciones_encuesta($preguntas_encuesta_id);

		$this->set('opciones',$data);
	}


	function add($preguntas_encuesta_id=null){
		//uses('sanitize');
		if (empty($this->request->data)){
			//$preguntas_encuesta_id=Sanitize::paranoid($preguntas_encuesta_id);
			$this->set('preguntas_encuesta_id',$preguntas_encuesta_id);
		}
		else{
			$this->_clean_data();
			$data = $this->request->data;
			$preguntas_encuesta_id=$data['OpcionesEncuesta']['preguntas_encuesta_id'];
			if ($this->OpcionesEncuesta->save($data)){
				$this->_mensaje(__('mens_opcion_creada'),'/opciones_encuestas/index/' . $preguntas_encuesta_id);
			}
			else
				$this->set('preguntas_encuesta_id',$this->request->data['OpcionesEncuesta']['preguntas_encuesta_id']);
		}
	}

	function sort($preguntas_encuesta_id){
		if (empty($this->request->data)){
			$this->_set_vars($preguntas_encuesta_id);
		}
		else{
			$orden = array();
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->OpcionesEncuesta->ordenar($preguntas_encuesta_id,$orden)){
				$this->_mensaje(__('mens_ordenado'),'/opciones_encuestas/index/' . $preguntas_encuesta_id);
			}
			else{
				$this->_mensaje(__('error_ordenando'),'/opciones_encuestas/index/' . $preguntas_encuesta_id,true);
			}

		}
	}

	function delete($opcion_id){
		$this->OpcionesEncuesta->recursive=-1;
		$data=$this->OpcionesEncuesta->findById($opcion_id);
		$preguntas_encuesta_id=$data['OpcionesEncuesta']['preguntas_encuesta_id'];
		$this->OpcionesEncuesta->delete($opcion_id);
		$this->_mensaje(__('mens_opcion_eliminada'),'/opciones_encuestas/index/' . $preguntas_encuesta_id);
	}

	function edit($opcion_id=null){
		if (empty($this->request->data)){
			//uses('sanitize');
			//$opcion_id=Sanitize::paranoid($opcion_id);
			$this->OpcionesEncuesta->recursive=-1;
			$data=$this->OpcionesEncuesta->findById($opcion_id);
			if (!$data) $this->_mensaje(__('error_opcion'),'/encuestas/',true);
			$this->request->data=$data;
		}
		else{
			$this->_clean_data();
			$data = $this->request->data;
			$this->OpcionesEncuesta->set($data);
			if($this->OpcionesEncuesta->validates()){
				$opcion_id=$data['OpcionesEncuesta']['id'];
				$this->OpcionesEncuesta->recursive=-1;
				$opcion=$this->OpcionesEncuesta->findById($opcion_id);
				if (!$opcion) $this->_mensaje(__('error_opcion'),'/encuestas/',true);
				$preguntas_encuesta_id=$opcion['OpcionesEncuesta']['preguntas_encuesta_id'];
				if ($this->OpcionesEncuesta->save($data)){
					$this->_mensaje(__('mens_opcion_modificada'),'/opciones_encuestas/index/' . $preguntas_encuesta_id);
				}
			}
		}
	}

}
?>
