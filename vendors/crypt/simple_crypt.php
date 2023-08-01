<?php
/**
 * SimpleCrypt Class
 * Simple encryption of strings
 *
 * Usage:
 * $this->SimpleCrypt=& new SimpleCrypt();
 * $encrypted=$this->SimpleCrypt->encrypt('a_key','the_text');
 * $plain=$this->SimpleCrypt->decrypt('a_key',$encrypted);
 *
 * The result is $plain equals to 'the_text'
 *
 * You can use a different engine if you don't have Mcrypt:
 * $this->SimpleCrypt=& new SimpleCrypt('Simple');
 *
 * A string encrypted with one engine can't be decrypted with
 * a different one even if the key is the same.
 *
 * @author RosSoft
 * @version 0.2
 * @license MIT
 */

class SimpleCrypt extends Object
{
	/**
	 * Constructor
	 * @param string $engine_name Engine for encryption. Values: Simple, Mcrypt, Auto
	 */
	function __construct($engine_name='Auto')
	{
		if ($engine_name=='Auto')
		{
			if (function_exists('mcrypt_module_open'))
			{
				$engine_name='Mcrypt';
			}
			else
			{
				$engine_name='Simple';
			}
		}
		$engine_name='SimpleCrypt' . $engine_name . 'Engine';
		$this->_engine=new $engine_name;
		parent::__construct();
	}

	/**
	 * Encrypts the string with the given key
	 * @param string $key
	 * @param string $string Plaintext string
	 * @return string Ciphered string
	 */
	function encrypt($key, $string)
	{
		return $this->_engine->encrypt($key,$string);
	}


	/**
	 * Decrypts the string by the given key
	 * @param string $key
	 * @param string $string Ciphered string
	 * @return string Plaintext string
	 */
	function decrypt($key, $string)
	{
		return $this->_engine->decrypt($key,$string);
	}
}

/**
 * Simple Engine doesn't need any PHP extension.
 * Every encryption of the same string with the same key
 * will return the same encrypted string
 */
class SimpleCryptSimpleEngine extends Object
{
	function encrypt($key, $string)
	{
		$result = '';
		for($i=1; $i<=strlen($string); $i++)
		{
			$char = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		return $result;
	}

	function decrypt($key, $string)
	{
		$result = '';
		for($i=1; $i<=strlen($string); $i++)
		{
			$char = substr($string, $i-1, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
		return $result;
	}
}


/**
 * McryptEngine requires Mcrypt extension
 * Every encryption of the same string with the same key
 * will return a different encrypted string.
 */
class SimpleCryptMcryptEngine extends Object
{
	var $alg=MCRYPT_BLOWFISH;

	function encrypt($key, $string)
	{
		$td = mcrypt_module_open ($this->alg, '', 'ecb', '');
	    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td),MCRYPT_DEV_RANDOM);
    	$encrypted_data = mcrypt_ecb($this->alg, $key,$string,MCRYPT_ENCRYPT,$iv);
    	return $iv . $encrypted_data;
	}

	function decrypt($key, $string)
	{
		$td = mcrypt_module_open ($this->alg, '', 'ecb', '');
	    $iv = substr($string,0,mcrypt_enc_get_iv_size($td));
	    $string=substr($string,mcrypt_enc_get_iv_size($td));
    	return mcrypt_ecb($this->alg, $key,$string,MCRYPT_DECRYPT,$iv);
	}
}
?>