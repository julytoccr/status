<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	
	public $helpers = array('Formato');
    
    public $components = array(
        'Session',
        //'CustomSecurity',
        'Security',
        'Auth' => array(
			'loginAction' => array('controller' => 'usuarios', 'action' => 'login'),
            'loginRedirect' => array('controller' => 'usuarios', 'action' => 'home'),
            'logoutRedirect' => array('controller' => 'usuarios', 'action' => 'login'),
            'authenticate' => array('Custom'),
            'authorize' => 'Controller',
        )
    );
    
    function _getModelsCleanData(){
		if(! isset($this->modelClass))
			return false;
		$model = $this->modelClass;
		App::uses($model.'Form', 'Form');
		$classname = $model.'Form';
		if(! class_exists($classname))
			return false;
		$class = new ReflectionClass($classname);
		$validFields=$class->getStaticPropertyValue('validFields');
		if(isset($validFields[$this->action]))
			return $validFields[$this->action];
		else
			return false;
		/*if(isset($classname::$validFields[$this->action]))
			return $classname::$validFields[$this->action];
		else
			return false;*/
	}
    
    function _clean_data($data=null){
		if ($data===null) $data=$this->request->data;
		
		$models = $this->_getModelsCleanData();

		if(! $models )
			return;

		$result = array();
		foreach ($models as $model => $fields){
			$result[$model] = array();
			foreach ($fields as $field)
				if (isset($data[$model][$field]))
					$result[$model][$field] = $data[$model][$field];
		}
		$this->request->data = $result;
	}
    
    /*public function isAuthorized($user = null) {
		if (!isset($this->access)){
			return true;
		}
		$access = $this->access;
		$action = $this->action;
		
		$user_roles = $this->Session->read('usuario.roles');
		foreach($access as $role => $actions){
			if(! isset($user_roles[$role]))
				continue;
			if($actions == '*' or in_array($action, $actions))
				return true;
		}

		return false;
    }*/
    
    function isAuthorized(){
		$action=$this->action;
		$user_roles = $this->Session->read('usuario.roles');
		if (isset($this->access)){
			$access=$this->access;
			if(is_array($access)){
				if (isset($access[$action])){
					$act=$access[$action];
				}
				elseif (isset($access['*'])){
					$act=$access['*'];
				}
				else return true;
			 	if (isset($act) && !in_array('*',$act)){
					if($user_roles){
						if (is_array($user_roles)){
							foreach ($user_roles as $role => $v){
								if (in_array($role,$act)){
									return true;
								}
							}
							return false;
						}
					}
					else{
						return in_array('',$act);
					}
				}
				else return true;
			}
			else return	true;
		}
		else return true;
	}
    
  public function beforeFilter(){
	  if ($this->isSSLRequired()) {
		  $this->requireSSL();
		}
		
		if($this->Session->check('usuario.lenguaje_id'))
			$lang = $this->Session->read('usuario.lenguaje_id');
		else{
			$this->loadModel('ConfiguracionWeb');
			$conf = $this->ConfiguracionWeb->findByNombre('lenguaje_por_defecto');
			$lang = $conf['ConfiguracionWeb']['valor'];
		}
		Configure::write('Config.language', $lang);
		
		$this->loadRedisTranslations();
		
		if($this->_es_administrador()) {
			$this->loadModel('Lenguaje');			
			$cont_lenguajes=$this->Lenguaje->find('count');
			$this->Session->write('hay_lenguaje', $cont_lenguajes > 0 ? 1 : 0);
		}
	}
	
	private function isSSLRequired() {
    if (FORCE_SSL == 1 && !$this->_is_localhost_call("")) return true;
		
    return false;
  }
	
	private function requireSSL() {
    $this->Security->blackHoleCallback = 'blackhole';
    $this->Security->requireSecure();
  }

  public function blackhole($type) {
    if ($type == 'secure') {
        // The Security component has determined that there is an SSL method restriction failure
        $this->redirect('https://' . SERVER_NAME . $this->here);
    }
  }
	
	private function loadRedisTranslations(){
		try{
			if(Cache::read('langLoaded','redis')) return;
			$this->loadModel('Etiqueta');
			$data = $this->Etiqueta->query("select * from etiquetas etiqueta inner join textos texto on etiqueta.id = texto.etiqueta_id");
			foreach($data as $traduccion){
				Cache::write($traduccion['texto']['lenguaje_id'].'.'.$traduccion['etiqueta']['cadena'],$traduccion['texto']['texto'],'redis');
			}
			Cache::write('langLoaded','yes','redis');
		}
		catch(Exception $e){
			return;
		}
	}
    
    public function _usuario_actual(){
		return $this->Session->read('usuario.id');
	}
    public function _es_alumno(){
		$roles = $this->Session->read('usuario.roles');
		return isset($roles['alumno']);
	}
    public function _es_profesor(){
		$roles = $this->Session->read('usuario.roles');
		return isset($roles['profesor']);
	}
    public function _es_administrador(){
		$roles = $this->Session->read('usuario.roles');
		return isset($roles['admin']);
	}
	
	function _admin_actual(){
		if (!$this->_es_administrador()){
			$this->_mensaje('Error general accediendo','/',true);
		}
		return $this->_usuario_actual();
	}
	
	function _profesor_actual($value=null)
	{
		return $this->_variable_sesion('profesor.id',$value);
	}	
	
	function _alumno_actual($value=null)
	{
		return $this->_variable_sesion('alumno.id',$value);
	}

	/**
	 * Devuelve o establece la asignatura actual del alumno
	 * @param integer $value
	 * @return integer
	 */
	function _alumno_asignatura_actual($id=null, $name='')
	{
		if($id!=null)
			$this->_variable_sesion('alumno.asignatura.name', $name);
		return $this->_variable_sesion('alumno.asignatura.id',$id);
	}

	/**
	 * Devuelve o establece la asignatura actual del profesor
	 * @param integer $value
	 * @return integer
	 */
	function _profesor_asignatura_actual($id=null, $name='')
	{
		if($id!=null)
			$this->_variable_sesion('profesor.asignatura.name', $name);
		return $this->_variable_sesion('profesor.asignatura.id',$id);
	}

	/**
	 * Devuelve o establece la asignatura actual del administrador
	 * @param integer $value
	 * @return integer
	 */
	function _admin_asignatura_actual($id=null, $name='')
	{
		if($id!=null)
			$this->_variable_sesion('admin.asignatura.name', $name);
		return $this->_variable_sesion('admin.asignatura.id',$id);
	}


	/**
	 * Lee o escribe una variable de la sesión.
	 * Para leer, $value debe ser null, y la variable debe
	 * existir obligatoriamente en la sesión, de lo contrario
	 * se producirá un error fatal.
	 *
	 * @param string $name Nombre de la variable
	 * @param mixed $value Si null, se trata de una lectura. Si no es null, entonces es el valor de la variable a escribir.
	 * @return string Valor de la variable (en caso de lectura)
	 */
	function _variable_sesion($name,$value=null)
	{
		if ($value!==null)
		{
			$this->Session->write($name,$value);
		}
		else
		{
			if ($this->Session->check($name))
			{
				return $this->Session->read($name);
			}
			else
			{
				$this->log("Se intentó acceder a la variable inexistente en la sesión: $name");
				$this->_mensaje('Error general accediendo','/',true);
			}
		}
	}

	/**
	 * Muestra un mensaje redirigiendo a la url
	 * @param string $mensaje Mensaje a mostrar
	 * @param string $url URL destino. Si es nula, redirige a la acción actual sin parámetros
	 * @param boolean $error El mensaje es de tipo error
	 */
	function _mensaje($mensaje,$url=null,$error=false)
	{
			if ($url===null) $url='/' . Inflector::underscore($this->name) . '/' . $this->action;

           	if ($error)
           	{
           		$element='flash_error';
           	}
           	else
           	{
           		$element='flash_info';
           	}
           	$this->Session->setFlash($mensaje,$element);
           	if($this->request->isAjax())
				die(sprintf('<script>window.location = "%s";</script>', $url));
			else
				$this->redirect($url);
	}
	
	function ajax_redirect($url){
		die(sprintf('<script>window.location = "%s";</script>', $url));
	}
  
  function _is_localhost_call($extra_ips)
  {
    $ips=am($extra_ips,array('127.0.0.1'));
    return (in_array($_SERVER['REMOTE_ADDR'],$ips));
  }
}
