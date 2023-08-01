<?php
App::uses('AppController', 'Controller');

class ProfesoresController extends AppController {
	
	public $name = 'Profesores';
	public $uses = array('Profesor','Usuario','Asignatura');
	
	var $access = array
	(
	'add' => array('admin'),
	'delete' => array('admin'),
	'edit' => array('admin'),
	'docencia' => array('admin'),
	'seleccionar' => array('profesor'),
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_administrar_profesores'));
		if($this->request->isAjax()){
			$this->layout = 'ajax';
			$this->autoRender = false;
		}
    }
    
	public function index() {
		$this->Profesor->recursive = 0;
		$this->Profesor->contain('Usuario');
		$this->set('tipo_auth',$this->Usuario->tipo_auth);
		$this->set('profesores', $this->Profesor->find('all'));
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
				$id = $this->Profesor->crear($this->request->data);
				if($id){
					if($ajax){
						$result = array("result" => "0", "id" => $id);
						echo json_encode($result);
					}
					else
						$this->_mensaje(__('mens_profesor_creado'),'/profesores/');
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
					$this->_mensaje(__('error_profesor'),'/profesores/',true);
			}
			else{
				$id = $this->Usuario->save($this->request->data);
				if($id){
					if($ajax){
						$result = array("result" => "0", "id" => $id);
						echo json_encode($result);
					}
					else
						$this->_mensaje(__('mens_profesor_modificado'),'/profesores/');
				}
			}
		}
	}
	
	function delete($id){
		$ajax = $this->request->isAjax();
		$this->Profesor->id = $id;
		if ($this->Profesor->delete($id)){
			if ($ajax) {
				$result = array("result" => "0");
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('mens_profesor_eliminado'),'/profesores/');
			}
		}
		else{
			if ($ajax) {
				$result = array("result" => "1", __('profesor') => __('error_profesor_eliminado'));
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('error_profesor_eliminado'),'/profesores/',true);
			}
		}
		return;
	}
	
	function docencia($profesor_id=null){
		if (empty($this->request->data)){
			$this->Asignatura->recursive=-1;
			$asignaturas=$this->Asignatura->find('all');
			$this->Profesor->contain('Asignatura');
			$data=$this->Profesor->findById($profesor_id);
			if (!$data){
				$this->_mensaje(__('error_profesor'),'/admin_profesores/',true);
			}
			foreach ($data['Asignatura'] as $asig){
				$this->request->data['Asignatura']['docencia_' . $asig['id']]=true;
			}
			$this->request->data['Profesor']['id']=$profesor_id;
			$this->set('asignaturas',$asignaturas);
		}
		else{
			$profesor_id=$this->request->data['Profesor']['id'];
			$this->Profesor->recursive=-1;
			$data=$this->Profesor->findById($profesor_id);
			if (!$data)
				$this->_mensaje(__('error_profesor'),'/admin_profesores/',true);

			$asignaturas=array();

			foreach ($this->request->data['Asignatura'] as $asig=>$checked)
				if ($checked && preg_match('/^docencia_(\d+)$/',$asig,$matches))
					$asignaturas[]=$matches[1];

			$this->Profesor->guardar_docencia($profesor_id,$asignaturas);
			$this->_mensaje(__('mens_profesor_docen_modificada'),'/profesores/');
		}
	}
	
	function seleccionar()
	{
		$this->set('title_for_layout',__('titulo_profesores'));
		if (empty($this->request->data))
		{
			$this->Profesor->recursive = 1;
			$this->Profesor->contain('Asignatura');
			$profesor = $this->Profesor->findById($this->_profesor_actual());
			$asignaturas = array();
			$descripciones = array();
			foreach($profesor['Asignatura'] as $asignatura){
				$asignaturas[$asignatura['id']] = $asignatura['nombre'];
				$descripciones[$asignatura['id']] = $asignatura['descripcion'];
			}

			$this->set('asignaturas',$asignaturas);
			$this->set('descripciones',$descripciones);
		} else {
			$this->_clean_data();
			$profesor_id = $this->_profesor_actual();
			$asignatura_id = $this->request->data['Profesor']['asignaturas'];
			$this->Profesor->Asignatura->recursive = -1;
			$asignatura = $this->Profesor->Asignatura->findById($asignatura_id);
			$asignatura_name = $asignatura['Asignatura']['nombre'];
			$this->_profesor_asignatura_actual($asignatura_id, $asignatura_name);
			$this->_mensaje(__('mens_asignatura_seleccion'),'/estadisticas/asignatura');
		}
	}
}
