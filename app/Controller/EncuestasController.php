<?php
/**
 * EncuestasController
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class EncuestasController extends AppController{
    var $name = 'Encuestas';
    var $uses=array('Encuesta', 'EncuestasAsignatura', 'EncuestasAsignatura', 'EstAlumno', 'PreguntasEncuesta', 'OpcionesEncuesta');
	var $access = array
	(
	'*' => array('profesor'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_encuestas'));
    }
                
	function index(){
		$this->Encuesta->recursive=-1;
		$this->paginate = array(
			'conditions' => array('Encuesta.profesor_id'=>$this->_profesor_actual()),
		);
		$this->set('encuestas',$this->paginate());
	}

	function resultados($encuestas_asignatura_id){
		$this->redirect('/encuestas_asignaturas/resultados/'. $encuestas_asignatura_id);
	}
	
	function todas()
	{
		$this->Encuesta->recursive=-1;
		$this->set('encuestas',$this->paginate());
	}

	function add(){
		if (!empty($this->request->data)){
			$this->_clean_data();
			$data  = $this->request->data;
			$data['Encuesta']['profesor_id'] = $this->_profesor_actual();
			if ($this->Encuesta->save($data)){
				$this->_mensaje(__('mens_tipo_creado'),'/encuestas');
			}
		}
	}

	function clonar_encuesta($id){
		$this->Encuesta->recursive=-1;
		$data=$this->Encuesta->findById($id);
		if($data != null){
			unset($data['Encuesta']['id']);
			$data['Encuesta']['profesor_id'] = $this->_profesor_actual();
			$this->Encuesta->create();
			if ($this->Encuesta->save($data)){
				$new_id = $this->Encuesta->getLastInsertId();
				$data = $this->PreguntasEncuesta->find('all',array('conditions'=>array('encuesta_id' => $id)));
				//Salvando preguntas
				foreach($data as $pregunta){
					$id_pregunta = $pregunta['PreguntasEncuesta']['id'];
					unset($pregunta['PreguntasEncuesta']['id']);
					$this->PreguntasEncuesta->create();
					$pregunta['PreguntasEncuesta']['encuesta_id'] = $new_id;
					if($this->PreguntasEncuesta->save($pregunta)){
						$new_pregunta_id = $this->PreguntasEncuesta->getLastInsertId();
						$this->OpcionesEncuesta->recursive=-1;
						$opciones = $this->OpcionesEncuesta->find('all',array('conditions'=>array('preguntas_encuesta_id' => $id_pregunta)));
						//salvando opciones preguntas
						foreach($opciones as $opcion){
							unset($opcion['OpcionesEncuesta']['id']);
							$this->OpcionesEncuesta->create();
							$opcion['OpcionesEncuesta']['preguntas_encuesta_id'] = $new_pregunta_id;
							$this->OpcionesEncuesta->save($opcion);
						}
					}
				}
			}
		}
		
		$this->redirect('/encuestas');
	}
	
	function delete($id){
		$this->Encuesta->id = $id;
		$data = $this->Encuesta->EncuestasAsignatura->find('count');
		if($data == 0){
			$this->Encuesta->delete($id);
			$this->_mensaje(__('mens_tipo_eliminado'),'/encuestas');
		}
		else{
			$this->_mensaje(__('error_eliminando_encuesta'),'/encuestas');
		}
	}
	
	public function edit($id = null) {
		$this->Encuesta->id = $id;
		if (!$this->Encuesta->exists()) {
			$this->_mensaje(__('error_encuestas'),'/encuestas',true);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->_clean_data();
			$this->Encuesta->set($this->request->data);
			if($this->Encuesta->validates()){
				if ($this->Encuesta->save()) {
					$this->_mensaje(__('mens_encuesta_modificada'),'/encuestas');
				} else {
					$this->_mensaje(__('error_encuestas'),'/encuestas',true);
				}
			}
		} else {
			$this->request->data = $this->Encuesta->read(null, $id);
		}
	}
	
	function mostrar($encuesta_id){
		$encuesta = $this->Encuesta->findById($encuesta_id);
		$preguntas=$this->PreguntasEncuesta->preguntas_encuesta($encuesta_id);
		foreach ($preguntas as $i=>$preg){
			$opciones=$this->OpcionesEncuesta->opciones_encuesta($preg['PreguntasEncuesta']['id']);
			$preguntas[$i]['OpcionesEncuesta']=$opciones;
		}
		$this->set('encuesta',$encuesta['Encuesta']);
		$this->set('preguntas',$preguntas);
	}
}
?>
