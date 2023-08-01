<?php
App::uses('AppController', 'Controller');
/**
 * SintaxisRespuestas Controller
 *
 * @property SintaxisRespuesta $SintaxisRespuesta
 */
class SintaxisRespuestasController extends AppController {
	var $access = array
	(
	'*' => array('admin'),
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_sintaxis_respuestas'));
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}

	function avanzado(){
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->SintaxisRespuesta->recursive = 0;
		$this->set('sintaxisRespuestas', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SintaxisRespuesta->create();
			if ($this->SintaxisRespuesta->save($this->request->data)) {
				$this->_mensaje(__('mens_tipo_creado'),'/sintaxis_respuestas/');
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->SintaxisRespuesta->id = $id;
		if (!$this->SintaxisRespuesta->exists()) {
			$this->_mensaje(__('error_sintaxis'),'/sintaxis_respuestas',true);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SintaxisRespuesta->save($this->request->data)) {
				$this->_mensaje(__('mens_tipo_modificado'),'/sintaxis_respuestas');
			} else {
				$this->_mensaje(__('error_sintaxis'),'/sintaxis_respuestas',true);
			}
		} else {
			$this->request->data = $this->SintaxisRespuesta->read(null, $id);
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
			$this->_mensaje(__('error_sintaxis'),'/sintaxis_respuestas',true);
		}
		$this->SintaxisRespuesta->id = $id;
		if (!$this->SintaxisRespuesta->exists()) {
			$this->_mensaje(__('error_sintaxis'),'/sintaxis_respuestas',true);
		}
		if ($this->SintaxisRespuesta->delete()) {
			$this->_mensaje(__('mens_tipo_eliminado'),'/sintaxis_respuestas');
		}
	}
	
	function sort(){
		if (empty($this->request->data)){
			$this->SintaxisRespuesta->recursive=-1;
			$data=$this->SintaxisRespuesta->find('all',array('order'=>array('SintaxisRespuesta.orden')));
			$this->set('data',$data);
		}
		else{
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->SintaxisRespuesta->ordenar($orden)){
				$this->_mensaje(__('mens_ordenado'),'/sintaxis_respuestas/index/');
			}
			else{
				$this->_mensaje(__('error_ordenando'),'/sintaxis_respuestas/index/',true);
			}
		}
	}
}
