<?php
App::uses('AppController', 'Controller');

class UsuariosController extends AppController {
	public $uses = array('Usuario', 'Lenguaje','ConfiguracionWeb','CorreccionTextos', 'Alumno','HistorialLogin');
	
	var $access = array
	(
	'home' => array('admin','profesor','alumno'),
	'change_password' => array('admin','profesor','alumno')
	);
	
    public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_usuarios'));
        $this->Auth->allow('login');
    }

	public function login() {
		$this->set('title_for_layout','Authentication');
		$this->_clean_data();
		if ($this->request->is('post')) {
			$this->Usuario->setValidation('login');
			$this->Usuario->set($this->request->data);

			if (!$this->Usuario->validates()) return;
			
			$login = $this->request->data["Usuario"]["login"];
			$previous_login_failed = false;
			try {
				$tries = Cache::read($login, 'redis_login');
				if ($tries !== false) {
					$previous_login_failed = true;
					$seconds = $tries;
					if ($seconds > 30) {
						$seconds = 30;
					}
					sleep($seconds);
				}
			} catch (Exception $e) {
			}
			
			if ($this->Auth->login()) {
				try {
					Cache::delete($login, 'redis_login');
				} catch (Exception $e) {
				}
				
				$this->Usuario->recursive = 1;
				$user = $this->Usuario->findByLogin($this->request->data['Usuario']['login']);
				$id = $user['Usuario']['id'];
				
				/* Roles */
				if(! is_null($user['Administrador']['id']))
					$this->Session->write('usuario.roles.admin', true);
				if(! is_null($user['Profesor']['id'])){
					$this->Session->write('usuario.roles.profesor', true);
					$this->_profesor_actual($user['Profesor']['id']);
				}
				$this->Usuario->Alumno->recursive = -1;
				if( $this->Usuario->Alumno->find('count', array('conditions'=>array('Alumno.usuario_id'=>$id))) > 0){
					$this->Session->write('usuario.roles.alumno', true);
					
					$asignaturas = $this->Alumno->asignaturas($id,true);
						
					if (count($asignaturas)==1){
						$this->_alumno_asignatura_actual($asignaturas[0]['id'],$asignaturas[0]['nombre']);
						$alumno_id=$this->Alumno->buscar($id, $asignaturas[0]['id']);
						$this->_alumno_actual($alumno_id);
					}
				}

				/* ID */
				$this->Session->write('usuario.id', $user['Usuario']['id']);
				$this->Session->write('usuario.login', $user['Usuario']['login']);
				
				$ip=$this->request->clientIp();
				$this->HistorialLogin->crear($id,$ip);
				
				/* Lenguaje */
				$cont_lenguajes = $this->Lenguaje->find('count');
				if ($cont_lenguajes>0) {
					$lenguaje_id = $user['Usuario']['lenguaje'];

					if ($lenguaje_id == 0){
						$lenguaje_id = $this->ConfiguracionWeb->lenguaje_por_defecto();
					}

					$lenguaje = $this->Lenguaje->lenguaje_estandard($lenguaje_id);

					$this->Session->write('usuario.lenguaje_id', $lenguaje_id);
					$this->Session->write('usuario.lenguaje', $lenguaje);
				} else {
					$this->Session->write('usuario.lenguaje', 'NOLANG');
					$this->Session->write('usuario.lenguaje_id', '0');
				}

				return $this->redirect($this->Auth->redirect());
			} else {
				if ($previous_login_failed) {
					try {
						Cache::increment($login, 1, 'redis_login');
					} catch (Exception $e) {
					}
				}
				else {
					try {
						Cache::write($login, 1, 'redis_login');
					} catch (Exception $e) {
					}
				}
				$this->_mensaje(__('error_usuario_no_existe'),'/usuarios/login',true);
			}
		}
	}

	public function logout() {
		$this->Session->destroy();
		$this->redirect($this->Auth->logout());
	}
	
	function home(){
		$this->set('title_for_layout',__('titulo_pagina_principal'));
		if($this->_es_alumno() && $this->Session->check('alumno.asignatura.id')){
			$asignatura_id = $this->_alumno_asignatura_actual();
			$alumno_id=$this->_alumno_actual();
			$alumno_problemas = $this->Alumno->problemas($alumno_id, $asignatura_id);
			$this->set(compact('alumno_problemas'));
		}
	}
	
	function change_password()
	{
		$this->set('title_for_layout',__('cambiar_password'));
        if (!$this->request->is('post')) return;
		$this->_clean_data();
		$this->Usuario->setValidation('change_password');
		$this->Usuario->set($this->request->data);
       	if (!$this->Usuario->validates()) return;

		$this->request->data['Usuario']['login'] = $this->Session->read('usuario.login');
		
		if(!$this->Auth->identify($this->request,$this->response))
		{
			$this->_mensaje(__('error_password'),'/usuarios/change_password',true);
		}
		
		$user = $this->Usuario->findByLogin($this->Session->read('usuario.login'));
		$user['Usuario']['password'] = $this->request->data['Usuario']['new_password'];
		
		if ($this->Usuario->save($user)){
			$this->_mensaje(__('mens_password_cambiado'),'/');
		}else{
			$this->_mensaje(__('error_password_cambiado'),'/usuarios/change_password',true);
		}
	}
	
	function seleccionar_lenguaje(){
		if (!$this->request->is('post')){
			$this->Lenguaje->recursive=-1;
			$data = $this->Lenguaje->find('all', array('fields' => array('Lenguaje.id', 'Lenguaje.lenguaje_propio', 'Lenguaje.lenguaje_estandard')));
			$lenguajes=array();
			foreach ($data as $leng) $lenguajes[$leng['Lenguaje']['id']]=$leng['Lenguaje']['lenguaje_propio'].' ('.$leng['Lenguaje']['lenguaje_estandard'].')';
			$this->set('lenguajes',$lenguajes);
		}
		else{
			$this->_clean_data();
			$data = $this->request->data;
			$lenguaje_id=$data['Usuario']['lenguaje'];
			$this->Usuario->id = $this->_usuario_actual();
			if($this->Usuario->saveField('lenguaje',$lenguaje_id)){
				$this->Session->write('usuario.lenguaje_id', $lenguaje_id);
				$this->Session->write('usuario.lenguaje', $this->Lenguaje->lenguaje_estandard($lenguaje_id));
				$this->_mensaje(__('mens_lenguaje_seleccion'),'/');
			}
		}
	}
	
	function notificar_error_traduccion()
	{
		if (!$this->request->is('post'))
		{
			$lenguaje_actual=$this->Session->read('usuario.lenguaje');
			$url_anterior=$_SERVER['HTTP_REFERER'];
			$fecha= date('j/n/Y H:i');
			$usuario_id=$this->_usuario_actual();
			$usuario=$this->Usuario->findById($usuario_id);
			$mail=$usuario['Usuario']['email'];
			$login=$usuario['Usuario']['login'];
			$this->set('lenguaje_actual',$lenguaje_actual);
			$this->set('url_anterior',$url_anterior);
			$this->set('fecha',$fecha);
			$this->set('mail',$mail);
			$this->set('login',$login);
		}
		else
		{
			$this->_clean_data();
			$data = $this->request->data;
			$data['CorreccionTextos']['estado'] = 'No revisada';
			$this->CorreccionTextos->save($data);
			$this->_mensaje(__('mens_notificacion_insertada'),'/');
		}
	}
	
	function cache_ldap(){
		$this->autoRender=false;
		App::import('Vendor', '/estatus/clases/cache_ldap');
		$CacheLdap = new CacheLdap();
		$this->Usuario->recursive=-1;
		$usuarios=$this->Usuario->find('all',array('conditions'=>array('Usuario.password NOT LIKE "cn: %"',
													'Usuario.tipo_auth'=>'ldap')));
		$logins=array();
		foreach ($usuarios as $u) $logins[]=$u['Usuario']['login'];
		if ($logins){
			$result=$CacheLdap->cache_cn($logins);
			foreach ($logins as $login){
				if (isset($result[$login])){
					echo "OK: $login - " . $result[$login] . '<br/>';
				}
				else{
					echo "ERROR: $login<br/>";
				}
			}
		}
		else{
			echo 'No logins ldap sin CN';
		}
	}
}
