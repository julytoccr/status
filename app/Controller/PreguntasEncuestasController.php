<?php
/**
 * Preguntas Encuestas Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class PreguntasEncuestasController extends AppController
{
    var $name = 'PreguntasEncuestas';
    var $uses=array('PreguntasEncuesta','Encuesta');
     var $access = array
	(
	'*' => array('profesor'),
	);
	
    public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_preguntas_encuestas'));
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}

	function index($encuesta_id){
		$this->_set_vars($encuesta_id);
	}

	function _set_vars($encuesta_id)
	{
		$encuesta=$this->Encuesta->findById($encuesta_id);
		if (!$encuesta) $this->_mensaje(__('error_encuesta'),'/encuestas',true);
		
		$this->set('encuesta',$encuesta['Encuesta']);
		$this->set('encuesta_id',$encuesta_id);
		$data=$this->PreguntasEncuesta->preguntas_encuesta($encuesta_id);
		$this->set('preguntas',$data);
	}


	function add($encuesta_id=null){
		//uses('sanitize');
		if (empty($this->request->data)){
			//$encuesta_id=Sanitize::paranoid($encuesta_id);
			$this->set('encuesta_id',$encuesta_id);
		}
		else{
			//$data = $this->_clean_data();
			$data = $this->request->data;
			$encuesta_id=$data['PreguntasEncuesta']['encuesta_id'];
			if ($this->PreguntasEncuesta->save($data)){
				$this->_mensaje(__('mens_pregunta_creada'),'/preguntas_encuestas/index/' . $encuesta_id);
			}
		}
	}

	function sort($encuesta_id){
		if (empty($this->request->data)){
			$this->_set_vars($encuesta_id);
		}
		else{
			$orden = array();
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->PreguntasEncuesta->ordenar($encuesta_id,$orden)){
				$this->_mensaje(__('mens_ordenado'),'/preguntas_encuestas/index/' . $encuesta_id);
			}
			else{
				$this->_mensaje(__('error_ordenando'),'/preguntas_encuestas/index/' . $encuesta_id,true);
			}
		}
	}

	function delete($pregunta_id){
		$this->PreguntasEncuesta->recursive=-1;
		$data=$this->PreguntasEncuesta->findById($pregunta_id);
		$encuesta_id=$data['PreguntasEncuesta']['encuesta_id'];
		$this->PreguntasEncuesta->delete($pregunta_id);
		$this->_mensaje(__('mens_pregunta_eliminada'),'/preguntas_encuestas/index/' . $encuesta_id);
	}

	function edit($pregunta_id=null){
		if (empty($this->request->data)){
			//uses('sanitize');
			//$pregunta_id=Sanitize::paranoid($pregunta_id);
			$this->PreguntasEncuesta->recursive=-1;
			$data=$this->PreguntasEncuesta->findById($pregunta_id);
			if (!$data) $this->_mensaje(__('error_preguntas'),'/encuestas',true);
			$this->request->data=$data;
		}
		else{
			//$data = $this->_clean_data();
			$data = $this->request->data;
			$pregunta_id=$data['PreguntasEncuesta']['id'];
			$this->PreguntasEncuesta->recursive=-1;
			$opcion=$this->PreguntasEncuesta->findById($pregunta_id);
			if (!$opcion) $this->_mensaje(__('error_preguntas'),'/encuestas/',true);
			$encuesta_id=$opcion['PreguntasEncuesta']['encuesta_id'];
			if ($this->PreguntasEncuesta->save($data)){
				$this->_mensaje(__('mens_pregunta_modificada'),'/preguntas_encuestas/index/' . $encuesta_id);
			}
		}
	}
}
?>
