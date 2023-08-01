<?php
App::import('Vendor', 'estatus/clases/tipo_login/tipo_login');
/**
 * TipoLoginLDAP
 * LDAP Auth.
 * @see app/config/ldap.php
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */

class TipoLoginLdap extends TipoLogin
{
	function auth($login,$password,$data)
	{
		include_once APP . 'Config' . DS . 'ldap.php';
		//el campo password de la BD se usa como caché de CN
		if (preg_match('/^cn: (.*)$/',$data['Usuario']['password'],$matches))
		{
			$cn=$matches[1];
		}
		else
		{
			App::import('Vendor', '/estatus/clases/cache_ldap');
			$CacheLdap = new CacheLdap();
			$result=$CacheLdap->cache_cn(array($login));
			if (! isset($result[$login])) return false;
			$cn=$result[$login];
		}
		//aquí tenemos el cn

		$command=LDAP_CHECK_PASS;
		$command=str_replace('{LOGIN}',$login,$command);
		$command=str_replace('{PASSWORD}',escapeshellarg($password),$command);
		$command=str_replace('{CN}',$cn,$command);

		$output='';
		exec($command,$output);
		$output=join("\n",$output);
		return(strstr($output,'result: 0'));
	}
}
?>
