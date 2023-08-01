<?php
App::uses('AppController', 'Controller');
/**
 * TiposRespuestas Controller
 *
 * @property TiposRespuesta $TiposRespuesta
 */
class TiposRespuestasController extends AppController {
	
	var $access = array
	(
	'*' => array('admin'),
	);

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_tipos_respuestas'));
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->TiposRespuesta->recursive = 0;
		$this->set('tiposRespuestas', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->_clean_data();
			$this->TiposRespuesta->create();
			if ($this->TiposRespuesta->save($this->request->data)) {
				$this->_mensaje(__('mens_tipo_creado'),'/tipos_respuestas');
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
		$this->TiposRespuesta->id = $id;
		if (!$this->TiposRespuesta->exists()) {
			$this->_mensaje(__('error_tipos'),'/tipos_respuestas',true);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->_clean_data();
			if ($this->TiposRespuesta->save($this->request->data)) {
				$this->_mensaje(__('mens_tipo_modificado'),'/tipos_respuestas');
			} else {
				$this->_mensaje(__('error_tipos'),'/tipos_respuestas',true);
			}
		} else {
			$this->request->data = $this->TiposRespuesta->read(null, $id);
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
		$this->TiposRespuesta->id = $id;
		if (!$this->TiposRespuesta->exists()) {
			$this->_mensaje(__('error_tipos'),'/tipos_respuestas',true);
		}
		if ($this->TiposRespuesta->delete()) {
			$this->_mensaje(__('mens_tipo_eliminado'),'/tipos_respuestas');
		}
	}
	
	function sort(){
		if (empty($this->request->data)){
			$this->TiposRespuesta->recursive=-1;
			$data=$this->TiposRespuesta->find('all',array('order'=>array('TiposRespuesta.orden')));
			$this->set('data',$data);
		}
		else{
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->TiposRespuesta->ordenar($orden)){
				$this->_mensaje(__('mens_ordenado'),'/tipos_respuestas/index/');
			}
			else{
				$this->_mensaje(__('error_ordenando'),'/tipos_respuestas/index/',true);
			}
		}
	}
}
