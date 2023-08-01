<?php
vendor('/estatus/clases/tipo_login/tipo_login');

/**
 * TipoLoginMd5
 * Texto encriptado en la BD
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class TipoLoginMd5 extends TipoLogin
{
	function auth($login,$password,$data)
	{
		return(md5($password)==$data['Usuario']['password']);
	}
}
?>