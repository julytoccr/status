<?php
App::import('Vendor', 'estatus/clases/tipo_login/tipo_login');
/**
 * TipoLoginFib
 * Alumno de la FIB.
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class TipoLoginFib extends TipoLogin{
	
	function auth($login,$password,$data){
		$url='https://raco.fib.upc.edu/login';
		App::import('Vendor', 'http_client/http_client');
		$client = new HttpClient();
		$client->user=$login;
		$client->password=$password;
		pr($client->get($url));
		return ($client->response_code() == 200);
	}
}
?>
