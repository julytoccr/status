<?php
/**
 * RConexion
 * Interfaz Rserve PHP
 *
 * @author David Rodriguez
 *
 * @package estatus
 *
 * @link http://rosuda.org/Rserve/dev.shtml
 */

	// Comandos de RServe
	define('CMD_LOGIN',0x0001);
	define('CMD_VOID_EVAL',0x0002);
	define('CMD_EVAL',0x0003);
	define('CMD_SHUTDOWN',0x0004);

	define('CMD_OPEN_FILE',0x0010);
	define('CMD_CREATE_FILE',0x0011);
	define('CMD_CLOSE_FILE',0x0012);
	define('CMD_READ_FILE',0x0013);
	define('CMD_WRITE_FILE',0x0014);
	define('CMD_REMOVE_FILE',0x0015);

	define('CMD_SET_SEXP',0x0020);
	define('CMD_ASSIGN_SEXP',0x0021);

	// Tipos de Datos de Rserve
	define('DT_INT',1);
	define('DT_CHAR',2);
	define('DT_DOUBLE',3);
	define('DT_STRING',4);
	define('DT_BYTESTREAM',5);

	define('DT_SEXP',10);
	define('DT_ARRAY',11);

	//Tipos de Datos de las REXP
	define('XT_NULL',0); 							//?
	define('XT_INT',1);							//OK
  	define('XT_DOUBLE',2);						//OK
  	define('XT_STR',3);							//OK
	define('XT_LANG',4);						//OK (equivalente a XT_LIST)
  	define('XT_SYM',5);							//OK?
  	define('XT_BOOL',6);							//OK
  	define('XT_VECTOR',16);						//OK
  	define('XT_LIST',17);						//OK
  	define('XT_CLOS',18);
        define('XT_SYMNAME',19);                                        //Nou, abans XT_SYM + XT_STR
        define('XT_LIST_TAG', 21);                                    //Nou, abans XT_LIST
  	define('XT_ARRAY_INT',32);					//OK
	define('XT_ARRAY_DOUBLE',33);					//OK
  	define('XT_ARRAY_STR',34);
	define('XT_ARRAY_BOOL_UA',35);				//Uso Interno no deberia aparecer
  	define('XT_ARRAY_BOOL',36);					//OK
  	define('XT_UNKNOWN',48);
  	define('XT_ERROR',60); 						//Uso Interno de estas Clases, no se utiliza en Rserve
  	define('XT_FACTOR',127);

// Clase que crea una conexion con el servidor de Rserve para poder evaluar instrucciones de R
class RConexion
{
	var $error;
	var $sock;
	var $conectado = false;
	var $time_r = 0;
	var $inst_r = "";

	function conexion($ip="127.0.0.1",$port=6311){

		// conexion a Rserve en $ip:$port
		if($this->conectado) fclose($this->sock);
		$this->sock = @fsockopen($ip,$port,$errno,$errstr,0);
		if (!$this->sock) die('Imposible conectar a R');

		$this->conectado = true;

		//Lectura inicial del socket y la unica no bloqueante
  		$bufer = fread($this->sock, 1024);
  		socket_set_blocking($this->sock, true);

  		//comprobamos la version d Rserve...

	}

	function evaluarVoid($inst){

		// Evalua un string pero no devuelve el resultado
		return $this->evaluar($inst, CMD_VOID_EVAL);

	}

	function evaluar($inst, $cmd=CMD_EVAL){
		//echo ($inst);
		// Funcion que evalua un String y que por defecto devuelve el resultado
		$respuesta[] = array();
		
		/*if (substr($inst, 0, 7) == "source(") {
			$pos = strpos($inst, "\"", 10);
			$this -> inst_r .= "\n" . $inst;//file_get_contents(substr($inst, 8, strlen($inst) - 8 - 13), false, NULL, 0);
			copy(substr($inst, 8, strlen($inst) - 8 - 13), substr($inst, 8, strlen($inst) - 8 - 13) . "_");
		} else {
			$this -> inst_r .= "\n" . $inst;
		}*/

		$header_format =
    		'CTipo/'. 	 	 	# Tipo d la respuesta (1 byte)
			'x1/'.				# En Blanco
			'CError/'.			# Codigo de ERROR (1 byte)
			'x1/'.				# En Blanco
    		'LLongitud/'. 		  	# Longitud (32 bits)
    		'x8';				# En Blanco
		$longitud = strlen($inst);
		//montamos y enviamos la cabecera eval; longitud+4; 0; 0;
		$l = $longitud+4;
		$buffer = pack('V', $cmd).pack('V', $l).pack('V',0).pack('V',0);
		fwrite($this->sock, $buffer);

		//enviamos el string
		$l='a'.$longitud;
		$buffer = pack('C', DT_STRING).pack('S',$longitud).pack('C',0).pack($l,$inst);
		fwrite($this->sock, $buffer);
		$t1 = microtime(true);

		//leemos la cabecera de la respuesta de RServe
		$buffer = fread($this->sock, 16);
		$this->time_r += (microtime(true) - $t1);
		// Desempaquetamos la cabecera
		$header = unpack($header_format, $buffer);
		$total = $header['Longitud'];

		// Si hay atributos los leemos
		if($header['Tipo']==0x01){
			// Operacion correcta, si hay atributos los leemos;
			if($total > 0){
				$respuesta = $this->leerSEXP();
				$rp = $respuesta['resp'];
				return $rp;
			}else{
				return new RespuestaR(0, 0);
			}
		}else if($header['Tipo']==0x02){
			// Operacion incorrecta, devolvemos el error
			return new RespuestaR(XT_ERROR, $header['Error']);
		}

	}

	function leerDatos($longitud){
		// Funcion utilizada para leer datos mas grandes que el tamaÃƒÆ’Ã‚Â¯Ãƒâ€šÃ‚Â¿Ãƒâ€šÃ‚Â½o maximo del buffer del socket
		$buf = '';
		while($longitud>1000){
			$buf .= fread($this->sock, 1000);
			$longitud -= 1000;
		}
		$buf .= fread($this->sock, $longitud);
		return $buf;

	}

	function leerSEXP(){
		//$header = unpack ($header_format, $buffer);
		$leido = 0;
		$datos = 0;
		$attr="";

		$respuesta[] = array();
		$respuesta['resp'] = new RespuestaR(XT_STR, "");

		$buffer = fread($this->sock, 4);

		//leemos la cabecera
		$header_datos = unpack('CTipo/SLongitud/CLongUP', $buffer);

		//$header_datos = unpack('CTipo/H3Longitud', $buffer);
		$tipo = $header_datos['Tipo'];
		$longitud = (integer)$header_datos['Longitud']|(((integer)$header_datos['LongUP']<<16));
		//$longitud = (long)$header_datos['Longitud'];

		if($tipo>128)
		{
			$resp = $this->leerSEXP();
			$attrlong= $resp['leido'];
			$attr = $resp['resp'];
			$tipo -= 128;
			$tieneAttr = true;
		}
		else
		{
			$tieneAttr=false;
			$attrlong=0;
			$attr=array();
		}
		$longitud_restante=$longitud - $attrlong;

		if($longitud_restante > 0)
		{
			switch($tipo){

				case DT_SEXP:
					$respuesta = $this->leerSEXP();
					continue 1;

				case XT_INT:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido']=$longitud+4;
					$resp = unpack("d*", $buffer_dato);
					$respuesta['resp'] = new RespuestaR($tipo,$resp[0],$attr);
					continue 1;

				case XT_DOUBLE:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido']=$longitud+4;
					$resp = unpack("d*", $buffer_dato);
					$respuesta['resp'] = new RespuestaR($tipo,$resp[0],$attr);
					continue 1;

                                case XT_SYMNAME:
				case XT_STR:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido'] = $longitud+4;
					$i=0;
					$resp ="";
					while(($buffer_dato[$i]!=chr(0))&&($i<$longitud_restante)){
						$resp .= $buffer_dato[$i];
						$i = $i+1;
					}
					$respuesta['resp'] = new RespuestaR($tipo,$resp,$attr);
					continue 1;

				case XT_BOOL:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido'] = $longitud+4;
					$respuesta['resp'] = new RespuestaR($tipo, unpack("C/x3", $buffer_dato),$attr);
					continue 1;

				case XT_VECTOR:
					$i = 0;
					$respuesta['leido'] = $longitud+4;
					$resp = array();
					while($longitud_restante>0){
						$rs = $this->leerSEXP();
						$longitud_restante = $longitud_restante - $rs['leido'];
						$resp[$i] = $rs['resp'];
						$i=$i+1;
					}
					$respuesta['resp']=new RespuestaR($tipo, $resp, $attr);
					continue 1;

				case XT_LIST:
				case XT_LANG:
					//XT_LIST data: SEXP head, SEXP vals, [SEXP tag]
					$head = $this->leerSEXP();
					$body = $this->leerSEXP();
					$respuesta['leido'] = $head['leido']+$body['leido'];
					$resp['head'] = $head['resp'];
					$resp['body'] = $body['resp'];

					if($respuesta['leido']<$longitud_restante){
						$tag = $this->leerSEXP();
						$respuesta['leido']+=$tag['leido'];
						$resp['tag'] = $tag['resp'];
					}
					if($tieneAttr){
						$resp['attr']=$attr;
						$respuesta['leido']+=$attrlong;
						$tieneAttr = false;
					}
					$respuesta['leido']+=4;
					$respuesta['resp'] = new RespuestaR($tipo, $resp,$attr);
					continue 1;

				case XT_ARRAY_STR:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido'] = $longitud+4;
					$i=0;
					$resp ="";
					while(($buffer_dato[$i]!=chr(0))&&($i<$longitud_restante)){
					  $resp .= $buffer_dato[$i];
					  $i = $i+1;
					}

                                        // ens saltem bytes enviats que no sÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n lletres o nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âºmeros
                                        while(($i<$longitud_restante)&&($buffer_dato[$i]<chr(8))) ++$i;

                                        // d'acord, no ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©s un sol string (aixÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â² a versions antigues de R no passava), farem un array
                                        if ($i < $longitud_restante) {
                                          $aux = $resp;
                                          unset($resp);
                                          $resp[1] = $aux;
                                          $k = 1;
                                          while($i < $longitud_restante) {
                                            $resp[++$k] = "";
					    while(($buffer_dato[$i]!=chr(0))&&($i<$longitud_restante)){
						$resp[$k] .= $buffer_dato[$i];
						$i = $i+1;
					    }

                                            // ens saltem bytes enviats que no sÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³n lletres o nÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Âºmeros
                                            while(($i<$longitud_restante)&&($buffer_dato[$i]<chr(8))) ++$i;
                                          }
                                        }
                                        
					$respuesta['resp'] = new RespuestaR($tipo,$resp,$attr);
					continue 1;

				case XT_ARRAY_INT:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido'] = $longitud+4;
					$respuesta['resp'] = new RespuestaR($tipo, unpack("l*", $buffer_dato),$attr);
					continue 1;

				case XT_ARRAY_DOUBLE:
					$buffer_dato = $this->leerDatos($longitud_restante);
					$respuesta['leido'] = $longitud+4;
					$respuesta['resp'] = new RespuestaR($tipo, unpack("d*", $buffer_dato),$attr);
					continue 1;

				case XT_ARRAY_BOOL:
					$buffer_n = $this->leerDatos(4);
					$n = unpack("L", $buffer_n);
					$buffer_dato = $this->leerDatos($longitud_restante - 4);
					// Obtenemos el array evitando el padding
					$respuesta['resp'] = new RespuestaR($tipo, unpack("C".$n[1], $buffer_dato),$attr);
					$respuesta['leido'] = $longitud+4;
					continue 1;

				case XT_SYM:
					$sym= $this->leerSEXP();
	    			//if ($symXt==XT_STR) s=(String)sym.cont; else s=sym.toString();
					$respuesta['resp']=$sym;
					$respuesta['leido']=$longitud + 4;
					continue 1;

				case XT_LIST_TAG:
                                        $respuesta['leido'] = 0;
                                        $resp = array();
                                        while ($respuesta['leido'] < $longitud) {
                                          $list = $this->leerSEXP();
					  // Hauria de tornar una llista

                                          $attrname = $this->leerSEXP();
                                          // Hauria de tornar el nom de la llista

                                          // La llista no ha de ser de tipus RespuestaR
                                          $list_final = array();
                                          $returned_value = $list['resp'] -> getValor();
                                          
                                          // Comprovem que sigui una llista
                                          if (is_array($returned_value)) {
                                            foreach($returned_value as $valor) {
                                              if (gettype($valor) == "object") {
                                                // Si l'element de la llista ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â©s de tipus RespuestaR, cal treue'n el valor
                                                if ($valor -> getTipo() == 34) {
                                                  // Les llistes de strings van indexades des del 0 en el cas d'etiquetes per atributs
                                                  $aux = array();
                                                  foreach ($valor -> getValor() as $valor2) $aux[] = $valor2;
                                                  $list_final[] = $aux;
                                                } else $list_final[] = $valor -> getValor();
                                              } else $list_final[] = $valor;
                                            }
                                          } else {
                                            // no era una llista... raru, pero weno, deixem-ho com estava perÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â² sense tipus RespuestaR
                                            $list_final = $returned_value;
                                          }
					
                                          $respuesta['leido'] += $list['leido'] + $attrname['leido'];
					  $resp[$attrname['resp']-> getValor()] = $list_final;
                                        }
                                        $respuesta['leido'] += 4;
                                        $respuesta['resp'] = new RespuestaR($tipo, $resp, $attr);

                                        continue 1;

				default:
					$buffer = $this->leerDatos($longitud_restante);
					$respuesta['leido']=$longitud + 4;
					$respuesta['resp']=null;
			}
		}
		else
		{
			$respuesta['leido']=$longitud + 4;
		}
		$respuesta['attr']=$attr;
		return $respuesta;
	}

	function abrirArchivo($nombre){
		$respuesta[] = array();

		$header_format =
    		'LTipo/'. 	 	# Tipo d la respuesta (32 bits)
    		'LLongitud/'.   	# Longitud (32 bits)
    		'x8';

		$longitud = strlen($nombre);
		//montamos y enviamos la cabecera eval; longitud+4; 0; 0;
		$l = $longitud+4;
		$buffer = pack(V, CMD_OPEN_FILE).pack(V, $l).pack(V,0).pack(V,0);
		fwrite($this->sock, $buffer);

 		//enviamos el string
		$l='a'.$longitud;
		$buffer = pack(C, DT_STRING).pack(S,$longitud).pack(C,0).pack($l,$nombre);
		fwrite($this->sock, $buffer);

		//leemos la cabecera de la respuesta de RServe
		$buffer = fread($this->sock, 16);

		// Desempaquetamos la cabecera
		$header = unpack($header_format, $buffer);
		$total = $header['Longitud'];

		// Si hay atributos los leemos
		if($total > 0){
			return fread($this->sock, $total);
		}
	}

	function leerArchivo(){
		$respuesta[] = array();

		$header_format =
    		'LTipo/'. 	 	 # Tipo d la respuesta (32 bits)
    		'LLongitud/'.   # Longitud (32 bits)
    		'x8';

		$l = 0;
		$buffer = pack(V, CMD_READ_FILE).pack(V, $l).pack(V,0).pack(V,0);
		fwrite($this->sock, $buffer);

		//leemos la cabecera de la respuesta de RServe
		$buffer = fread($this->sock, 16);

		// Desempaquetamos la cabecera
		$header = unpack($header_format, $buffer);
		$total = $header['Longitud'];

		// Si hay atributos los leemos
		if($total > 0){
			return fread($this->sock, $total);
		}
	}



	function close(){
		// cerramos el socket
		if ($this->conectado)
		{
			fclose($this->sock);
			$this->conectado = false;
		}
	}

}

class RespuestaR
{
	var $tipo;
	var $valor;
	var $attr;

	function RespuestaR($rtipo, $rvalor,$attr=array())
	{
		$this->tipo = $rtipo;
		$this->valor = $rvalor;
		$this->attr = $attr;
	}

	function getValor(){
		return $this->valor;
	}

	function getAtributos()
	{
		return $this->attr;
	}

	function getTipo(){
		return $this->tipo;
	}

	function hayError(){
		return ($this->tipo == XT_ERROR);
	}

	function imprimir(){
		$respuesta = "";
		if($this->tipo == XT_ERROR){
			//$respuesta = "($this->valor)-> $this->StringError($this->valor)<br>\n";
			$respuesta = "ERROR R\n";
		}else{
			switch($this->tipo){
				case XT_ARRAY_STR:
				case XT_STR:
					//$respuesta = substr(addslashes($val->valor), 0, stripos(addslashes($val->valor), "\0"));
					$respuesta = trim($this->valor);
					$respuesta = stripslashes($respuesta);
					continue 1;

				case XT_LIST:
					$respuesta = $this->valor['head']->imprimir();
					$respuesta .= "<br/>\n".$this->valor['body']->imprimir();
					continue 1;
				case XT_VECTOR:
					$respuesta=array();
					foreach($this->getValor() as $val) {
						$respuesta[]= $val->imprimir();
					}
					continue 1;
				case XT_ARRAY_INT:
				case XT_ARRAY_DOUBLE:
				case XT_ARRAY_BOOL:
					foreach($this->getValor() as $val) {
   						$resp = $val;
						$respuesta .= $resp.", ";
					}
					$respuesta = substr($respuesta,0,strlen($respuesta)-2);
					continue 1;
				default:
					//foreach($this->valor as $val) {
   					//	$respuesta .= $val.", ";
					//}
					//$respuesta = substr($respuesta, 0, strlen($respuesta)-2);
					//$respuesta .= "<br>\n";
					$respuesta = $this->getValor();
					continue 1;
			}
		}
		return $respuesta;
	}

	function imprimir_php()
	{
		$respuesta=null;
		if($this->tipo != XT_ERROR)
		{
			switch($this->tipo)
			{
				case XT_ARRAY_STR:
				case XT_STR:
					$respuesta = trim($this->valor);
					$respuesta = stripslashes($respuesta);
					break;
				case XT_LIST:
					$respuesta=array();
					$respuesta['head'] = $this->valor['head']->imprimir_php();
					$respuesta['body'] = $this->valor['body']->imprimir_php();
					break;
				case XT_VECTOR:
					$respuesta=array();
					foreach($this->getValor() as $val) {
						$respuesta[]= $val->imprimir_php();
					}
					break;
				case XT_ARRAY_INT:
				case XT_ARRAY_DOUBLE:
				case XT_ARRAY_BOOL:
					$respuesta=array();
					foreach($this->getValor() as $val)
					{
   						$respuesta[] = $val;
					}
					break;
				default:
					$respuesta = $this->getValor();
					break;
			}
		}
		return $respuesta;
	}


	function StringError($error_code){
		/*switch($error_code){


		}*/
	}
}
?>
