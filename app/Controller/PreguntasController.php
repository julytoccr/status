<?php
App::uses('AppController', 'Controller');
/**
 * Preguntas Controller
 *
 * @property Pregunta $Pregunta
 */
class PreguntasController extends AppController {
	
	var $uses=array('Pregunta','Problema','TiposRespuesta','SintaxisRespuesta');
	var $access = array
	(
	'*' => array('profesor'),
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}
/**
 * index method
 *
 * @return void
 */
	public function index($problema_id) {
		$this->Pregunta->recursive = 0;
		$this->paginate = array('conditions' => array('problema_id' => $problema_id), 'order' => array('orden'=>'asc'));
		$this->set('preguntas', $this->paginate());
		$this->set('problema_id',$problema_id);
	}
	
	function _set_variables_respuestas($problema_id)
	{
		$variables=$this->Problema->variables($problema_id);
		$ret=array();
		foreach ($variables as $var)
		{
			$ret[$var]=$var;
		}
		$this->set('variables_respuestas_hash',$ret);
		$this->set('variables_respuestas_array',$variables);
	}

/**
 * add method
 *
 * @return void
 */
	public function add($problema_id) {
		if ($this->request->is('post')) {
			$this->Pregunta->create();
			if ($this->Pregunta->save($this->request->data)) {
				$this->_mensaje(__('mens_pregunta_creada'),'/preguntas/index/' . $problema_id);
			}
		}
		$this->_set_variables_respuestas($problema_id);
		$this->set('problema_id',$problema_id);
		$sintaxisRespuestas = $this->Pregunta->SintaxisRespuesta->find('list');
		$this->set('tiposRespuesta',$this->TiposRespuesta->find('list'));
		$this->set(compact('sintaxisRespuestas'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Pregunta->id = $id;
		if (!$this->Pregunta->exists()) {
			throw new NotFoundException(__('Invalid pregunta'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Pregunta->save($this->request->data)) {
				$this->_mensaje(__('mens_pregunta_modificada'),'/preguntas/index/' . $this->request->data['Pregunta']['problema_id']);
			} else {
				$this->_mensaje(__('error_preguntas'),'/editor/',true);
			}
		} else {
			$this->Pregunta->recursive=-1;
			$data = $this->Pregunta->read(null, $id);
			$problema_id = $data['Pregunta']['problema_id'];
			$this->_set_variables_respuestas($problema_id);
			$sintaxisRespuestas = $this->Pregunta->SintaxisRespuesta->find('list');
			$this->request->data = $data;
			$this->set('problema_id',$problema_id);
			$this->set('tiposRespuesta',$this->TiposRespuesta->find('list'));
			$this->set(compact('sintaxisRespuestas'));
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Pregunta->id = $id;
		$data=$this->Pregunta->findById($id);
		$problema_id=$data['Pregunta']['problema_id'];
		if (!$this->Pregunta->exists()) {
			$this->_mensaje(__('error_preguntas'),'/editor/',true);
		}
		if ($this->Pregunta->delete()) {
			$this->_mensaje(__('mens_pregunta_eliminada'),'/preguntas/index/' . $problema_id);
		}
		$this->_mensaje(__('error_preguntas'),'/editor/',true);
		$this->redirect(array('action' => 'index'));
	}
	
	function seleccionar_tipos_respuesta()
	{
		if (empty($this->request->data)){
			return;
		}
		$data=$this->TiposRespuesta->findById($this->data['Pregunta']['tipos_respuesta_id']);
		if (!$data) return;
		echo $data['TiposRespuesta']['expresiones'];
	}
	
	function sort($problema_id){
		if (empty($this->request->data)){
			$this->Pregunta->recursive = -1;
			$data=$this->Pregunta->find('all',array('conditions'=>array('problema_id'=>$problema_id),'order' => array('orden'=>'asc')));
			$this->set('data',$data);
			$this->set('problema_id',$problema_id);
		}
		else{
			$orden = array();
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->Pregunta->ordenar($problema_id,$orden)){
				$this->_mensaje(__('mens_ordenado'),'/preguntas/index/'.$problema_id);
			}
			else{
				$this->_mensaje(__('error_ordenado'),'/preguntas/index/'.$problema_id,true);
			}

		}
	}
}
