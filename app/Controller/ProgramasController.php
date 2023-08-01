<?php
//App::uses('AppController', 'Controller');
//App::import('Vendor','/estatus/rserve/estatus_rserve');
/**
 * Programas Controller
 *
 * @property Programa $Programa
 */
class ProgramasController extends AppController {

	public $uses = array('Programa','Asignatura','Problema');
	
	var $access = array
	(
	'*' => array('admin'),
	'run'=>array('admin','profesor'),
	'index'=>array('admin','profesor')
	);

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_programas'));
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
		if($this->_es_profesor() && !$this->_es_administrador()){
			$this->paginate = array(
				'conditions' => array('Programa.publico'=>1),
			);
			$this->set('profesor',true);
		}
		$this->Programa->recursive = -1;
		$this->set('programas', $this->paginate());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Programa->create();
			if ($this->Programa->save($this->request->data)) {
				$this->_mensaje(__('mens_programa_creado'),'/programas');
			} else {
				$this->_mensaje(__('error_programa'),'/programas',true);
			}
		}
		else{
			$params=array(
				0=>'Alfanumérico',
				1=>'Asignatura',
				2=>'Problema'
			);
			$this->set('tipo_params',$params);
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
		$this->Programa->id = $id;
		if (!$this->Programa->exists()) {
			$this->_mensaje(__('error_programa'),'/programas',true);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Programa->save($this->request->data)) {
				$this->_mensaje(__('mens_programa_modificado'),'/programas');
			} else {
				$this->_mensaje(__('error_programa'),'/programas',true);
			}
		} else {
			$params=array(
				0=>'Alfanumérico',
				1=>'Asignatura',
				2=>'Problema'
			);
			$this->set('tipo_params',$params);
			$this->request->data = $this->Programa->read(null, $id);
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
		$this->Programa->id = $id;
		if (!$this->Programa->exists()) {
			$this->_mensaje(__('error_programa'),'/programas',true);
		}
		if ($this->Programa->delete()) {
			$this->_mensaje(__('mens_programa_eliminado'),'/programas');
		}
		$this->_mensaje(__('error_programa'),'/programas',true);
	}
	
	function run($id=null){
		//uses('sanitize');

		if (!$this->request->is('get')){
			$id=$this->request->data['Programa']['id'];
		}
		else{
			//$id=Sanitize::paranoid($id);
		}

		$programa=$this->Programa->findById($id);
		if (! $programa)  $this->_mensaje(__('error_programa'),'/',true);
		if (! $programa['Programa']['publico']){
			//obliga que el usuario sea admin, de lo contrario provoca error y redirige
			$admin_id=$this->_admin_actual();
		}
		$this->Asignatura->recursive=-1;
		/*$data=$this->Asignatura->find('all');
		$asignaturas=array();
		foreach ($data as $row) $asignaturas[$row['Asignatura']['id']]=$row['Asignatura']['nombre'];*/
		$this->set('asignaturas',$this->Asignatura->find('list'));

		$this->Problema->recursive=-1;
		/*$data=$this->Problema->find('all');
		$problemas=array();
		foreach ($data as $row) $problemas[$row['Problema']['id']]=$row['Problema']['id'] . ' - '. $row['Problema']['nombre'];*/
		$this->set('problemas',$this->Problema->find('list'));

		if (! empty($this->request->data)){
			$rserve = new EstatusRserve();
			$parametros=array();
			foreach ($this->request->data['Programa'] as $key=>$value){
				if (preg_match('/^param(\d+)$/',$key,$matches)){
					$index=$matches[1] - 1;
					$parametros[$index]=$value;
				}
			}
			
			$resultados=$this->Programa->evaluar_programa($id , $parametros,$rserve);
			$this->set('mensajes',$resultados['mensajes']);
			$this->set('grafico',$resultados['grafico']);
		}
		$this->request->data['Programa']['id']=$id;
		$this->set('programa',$programa['Programa']);
	}
	
	function sort(){
		if (empty($this->request->data)){
			$this->Programa->recursive=-1;
			$data=$this->Programa->find('all',array('order'=>array('Programa.orden')));
			$this->set('data',$data);
		}
		else{
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->Programa->ordenar($orden)){
				$this->_mensaje(__('mens_ordenado'),'/programas/index/');
			}
			else{
				$this->_mensaje(__('error_ordenando'),'/programas/index/',true);
			}
		}
	}
}
