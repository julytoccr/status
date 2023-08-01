<?php
App::uses('AppController', 'Controller');

class AdministradoresController extends AppController {
	
	public $name = 'Administradores';
	public $uses = array('Administrador','Usuario');
	
	var $access = array
	(
	'*' => array('admin'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_administrar_administradores'));
		if($this->request->isAjax()){
			$this->layout = 'ajax';
			$this->autoRender = false;
		}
    }

	public function index() {
		$this->Administrador->recursive = 0;
		$this->set('tipo_auth',$this->Usuario->tipo_auth);
		$this->Administrador->contain('Usuario');
		$this->set('administradores', $this->Administrador->find('all'));
	}

	public function add() {
		$ajax = $this->request->isAjax();
		if(!empty($this->request->data)){
			$this->_clean_data();
			$this->Usuario->set($this->request->data);
			if (!$this->Usuario->validates()){
				if($ajax){
					$result = array("result" => "1");
					foreach($this->Usuario->validationErrors as $field => $error) {
						$result[$field] = $error;
					}
					echo json_encode($result);
				}
				else
					return;
			}
			else{
				$id = $this->Administrador->crear($this->request->data);
				if($id){
					if($ajax){
						$result = array("result" => "0", "id" => $id);
						echo json_encode($result);
					}
					else
						$this->_mensaje(__('mens_admin_creado'),'/administradores/');
				}
			}
		}
	}
	
	public function edit($id) {
		$ajax = $this->request->isAjax();
		if(!empty($this->request->data)){
			$this->Usuario->id = $id;
			$this->_clean_data();
			$this->Usuario->set($this->request->data);
			if (!$this->Usuario->validates()){
				if($ajax){
					$result = array("result" => "1");
					foreach($this->Usuario->validationErrors as $field => $error) {
						$result[$field] = $error;
					}
					echo json_encode($result);
				}
				else
					$this->_mensaje(__('error_admin_modificado'),'/administradores/',true);
			}
			else{
				$id = $this->Usuario->save($this->request->data);
				if($id){
					if($ajax){
						$result = array("result" => "0", "id" => $id);
						echo json_encode($result);
					}
					else
						$this->_mensaje(__('admin_modificado'),'/administradores/');
				}
			}
		}
	}
	
	function delete($id){
		$ajax = $this->request->isAjax();
		$this->Administrador->id = $id;
		if ($this->Administrador->delete($id)){
			if ($ajax) {
				$result = array("result" => "0");
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('mens_admin_eliminado'),'/administradores/');
			}
		}
		else{
			if ($ajax) {
				$result = array("result" => "1", __('administrador') => __('error_admin_eliminado'));
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('error_admin_eliminado'),'/administradores/',true);
			}
		}
		return;
	}
}
