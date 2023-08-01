<?php
App::uses('AppController', 'Controller');
/**
 * Bloques Controller
 *
 * @property Bloque $Bloque
 */
class BloquesController extends AppController {

	var $helpers = array('Html','Form','Tree', 'Tooltip', 'Ajax', 'Js');
	var $uses = array('Bloque', 'Profesor', 'ViewProblemasCarpetas', 'Carpeta', 'Problema','Agrupacion','ViewBuscarProblema','Tarea');

	var $access = array
	(
	'*' => array('profesor'),
	);
/**
 * index method
 *
 * @return void
 */
 
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout',__('bloques'));
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}
	
	public function index() {
		$asignatura_id=$this->_profesor_asignatura_actual();
		$this->Bloque->recursive = -1;
		$this->paginate = array(
			'conditions' => array('Bloque.asignatura_id' => $asignatura_id),
		);
		$this->set('bloques', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Bloque->id = $id;
		if (!$this->Bloque->exists()) {
			throw new NotFoundException(__('Invalid bloque'));
		}
		$this->set('bloque', $this->Bloque->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->_clean_data();
			$data = $this->request->data;
			$data['Bloque']['asignatura_id'] = $this->_profesor_asignatura_actual();
			$this->Bloque->create();
			if ($this->Bloque->save($data)) {
				$this->_mensaje(__('mens_bloque_creado'),'/bloques/index/');
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
		$this->Bloque->id = $id;
		if (!$this->Bloque->exists()) {
			$this->_mensaje(__('error_bloque'),'/bloques/',true);
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$this->_clean_data();
			if ($this->Bloque->save($this->request->data)) {
				$this->_mensaje(__('mens_bloque_modificado'),'/bloques/index/');
			} else {
				$this->_mensaje(__('error_bloque'),'/bloques/',true);
			}
		} else {
			$this->request->data = $this->Bloque->read(null, $id);
		}
		$asignaturas = $this->Bloque->Asignatura->find('list');
		$this->set(compact('asignaturas'));
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
		$this->Bloque->id = $id;
		if (!$this->Bloque->exists()) {
			throw new NotFoundException(__('Invalid bloque'));
		}
		if ($this->Bloque->delete()) {
			$this->Session->setFlash(__('Bloque deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Bloque was not deleted'));
		$this->redirect(array('action' => 'index'));
	}



	function problemas(){
		$asignatura_id=$this->_profesor_asignatura_actual();

		//TODO: Comprobar bloqueid
		$profesor_id=$this->_usuario_actual();
		$propias=$this->Profesor->carpetas_propias($profesor_id);
		$subscritas=$this->Profesor->carpetas_externas_subscritas($profesor_id);
		$no_subscritas=$this->Profesor->carpetas_externas_no_subscritas($profesor_id);
		//mostrar tots els problemes o nomes els publicats??, si son nomes els publicats, es tindria que modificar la
		//vista viewbuscarproblema, perque no contempla el camp published, i fa que la cerca obri carpetes pero
		//no surt el problema
		$problemas=$this->ViewProblemasCarpetas->problemas_publicados();
		$carpetas=$this->Carpeta->carpetas();
		$bloques=$this->Bloque->bloques_asignatura($asignatura_id);

		//obtener asignaciones
		for($i=0;$i<count($bloques);$i++)
		{
			$bloques[$i]['Problema']=$this->Bloque->problemas($bloques[$i]['Bloque']['id']);
		}

		$this->set('propias',$propias);
		$this->set('bloques',$bloques);
		$this->set('subscritas',$subscritas);
		$this->set('no_subscritas',$no_subscritas);
		$this->set('carpetas',$carpetas);
		$this->set('problemas',$problemas);
		$this->set('action','view');
	}
	
	function _convertir_checkbox_info($form)
	{
		$result=array();
		foreach ($form as $item=>$value)
		{
			$item_data=split('_', $item);
			if ($item_data[0]!='')
			{
				$result[]=am(array($item),$item_data);
			}
		}
		return $result;
	}
	
	function asignar_problemas()
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		$data=$this->_convertir_checkbox_info($this->request->data);
		$problemas=array();
		$bloque_id=null;
		$error=false;

		foreach ($data as $info)
		{
			if (count($info)==5 && $info[1]=='problemas')
			{
				//se trata de un problema
				$problemas[]=$info[4];
			}
			elseif (count($info)==4)
			{
				if ($bloque_id===null)
				{
					$bloque_id=$info[3];
				}
				else
				{
					$error=true;
				}
			}
			else
			{
				$error=true;
			}
		}
		if ($error || ! count($problemas) || $bloque_id===null)
		{
			$this->_mensaje(__('error_asignar_problemas_bloque'),'/bloques/problemas/');
		}
		else
		{
			$prob_data=array();
			foreach ($problemas as $p)
			{
				$this->Problema->recursive=-1;
				$prob_data[]=$this->Problema->findById($p);
			}

			$this->set('problemas',$prob_data);
			$this->set('bloque_id',$bloque_id);
		}
	}
	
	function asignar_problemas_post($bloque_id)
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		if ($asignatura_id != $this->Bloque->asignatura($bloque_id))
		{
			$this->_mensaje(__('error_no_acceso'),'/bloques/',true);
		}

		$error=false;
		foreach ($this->request->data['tiempo'] as $problema_id=>$limite_minutos)
		{
			//TODO: Comprobar que se puede en el bloque indicado por asignatura correcta
			if (!$this->Agrupacion->guardar($bloque_id,$problema_id,$limite_minutos))
			{
				$error=true;
			}
		}
		if ($error)
		{
			$this->_mensaje(__('error_asignacion'),'/bloques/problemas/',true);
		}
		else
		{
			$this->_mensaje(__('mens_asignacion'),'/bloques/problemas/');
		}
	}
	
	function modificar_asignacion()
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		$data=$this->_convertir_checkbox_info($this->request->data);
		$problemas=array();
		$bloque_id=null;
		$error=false;

		foreach ($data as $info)
		{
			if (count($info)==6 && $info[1]=='bloques')
			{
				//se trata de una agrupación
				$agrupaciones[]=array(	'bloque_id'=>$info[4],
										'problema_id'=>$info[5]);
			}
			else
			{
				$error=true;
			}
		}
		if ($error || ! count($agrupaciones))
		{
			$this->_mensaje(__('error_mostrar_problemas_arbol'),'/bloques/problemas/');
		}
		else
		{
			foreach ($agrupaciones as $agr)
			{
				$this->Problema->recursive=-1;
				$p = $this->Problema->findById($agr['problema_id']);
				$a = $this->Agrupacion->buscar($agr['bloque_id'], $agr['problema_id']);
				$prob_data[$agr['bloque_id']][$agr['problema_id']]['nombre']= $p['Problema']['nombre'];
				$prob_data[$agr['bloque_id']][$agr['problema_id']]['tiempo']= $a['limite_minutos'];
			}
		
			$this->set('bloqs',$prob_data);
		}
	}
	
	function modificar_asignacion_post()
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		$error=false;
		foreach ($this->request->data as $b_id=>$p)
		{
			if ($asignatura_id != $this->Bloque->asignatura($b_id))
			{
				$this->_mensaje(__('error_no_acceso'),'/bloques/',true);
			}
		
			foreach ($p as $problema_id=>$limite_minutos)
			{
				if (!$this->Agrupacion->guardar($b_id,$problema_id,$limite_minutos))
				{
					$error=true;
				}
			}
		}
		if ($error)
		{
			$this->_mensaje(__('error_asignacion'),'/bloques/problemas/',true);
		}
		else
		{
			$this->_mensaje(__('mens_asignacion'),'/bloques/problemas/');
		}
	}

	
	function mostrar_ocultar_problemas($visible)
	{
		$asignatura_id=$this->_profesor_asignatura_actual();

		$data=$this->_convertir_checkbox_info($this->request->data);

		$agrupaciones=array();
		$error=false;

		foreach ($data as $info)
		{
			if (count($info)==6 && $info[1]=='bloques')
			{
				//se trata de una agrupación
				$agrupaciones[]=array(	'bloque_id'=>$info[4],
										'problema_id'=>$info[5]);
			}
			else
			{
				$error=true;
			}
		}
		if ($error || ! count($agrupaciones))
		{
			$this->_mensaje(__('error_mostrar_problemas_arbol'),'/bloques/problemas/');
		}
		else
		{
			foreach ($agrupaciones as $agr)
			{
				$bloque_id=$agr['bloque_id'];
				$problema_id=$agr['problema_id'];
				$this->Agrupacion->visible($bloque_id,$problema_id,$visible);
			}
			$this->_mensaje(__('mens_problemas_mostrados_ocultados'),'/bloques/problemas/');
		}
	}
	
	function buscar_problemas(){
		if (empty($this->request->data)){
			$this->render('/Elements/tree/buscar');
		}
		else{
			$this->_clean_data();
			$data = $this->request->data;
			$this->ViewBuscarProblema->recursive=2;
			$this->ViewBuscarProblema->Problema->contain('Carpeta');
			$problemas=$this->ViewBuscarProblema->buscar($data['Bloque']['nombre'],$data['Problema']);
			//$problemas=$this->Problema->buscar($search);
			$this->set('problemas',$problemas);
			$this->render('/Elements/tree/buscar_tree');
		}
	}
	
	function desasignar_problemas()
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		$data=$this->_convertir_checkbox_info($this->request->data);
		$agrupaciones=array();
		$error=false;
		
		foreach ($data as $info){
			if (count($info)==6 && $info[1]=='bloques'){
				//se trata de un problema asignado
				$agrupaciones[]=array('bloque_id'=>$info[4], 'problema_id'=>$info[5]);
			}
			else{
				$error=true;
			}
		}
		if ($error || ! count($agrupaciones)){
			$this->_mensaje(__('error_desasignar_problemas_bloque'),'/bloques/problemas/',true);
		}
		else{
			foreach ($agrupaciones as $agrupacion){
				$agrupacion_id=$this->Agrupacion->buscar($agrupacion['bloque_id'],$agrupacion['problema_id']);
				if (!$this->Agrupacion->delete($agrupacion_id)) $error=true;
			}
			if ($error){
				$this->_mensaje(__('error_desasignando'),'/bloques/problemas/',true);
			}
			else{
				$this->_mensaje(__('mens_desasignando'),'/bloques/problemas/');
			}
		}
	}
	
	function reasignar()
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		$data=$this->_convertir_checkbox_info($this->request->data);
		$agrupaciones=array();
		$bloque_id=null;
		$error=false;

		foreach ($data as $info){
			if (count($info)==6 && $info[1]=='bloques'){
				//se trata de una agrupación
				$agrupaciones[]=array(	'bloque_id'=>$info[4],
										'problema_id'=>$info[5]);
			}
			elseif (count($info)==4){
				if ($bloque_id===null){
					$bloque_id=$info[3];
				}
				else{
					$error=true;
				}
			}
			else{
				$error=true;
			}
		}
		if ($error || ! count($agrupaciones)  || $bloque_id===null){
			$this->_mensaje(__('error_mostrar_problemas_arbol'),'/bloques/problemas/');
		}
		else{
			foreach ($agrupaciones as $agrupacion){
				if($agrupacion['bloque_id'] != $bloque_id){
					if (!$this->Agrupacion->reasignar($agrupacion['bloque_id'], $bloque_id, $agrupacion['problema_id'])){
						$error=true;
					}
				}
			}
		}
		if ($error){
			$this->_mensaje(__('error_asignacion'),'/bloques/problemas/',true);
		}
		else{
			$this->_mensaje(__('mens_asignacion'),'/bloques/problemas/');
		}
	}
	
	function sort(){
		$asignatura_id=$this->_profesor_asignatura_actual();
		if (empty($this->request->data)){
			$data=$this->Bloque->bloques_asignatura($asignatura_id);
			$this->set('data',$data);
		}
		else{
			$orden = array();
			foreach($this->request->data['sort'] as $key=>$value){
				$orden[] = $value;
			}
			if ($this->Bloque->ordenar($asignatura_id,$orden)){
				$this->_mensaje(__('mens_ordenado'),'/bloques/index/');
			}
			else{
				$this->_mensaje(__('error_ordenado'),'/bloques/index/',true);
			}

		}
	}
	
	function notificar_problemas($enviar){
		$asignatura_id=$this->_profesor_asignatura_actual();
		$data=$this->_convertir_checkbox_info($this->request->data);
		$agrupaciones=array();
		$error=false;
		foreach ($data as $info){
			if (count($info)==6 && $info[1]=='bloques'){
				//se trata de una agrupación
				$agrupaciones[]=array(	'bloque_id'=>$info[4],
										'problema_id'=>$info[5]);
			}
			else{
				$error=true;
			}
		}
		if ($error || ! count($agrupaciones)){
			$this->_mensaje(__('error_notificar_problemas_arbol'),'/bloques/problemas/');
		}
		else{
			foreach ($agrupaciones as $agr){
				$bloque_id=$agr['bloque_id'];
				$problema_id=$agr['problema_id'];
				$this->Agrupacion->notificado($bloque_id,$problema_id,true);
				if ($enviar){
					//se debe enviar mail de notificación
					$agrupacion=$this->Agrupacion->buscar($bloque_id,$problema_id);
					$this->requestAction('/mail/enviar_notificacion_agrupacion/' . $agrupacion['id']);
					//$this->Tarea->crear('/mail/enviar_notificacion_agrupacion/' . $agrupacion['id'],2);
				}
			}
			$this->_mensaje(__('mens_problemas_notificados_ignorados'),'/bloques/problemas/');
		}
	}

}
