<?php
App::import('Vendor', 'estatus/clases/tipo_login/tipo_login');
/**
 * TipoLoginPlain
 * Texto plano en la BD
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class TipoLoginPlain extends TipoLogin
{
	function auth($login,$password,$data){
		return (strcasecmp($password,$data['Usuario']['password'])===0);
	}
}
?>
