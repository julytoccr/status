<?php
/**
 * Usuario Model
 * @package models
 */
App::uses('UsuarioForm', 'Form');
class Usuario extends AppModel
{
	var $name = 'Usuario';
	public $actsAs = array('Containable');
	public $tipo_auth;
	
	var $hasOne = array(
			'Profesor' =>
			 array('className' => 'Profesor',
					'foreignKey' => 'id'),
			'Administrador' =>
			 array('className' => 'Administrador',
					'foreignKey' => 'id')

	);
	var $hasMany = array(
			'Alumno'
	);
	
	function __construct(){
		parent::__construct();
		$this->tipo_auth = UsuarioForm::tipo_auth();
		$this->validate = UsuarioForm::validation();
	}
	
	function setValidation($action){
		$this->validate = UsuarioForm::$action();
	}
	
	function identicalFieldValues($field=array(), $compare_field=null ){ 
        foreach( $field as $key => $value ){ 
            $v1 = $value;
            $v2 = $this->data[$this->name][ $compare_field ];                  
            if($v1 !== $v2) { 
                return false; 
            } else { 
                continue; 
            } 
        } 
        return true; 
    } 

	/**
	 * @param integer $id Id del alumno. Si nulo entonces se usa el actual
	 * @return bool El usuario es un alumno
	 */
	function es_alumno($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->Alumno->contain();
		return $this->Alumno->hasAny(array('Alumno.usuario_id'=>$id));
	}

	/**
	 * @param integer $id Id del alumno. Si nulo entonces se usa el actual
	 * @return bool El usuario es un profesor
	 */
	function es_profesor($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->Profesor->contain();
		return $this->Profesor->hasAny(array('Profesor.id'=>$id));
	}

	/**
	 * @param integer $id Id del alumno. Si nulo entonces se usa el actual
	 * @return bool El usuario es un administrador
	 */
	function es_administrador($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->Administrador->contain();
		return $this->Administrador->hasAny(array('Administrador.id'=>$id));
	}

	/**
	 * Devuelve un array con los roles del usuario
	 * @param integer $id Usuario
	 * @return array roles
	 * 	$result=array('alumno','profesor','admin')
	 */
	function roles($id=null)
	{
		if ($id===null) $id=$this->id;
		$roles=array();
		if ($this->es_alumno($id))
		{
			$roles[]='alumno';
		}
		if ($this->es_profesor($id))
		{
			$roles[]='profesor';
		}
		if ($this->es_administrador($id))
		{
			$roles[]='admin';
		}
		return $roles;
	}

	/**
	 * Autentifica un usuario
	 *
	 * @param string $login
	 * @param string $password
	 *
	 * @return mixed Si el login es correcto, retorna el $data. De lo contrario false
	 * 		$data['Usuario']['id']
	 */
	function auth($login,$password,$token=null){
		$this->recursive=-1;
		$data=$this->findByLogin($login);
		if (!$data) return false;
		$type = $data['Usuario']['tipo_auth'];
		App::import('Vendor', 'estatus/clases/tipo_login/tipo_login_'.$type);
		$classname = 'TipoLogin' . Inflector::camelize($type);
		$auth = new $classname;
		return $auth->auth($login,$password,$data) ? $data : false;
	}

	/**
	 * Devuelve un password aleatorio alfanumérico en minúsculas
	 *
	 * @return string Password
	 */
	function random_password(){
		return strtolower($this->randomPassword(8));
	}
	
	function randomPassword($length, $available_chars = 'ABDEFHKMNPRTWXYABDEFHKMNPRTWXY23456789') {
		$chars = preg_split('//', $available_chars, -1, PREG_SPLIT_NO_EMPTY);
		$char_count = count($chars);
		$out = '';
		for($ii = 0; $ii < $length; $ii++) {
			$out .= $chars[rand(1, $char_count)-1];
		}
		return $out;
	}


	/**
	 * Busca un usuario
	 *
	 * @param string $login
	 * @return integer $usuario_id si encontrado, null si no existe
	 */
    function buscar($login)
    {
    	$this->recursive=-1;
    	$data=$this->findByLogin($login);
    	if (!$data) return null;
    	return $data['Usuario']['id'];
    }

	/**
	 * Envía el password al usuario
	 *
	 * @param integer $id Usuario
	 */
    function enviar_password($id, $asignatura = null){
    	$Tarea = ClassRegistry::init('Tarea');
        if ($asignatura == null)
			$Tarea->crear('/mail/enviar_password_usuario/' . $id,1);
        else
			$Tarea->crear('/mail/enviar_password_usuario/' . $id . "/" . $asignatura,1);
    }

	/**
	 * Programa una tarea de caches de LDAP
	 */
    function _cache_ldap(){
    	include_once APP . 'Config' . DS . 'ldap.php';
		if (! LDAP_FIELD_LOGIN) return; //no hay que hacer nada si el login es el CN
		$Tarea = ClassRegistry::init('Tarea');
    	$Tarea->crear('/usuarios/cache_ldap/',0);
    }

	/**
	 * Crea o modifica un usuario. Se identifica por login
	 *
	 * @param array $data Datos del usuario ($data['login'])
	 * @return integer Si se ha guardado correctamente, devuelve $usuario_id, de lo contrario null
	 */
	function guardar($data, $asignatura = null)
	{
		$this->recursive=-1;
		$user=$this->findByLogin($data['login']);

		$mandar_password=false;
		if ($data['tipo_auth']=='plain')
		{
			if ((!isset($data['password']) || !$data['password']) && (( $user && $user['Usuario']['tipo_auth'] != 'plain') ||
			                            !$user)) {
				// no s'ha especificat una contrassenya en concret i:
				// - l'usuari ja existeix i s'està canviant el mètode d'autenticació -> assignar password
				// - o bé l'usuari no existeix
				$data['password'] = $this->random_password(); // Establecer un password aleatorio
				$mandar_password = true;
			}
			else if ($data['password'] == '' && $user) {
				$data['password'] = $user['Usuario']['password'];
			}
		}
		elseif ($data['tipo_auth']=='ldap')
		{
			$data['password'] = "";
			//Que haga una cache de passwords LDAP nocturna
			$this->_cache_ldap();
		}
		
		if (!$user)
		{	//crear
			$this->create();
			if ($this->save($data))
			{
				$id=$this->getLastInsertID();
				if ($mandar_password) $this->enviar_password($id, $asignatura);
				return $id;
			}
		}
		else
		{	//modificar
			$data['id']=$user['Usuario']['id'];
			if ($this->save($data))
			{
				if ($mandar_password) $this->enviar_password($data['id']);
				return $data['id'];
			}
		}
		return null;
	}


	function eliminar_lenguaje_seleccionado($lenguaje){
		$this->recursive=-1;
		$usuarios=$this->findByLenguaje($lenguaje);

		if(is_array($usuarios)){
			foreach ($usuarios as $us){
				$data=array('id'=> $us['id'], 'lenguaje'=> 0);
				$this->save($data);
			}
		}
	}

}
?>
