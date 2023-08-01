<?php
/**
 * Cache LDAP
 *
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */
class CacheLdap extends Object
{
	/**
	 * Número máximo de usuarios a consultar en una sola query
	 */
	var $tam_consultas=5;

	/**
	 * Cachea el CN de los usuarios pasados.
	 * Guarda el CN en el campo correspondiente de la tabla usuarios y
	 * devuelve un array con los CN guardados correctamente.
	 *
	 * @param array Array de logins de usuario
	 * @return array Array asociativo login=>CN
	 */
	function cache_cn($array_usuarios)
	{
		include_once APP . 'Config' . DS . 'ldap.php';

		for($i=0;$i<count($array_usuarios);$i++)
		{
			$array_usuarios[$i]=strtolower($array_usuarios[$i]);
		}
		$result=array();
		for ($i=0;$i<count($array_usuarios);$i+=$this->tam_consultas)
		{
			//creamos un array con el intervalo de usuarios actual
			$last_idx=min($i+$this->tam_consultas,count($array_usuarios));

			$subarray=array_slice($array_usuarios,$i, $last_idx - $i);
			$result=am($result,$this->_cache_cn($subarray));
		}
		return $result;
	}

	/**
	 * Función auxiliar, trabaja sobre una porción del array original
	 */
	function _cache_cn($array_usuarios)
	{
		if (! $array_usuarios) return array();

		$res=array();

		if ((! LDAP_FIELD_LOGIN) || LDAP_FIELD_LOGIN == 'cn' || LDAP_FIELD_LOGIN == 'CN') {
			foreach ($array_usuarios as $usuario) {
				$res[$usuario] = $usuario;
			}
		}
                else {
			$command=LDAP_GET_CN;

			//hay que construir la query anidada, arbol prefijo,operación OR de 2
			//ejemplo: |(|(DNIpassport=12345678S)(DNIpassport=33333333K))(DNIpassport=22222222Z))
			$filter=LDAP_FIELD_LOGIN . "={$array_usuarios[0]}";
			$filter="|($filter)(cn={$array_usuarios[0]})";
			for ($i=1;$i<count($array_usuarios);$i++)
			{
				$filter="|($filter)(". LDAP_FIELD_LOGIN . "={$array_usuarios[$i]})";
				$filter="|($filter)(cn={$array_usuarios[$i]})";
			}

			$command=str_replace('{FILTER}',$filter,$command);
			$command=str_replace('{FIELDS}',"cn " . LDAP_FIELD_LOGIN,$command);

			$output='';
			exec($command,$output);
			$output=join($output,"\n");
			$arr_results=split("\n\n",$output);

		
			for ($i=0;$i<count($arr_results);$i++)
			{
				if (preg_match('/cn: ([^\s]*)/',$arr_results[$i],$matches))
				{
					$cn=low($matches[1]);
					if (in_array($cn,$array_usuarios))
					{
						$res[$cn]=$cn;
					}
					elseif (preg_match('/' . LDAP_FIELD_LOGIN . ': ([^\s]*)/',$arr_results[$i],$matches))
					{
						$login=low($matches[1]);
						if (in_array($login,$array_usuarios))
						{
							$res[$login]=$cn;
						}
					}
				}
			}
		}

		foreach ($res as $login=>$cn)
		{
			if (!$this->_guardar_cn($login,$cn))
			{
				unset($res[$login]);//ha habido error guardando
			}
		}
		return $res;
	}

	/**
	 * Cachear el cn, se guarda en el campo password del usuario
	 * @param string $login Login del usuario
	 * @param string $cn CN a guardar
	 * @return boolean Éxito
	 */
	function _guardar_cn($login,$cn)
	{
		$Usuario = new Usuario;
		$data=$Usuario->findByLogin($login);
		if ($data)
		{
			$data['Usuario']['password']="cn: $cn";
			return $Usuario->save($data['Usuario'],false);
		}
		return false;
	}
}
?>
