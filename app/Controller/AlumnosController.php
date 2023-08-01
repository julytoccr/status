<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'excel/AkExcelToArray');
/**
 * Alumnos Controller
 *
 * @property Alumno $Alumno
 */
class AlumnosController extends AppController {
	
	var $uses=array('Alumno','Asignatura','Usuario','Grupo','Usuario','ViewAlumnosUsuario');
	var $helpers=array('Jquery');
	var $access = array
	(
		'add' => array('admin'),
		'alta_excel' => array('admin'),
		'alta_excel_final' => array('admin'),
		'avatar_ultimo_problema_aprovado' => array('alumno'),
		'configuracion' => array('alumno'),
		'delete' => array('admin'),
		'delete_avatar' => array('alumno'),
		'edit' => array('admin'),
		'enviarpassword' => array('admin'),
		'envio_passwords' => array('admin'),
		'home' => array('alumno'),
		'index' => array('admin'),
		'seleccionar' => array('alumno'),
	);
    /*var $access = array
    (
		'admin' => array('add','edit','delete','index','alta_excel','alta_execel_final','enviarpassword','envio_passwords'),
		'alumno' => array('avatar_ultimo_problema_aprobado','configuracion','delete_avatar','home','seleccionar'),
	);*/

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_alumnos'));
		if($this->request->isAjax()){
			$this->layout = 'ajax';
			$this->autoRender = false;
		}
    }
    
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$asignatura_id=$this->_admin_asignatura_actual();
		$data=$this->Asignatura->alumnos($asignatura_id);
		$this->set('tipo_auth',$this->Usuario->tipo_auth);
		$this->set('alumnos', $data);
	}

/**
 * add method
 *
 * @return void
 */
	
	public function add() {
		$ajax = $this->request->isAjax();
		if(!empty($this->request->data)){
			$this->_clean_data();
			$asignatura_id=$this->_admin_asignatura_actual();
			$this->request->data['Grupo']['asignatura_id']=$asignatura_id;
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
				$id = $this->Alumno->crear($this->request->data['Usuario'],$asignatura_id,$this->request->data['Grupo']['nombre'],$this->request->data['Alumno']['repetidor']);
				if ($id >= 0){
					if ($ajax) {
						$result = array("result" => "0", "id" => $id);
						echo json_encode($result);
					}
					else {
						$this->_mensaje(__('mens_alumno_creado'),'/alumnos/');
					}
				}
				else if ($id == -1 && $ajax) {
					$result = array("result" => "1", "Usuario.login" => __('inv_alumno_existe_asignatura'));
					echo json_encode($result);
				}
				else {
					if ($ajax) {
						array("result" => "1");
						echo json_encode($result);
					}
				}
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
	
	public function edit($id) {
		$ajax = $this->request->isAjax();
		if(!empty($this->request->data)){
			$this->Alumno->id = $id;
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
					$this->_mensaje(__('error_alumno'),'/alumnos/',true);
			}
			else{
				if($this->Alumno->modificar($this->request->data)){
					if($ajax){
						$result = array("result" => "0", "id" => $id);
						echo json_encode($result);
					}
					else
						$this->_mensaje(__('mens_alumno_modificado'),'/alumnos/');
				}
				else{
					if ($ajax) {
						$result = array("result" => "1");
						echo json_encode($result);
					}
				}
			}
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

	function delete($id){
		$ajax = $this->request->isAjax();
		$this->Alumno->id = $id;
		if ($this->Alumno->delete($id)){
			if ($ajax) {
				$result = array("result" => "0");
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('mens_alumno_eliminado'),'/alumnos/');
			}
		}
		else{
			if ($ajax) {
				$result = array("result" => "1", __('alumno') => __('error_alumno_eliminado'));
				echo json_encode($result);
			}
			else {
				$this->_mensaje(__('error_alumno_eliminado'),'/alumnos/',true);
			}
		}
		return;
	}
	
	function home(){
		if($this->Session->check('alumno.asignatura.id')){
			$asignatura_id = $this->_alumno_asignatura_actual();
			$alumno_id=$this->_usuario_actual();
			$this->set('data', $this->Alumno->problemas($alumno_id, $asignatura_id));
		}
		$this->render('/Elements/alumno/home');
	}
	
	function seleccionar()
	{
		if (empty($this->request->data))
		{
			$data = $this->Alumno->asignaturas($this->_usuario_actual(),true);
			$asignaturas = array();
			$descripciones = array();
			foreach($data as $asignatura){
				$asignaturas[$asignatura['id']] = $asignatura['nombre'];
				$descripciones[$asignatura['id']] = $asignatura['descripcion'];
			}
			$this->set('asignaturas',$asignaturas);
			$this->set('descripciones',$descripciones);
		} else {
			$this->_clean_data();
			$usuario_id = $this->_usuario_actual();
			$asignatura_id = $this->request->data['Alumno']['asignaturas'];
			$this->loadModel('Asignatura');
			if (! $this->Asignatura->es_activa($asignatura_id))
				$this->_mensaje(__('error_no_acceso'),'/',true);

			$alumno_id=$this->Alumno->buscar($usuario_id, $asignatura_id);
			
			if ($alumno_id) {
				$this->_alumno_actual($alumno_id);
				$asignatura = $this->Asignatura->findById($asignatura_id);
				$this->_alumno_asignatura_actual($asignatura_id,$asignatura['Asignatura']['nombre']);
				$this->Session->delete('alumnos.bloque_id');

				$this->_mensaje(__('mens_asignatura_seleccion'),'/');
			} else
				$this->_mensaje(__('error_alumno_no_matriculado'),null,true);
		}
	}
	
	function envio_passwords(){
		$asignatura_id=$this->_admin_asignatura_actual();
		$this->loadModel('Asignatura');
		$data=$this->Asignatura->alumnos_password_plano($asignatura_id);
		$this->set('paginate',false);
		$this->set('alumnos',$data);
	}
	
	function enviarpassword($alumno_id){
		$this->Alumno->contain('Usuario');
		$data=$this->Alumno->findById($alumno_id);
		if($data['Usuario']['tipo_auth'] == 'plain'){
			//$this->Usuario->enviar_password($data['Usuario']['id']);
			$this->requestAction('/mail/enviar_password_usuario/' . $data['Usuario']['id']);
			$this->_mensaje(__('password_enviado'),'/alumnos/');
		}
		else{
			$this->_mensaje(__('usuario_no_auth_estatus'),'/alumnos/');
		}
	}

	function configuracion(){
		$avatar_kbmax=20;
		$this->set('avatar_kbmax',$avatar_kbmax);
		$alumno_id=$this->_alumno_actual();
		$this->Alumno->recursive=-1;
		$data=$this->Alumno->findById($alumno_id);
		if (!$data) $this->_mensaje(__('error_acceso_datos_alumno'),null,true);
		$alumno=$data['Alumno'];
		if (!empty($this->request->data)){
			$info=$this->request->data['Alumno']['avatar'];
			if ($info['tmp_name']){
				if (! in_array($info['type'],array('image/jpeg','image/jpg'))){
					$this->_mensaje(__('error_subir_jpg'),null,true);
				}
				$avatar_dir=WWW_ROOT. 'img' . DS . 'avatars' . DS;
				$avatar_content=file_get_contents($info['tmp_name']);
				if (strlen($avatar_content)>$avatar_kbmax*1024){
					$this->_mensaje(__('error_kb_jpg',array($avatar_kbmax)),null,true);
				}
				$fn=md5($alumno_id . '_' . $avatar_content) .'.jpg';
				//borrar avatar anterior
				if ($alumno['avatar']){
					@unlink ($avatar_dir . $alumno['avatar']);
				}
				$alumno['avatar']=$fn;
				file_put_contents($avatar_dir . $fn,$avatar_content);
			}
			//uses('sanitize');
			//$alias=Sanitize::paranoid($this->data['Alumno']['alias'],array('-',' ','.','_','(',')'));
			$alumno['alias']=$this->request->data['Alumno']['alias'];
			$this->Alumno->save($alumno);
			$this->_mensaje(__('mens_config_guardada'),'/');
		}
		else{
			$this->request->data=$data;
			$this->set('alumno',$alumno);
		}
	}
	
	function delete_avatar(){
		$alumno_id=$this->_alumno_actual();
		$this->Alumno->recursive=-1;
		$data=$this->Alumno->findById($alumno_id);
		if (!$data) $this->_mensaje(__('error_acceso_datos_alumno'),null,true);
		$alumno=$data['Alumno'];
		$alumno['avatar']=null;
		$this->Alumno->save($alumno);
		$this->_mensaje(__('mens_avatar_eliminado'),'/alumnos/configuracion');
	}
	
	function avatar_ultimo_problema_aprobado(){
		$this->loadModel('EstEjecucion');
		$this->EstEjecucion->contain('Alumno','Problema');
		$data=$this->EstEjecucion->find('all',
				array(
					'conditions'=>
						array(
							'EstEjecucion.nota >='=>'5',
							'EstEjecucion.alumno_id IS NOT NULL',
							'EstEjecucion.asignatura_id'=>$this->_alumno_asignatura_actual(),
						),
					'order'=>
						array('EstEjecucion.ejecucion_id'=>'DESC'),
				)
			);
		if ($data)
		{
			$this->set('data',$data[0]);
			$this->render('avatar_ultimo_problema_aprobado/exists');
		}
		else
		{
			$this->render('avatar_ultimo_problema_aprobado/not_exists');
		}
	}
	
	function alta_excel(){
		if (!empty($this->request->data)){
			$info=$this->request->data['Alumno']['excel'];
			if (!strpos($info['type'],'excel')){
				$this->_mensaje(__('error_subir_excel'),null,true);
			}
			else{
				// Suhosin estableix un nombre límit de variables enviades per post.
				// Per augmentar el valor, cal modificar php.ini a l'apartat [suhosin]
				//$max_altes = ini_get("suhosin.request.max_vars");
				//$max_altes_alt = ini_get("suhosin.post.max_vars");
				$max_altes = 35000;
				$max_altes_alt = $max_altes;
				if (is_null($max_altes) and is_null($max_altes_alt)) $max_altes = 35000; // No hi ha limit establert, per posar un limit...
				elseif (is_null($max_altes) or (! is_null($max_altes_alt) and $max_altes_alt < $max_altes)) $max_altes = $max_altes_alt;
				unset($max_altes_alt);
				$max_altes = (int)($max_altes / 7); // Cada usuari ocupa 7 camps: tipus_auth, nom, cognom, email, login, grup i repetidor
				$file=$info['tmp_name'];
				$excel_data=AkExcelToArray::convert_file($file,1);
				$asignatura_id=$this->_admin_asignatura_actual();
				$tipos_auth = array('plain','ldap','fib');
				if(!in_array($this->request->data['Usuario']['tipo_auth'],$tipos_auth)){
					$this->_mensaje(__('error_tipo_auth'),'/',true);
				}
				$alumnos=array();
                $i = 0;
                $lastname = "none";
				foreach ($excel_data as $row){
					$a=array();
					$a['nombre']=$row[0];
					if ($a['nombre'] != "") {
						if ($i == $max_altes) {
							// S'ha sobrepassat el màxim de usuaris que permet el Suhosin.
							$this->set('limit', $max_altes);
							break;
						} else {
							$lastname = $a['nombre'] . " " . $row[1];
							++$i;
						}
						$a['apellidos']=$row[1];
						$a['email']=$row[2];
						$a['login']=$row[3];
						$a['grupo']=$row[4];
						$a['repetidor']=$row[5];
						$a['tipo_auth']=$this->request->data['Usuario']['tipo_auth'];
						$alumnos[]=$a;
                     }
				}
                $this->set('readscount', $i);
                $this->set('lastuser', $lastname);
				$this->set('alumnos',$alumnos);
				$this->set('tipo_auth',$this->request->data['Usuario']['tipo_auth']);
				$this->render('alta_excel/post');
			}
		}
		else{
			$this->render('alta_excel/form');
		}
	}
	
	function alta_excel_final(){
		if (empty($this->request->data)){
			$this->redirect('/alumnos/alta_excel');
		}
		else{
			$asignatura_id=$this->_admin_asignatura_actual();
			$data=$this->request->data['Alumno'];
			$alumnos=array();
			foreach ($data as $key=>$value){
				$matches=array();
				if (preg_match('/^(.*)_(\d+)$/',$key,$matches)){
					$field=$matches[1];
					$index=$matches[2];
					$alumnos[$index][$field]=$value;
				}
			}
			$estado=array();
			foreach ($alumnos as $a){
				$usuario=array();
				$usuario['login']=$a['login'];
				$usuario['nombre']=$a['nombre'];
				$usuario['apellidos']=$a['apellidos'];
				$usuario['email']=$a['email'];
				$usuario['tipo_auth']=$a['tipo_auth'];
				$usuario['password']='';
				$grupo=$a['grupo'];
				$repetidor=$this->_excel_to_boolean($a['repetidor']);
				$this->Alumno->validationErrors = array();		
			  	$result=$this->Alumno->crear($usuario,$asignatura_id,$grupo,$repetidor);
				$estado[]=am($a,array('result'=>$result));
			}
			$this->set('estado',$estado);
			$this->render('alta_excel/final');
		}
	}
	
	function _excel_to_boolean($value){
		return in_array(strtolower($value),array('y','s',1,'r'));
	}
}
