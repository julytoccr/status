<?php
class UsuarioForm {
	public static $validFields = array(
		'login' => array(
			'Usuario' => array('login', 'password')
		),
		'add' => array(
			'Usuario' => array('login', 'nombre', 'apellidos', 'email', 'tipo_auth')
		),
		'edit' => array(
			'Usuario' => array('login', 'nombre', 'apellidos', 'email', 'tipo_auth')
		),
		'change_password' => array(
			'Usuario' => array('password','new_password','repeat_password')
		),
		'login' => array(
			'Usuario' => array('login','password')
		),
		'seleccionar_lenguaje' => array(
			'Usuario' => array('lenguaje')
		),
		'notificar_error_traduccion' => array(
			'CorreccionTextos' => array('texto_actual','texto_correccion','notas_adicionales','lenguaje','url','mail','fecha','login')
		)
	);
	
    public static function validation() {
        return array(
			'login' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'nombre' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'apellidos' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'email' => array(
				'ruleName' => array(
					'rule' => 'notEmpty',
					'required' => true,
					'message' => __('form_obligatorio'),
				),
				'ruleName2' => array(
					'rule' => 'email',
					'message' => __('form_email'),
				)
			),
		);
    }
    
    public static function change_password() {
        return array(
			'password' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'new_password' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'repeat_password' => array(
				'ruleName' => array(
					'rule' => 'notEmpty',
					'required' => true,
					'message' => __('form_obligatorio'),
				),
				'ruleName2' => array(
					'rule' => array('identicalFieldValues', 'new_password'),
					'message' => __('form_passwords_no_coinciden'),
				)
			),
		);
    }
    
    public static function login() {
        return array(
			'login' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
			'password' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'message' => __('form_obligatorio'),
			),
		);
    }
    
    public static function tipo_auth(){
		$plain = array('fib','Alumne FIB');
		$ldap = array('ldap','LDAP');
		$fib = array('plain','Password text plà');
		return array($plain,$ldap,$fib);
	}
}
?>
