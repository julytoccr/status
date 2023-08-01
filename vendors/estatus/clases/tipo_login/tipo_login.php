<?php
/**
 * TipoLogin
 * Clase base para los tipos de logins: FIB, Password plano...
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class TipoLogin extends Object{
	
	function auth($login,$password,$data){
		return false;
	}

}
?>
