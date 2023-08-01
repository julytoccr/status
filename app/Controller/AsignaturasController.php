<?php
App::uses('AppController', 'Controller');
/**
 * Asignaturas Controller
 *
 * @property Asignatura $Asignatura
 */
class AsignaturasController extends AppController {

    var $access = array
	(
	'*' => array('admin'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_administrar_asignaturas'));
		if($this->request->isAjax()){
			$this->layout = 'ajax';
			$this->autoRender = false;
		}
    }
	
	function _save($data){
		if ($data['Asignatura']['fecha_inicio']=''){
			$data['Asignatura']['fecha_inicio']=null;
		}
		if ($data['Asignatura']['fecha_fin']=''){
			$data['Asignatura']['fecha_fin']=null;
		}
		return $this->Asignatura->save($data);
	}

	public function index() {
		$this->Asignatura->recursive = -1;
		$this->set('asignaturas', $this->Asignatura->find('all',array('order'=>'id DESC')));
		if(isset($this->params['named']['sort'])){
			if($this->params['named']['sort'] == 'nombre'){
				$this->set('title_for_layout',__('titulo_administrar_asignaturas_alfabetico'));
			}
			else{
				$this->set('title_for_layout',__('titulo_administrar_asignaturas_creacion'));
			}
		}
	}
	
	function add(){
		$ajax = $this->request->isAjax();
		if (! empty($this->request->data)){
			$this->_clean_data();
			$id = $this->_save($this->request->data);
			if ($id){
				if ($ajax) {
					$result = array("result" => "0", "id" => $id);
					echo json_encode($result);
				}
				else {
					$this->_mensaje(__('mens_asignatura_creada'),'/asignaturas/');
				}
			}
			else if ($ajax) {
				$result = array("result" => "1");
				foreach($this->Asignatura->validationErrors as $field => $error) {
					$result[$field] = $error;
				}
				echo json_encode($result);
			}
			return;
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

	
	function edit($asignatura_id=null){
		$ajax = $this->request->isAjax();
		if (empty($this->request->data)){
			$this->Asignatura->recursive = -1;
			$this->request->data = $this->Asignatura->read(null, $id);
			$this->_set_activos($this->request->data);
		}
		else{
			$this->_clean_data();
			$this->Asignatura->id = $asignatura_id;
			if (!$this->Asignatura->exists()){
				if ($ajax) {
					$result = array("result" => "1", "Subject" => __('error_asignatura'));
					echo json_encode($result);
				}
				else {
					$this->_mensaje(__('error_asignatura'),'/asignaturas/',true);
				}
				return;
			}
			if ($this->_save($this->request->data)){
				if ($ajax) {
					$result = array("result" => "0");
					echo json_encode($result);
				}
				else {
					$this->_mensaje(__('mens_asignatura_modificada'),'/asignaturas/');
				}
			}
			else if ($ajax) {
				$result = array("result" => "1");
				foreach($this->Asignatura->validationErrors as $field => $error) {
					$result[$field] = $error;
				}
				echo json_encode($result);
			}
			return;
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
	
	function delete($asignatura_id){
		$ajax = $this->request->isAjax();
		if ($this->Asignatura->delete($asignatura_id)){
			if ($ajax) {
				$result = array("result" => "0");
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('mens_asignatura_eliminada'),'/asignaturas/');
			}
		}
		else{
			if ($ajax != NULL) {
				$result = array("result" => "1", __('asignatura') => __('error_asignatura_eliminada'));
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('error_asignatura_eliminada'),'/asignaturas/',true);
			}
		}
		return;
	}
	
	function select($id){
		$ajax = $this->request->isAjax();
		$this->Asignatura->recursive=-1;
		$asignatura=$this->Asignatura->findById($id);
		$asignatura_name = $asignatura['Asignatura']['nombre'];
		$this->_admin_asignatura_actual($id, $asignatura_name);
		if ($ajax) {
			$result = array("result" => "0", __('asignatura') => __('form_asignatura_sel'));
			echo json_encode($result);
		}
	}
	
	function _set_activos(&$data){
		$data['Asignatura']['activar_fecha_inicio']=($data['Asignatura']['fecha_inicio']!==null);
		$data['Asignatura']['activar_fecha_fin']=($data['Asignatura']['fecha_fin']!==null);
	}
}
