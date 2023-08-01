<?php
/**
 * EstatusRserve
 * Interfaz para sesiÃƒÂ³n Rserve
 *
 * @author David Rodriguez
 * @author Miguel Ros
 *
 * @package estatus
 */
 
include("conexion.php");

/**
 * Directorio donde se escriben temporales desde y hacia Rserve
 */
define('RSERVE_TMP',TMP . 'rserve/');
define('IMAGES_URL', 'img/');
define('DS', DIRECTORY_SEPARATOR);
define('WEBROOT_DIR', basename(dirname(__FILE__)) . ".." . DS);
define('WWW_ROOT', dirname(__FILE__) . DS . ".." . DS);

function am() {
	$r = array();
	$args = func_get_args();
	foreach ($args as $a) {
	if (!is_array($a)) {
	$a = array($a);
	}
	$r = array_merge($r, $a);
	}
	return $r;
}

class EstatusRserve
{
	var $_ip;
	var $_port;
	var $_conexion=null;
	var $_error=null;
	var $_ultima_respuesta=null;

	/**
	 * Cachear instrucciones. Si es cierto, entonces las
	 * instrucciones enviadas mediante evaluar() se guardan
	 * en memoria, y se ejecutarÃƒÂ¡n cuando se haga evaluar_cache().
	 * Tras evaluar_cache() el valor pasa a falso automÃƒÂ¡ticamente.
	 *
	 * @var boolean $cache
	 */

	var $cache=false;

	var $_cached_inst=array();

	var $historia=array();

	/**
	 * @param boolean $load_estatus Cierto si se desea cargar las librerÃƒÂ­as de estatus
	 * @param string $ip IP del servidor RServe
	 * @param integer $port Puerto del servidor
	 *
	 */
	function __construct($load_estatus=true,$ip='127.0.0.1',$port=6311)
	{
		static $conexion=null;
		$this->_ip=$ip;
		$this->_port=$port;


		$this->_conexion=new RConexion($ip,$port);
		$this->_conexion->conexion();
		if ($load_estatus)
		{
			$this->evaluar('library(estatus)');
			//$this->evaluar('library(est)');
		}
	}

	/**
	 * Llamado automÃƒÂ¡ticamente
	 */
	function __destruct()
	{
		if ($this->_conexion->conectado)
		{
			$dir=$this->obtener_directorio();
			$this->_conexion->close();
			$this->_eliminar_directorio($dir);
		}
	}
	
	function getTime()
	{
		return $this->_conexion->time_r;
	}
	
	
	function getInstr()
	{
		return $this->_conexion->inst_r;
	}


	/**
	 * Elimina un directorio recursivamente
	 *
	 * @param string $dir Directorio a eliminar
	 * @return boolean Ãƒâ€°xito
	 */
	function _eliminar_directorio($dir)
	{
		App::import('Utility', 'Folder');
		$folder = new Folder($dir);
		return $folder->delete();
	}

	/**
	 * Devuelve el ÃƒÂºltimo error ocurrido
	 *
	 * @return string
	 */
	function ultimo_error()
	{
		return $this->_error;
	}

	/**
	 * Devuelve la ÃƒÂºltima respuesta de R
	 *
	 * @return object RespuestaR
	 */
	function ultima_respuesta()
	{
		return $this->_ultima_respuesta;
	}

	/**
	 * Reinicia la sesiÃƒÂ³n eliminando todos los objetos
	 */
	function reiniciar()
	{
		$this->cache=false;
		$this->_cached_inst=array();
		$this->historia=array();
		$this->evaluar('rm(list=rs())');
	}


	/**
	 * Enviar a R las instrucciones que queremos ejecutar
	 *
	 * @param mixed $instrucciones CÃƒÂ³digo a ejecutar. Si es un string, tiene las instrucciones separadas por \n.
	 * Si es un array de lÃƒÂ­neas de instrucciones, se convertirÃƒÂ¡ automÃƒÂ¡ticamente a string
	 *
	 * @param boolean $solo_sintaxis Si es cierto, entonces ÃƒÂºnicamente se comprueba la sintaxis, no se ejecuta
	 *
	 * @return boolean Cierto si las instrucciones son correctas.
	 * Falso si incorrectas y actualiza el ÃƒÂºltimo error
	 */
	function evaluar_instrucciones($instrucciones,$solo_sintaxis=false)
	{
		$this->historia[]='# start evaluar_instrucciones';
		if (is_array($instrucciones))
		{
			$this->historia=am($this->historia,$instrucciones);
			$instrucciones=join($instrucciones,"\n");
		}
		else
		{
			$this->historia=am($this->historia,split("\n",$instrucciones));
		}
		$this->historia[]='# end evaluar_instrucciones';

		//escribimos las instrucciones en un temporal
		$nombre_temp = tempnam(RSERVE_TMP, 'expresiones');
		file_put_contents($nombre_temp,$instrucciones);
		chmod($nombre_temp, 0644);

		//PeticiÃƒÂ³n a R para la evaluaciÃƒÂ³n del archivo
		if ($solo_sintaxis)
		{
			$res = $this->_conexion->evaluarVoid("cat(file=\"$nombre_temp\")");
			$res = $this->_conexion->evaluarVoid("parse(\"$nombre_temp\")");
		}
		else
		{
			$res = $this->_conexion->evaluarVoid("source(\"$nombre_temp\", echo=TRUE)");
		}
		$this->_ultima_respuesta=$res;
		unlink($nombre_temp);
		// Comprobamos que el resultado es correcto

		if($res->tipo == XT_ERROR)
		{
			// Recuperamos el mensaje de error de R
			$error= $this->evaluar("geterrmessage()");
			$str=$error->imprimir();
			$str=substr($str,strpos($str,':')+1);
			$this->_error=$str;
			return false;
		}
		return true;
	}

	/**
	 * Obtiene el codigo que nos permite recuperar una variable
	 * en otra sesion
	 *
	 * @param string $nombre Nombre de la variable
	 * @return string CÃƒÂ³digo
	 *
	 */
	function obtener_codigo_variable($nombre)
	{
		$res = $this->evaluar("toString(deparse($nombre, width=500))");
		return $res->imprimir();
	}

	/**
	 * Obtiene el cÃƒÂ³digo HTML para visualizar una variable
	 * @param string $variable Nombre de la variable
	 */
	function obtener_HTML_variable($nombre)
	{
		$res = $this->evaluar("estatus.HTML($nombre)");
		return $res->imprimir();
	}

	/**
	 * Obtiene una representaciÃƒÂ³n HTML de una variable
	 * @param string $nombre Nombre de la variable
	 * @param string $plantilla Plantilla de representaciÃƒÂ³n bajo /vendors/estatus/html
	 */
	function obtener_HTML_variable2($nombre,$plantilla='default')
	{
		$tipo = $this->obtener_tipo_variable($nombre);
		switch ($tipo)
		{
			case 'matrix':
				$list= $this->obtener_lista($nombre);
				$html=$this->obtener_HTML_matrix($list);
				break;
			case 'array':
				$html='[ARRAY]';
				$list= $this->obtener_lista($nombre);
				switch(count($list['attr']['dim']))
				{
					case 2:
						$html=$this->obtener_HTML_matrix($list);
						break;
					case 3:
						$html=$this->obtener_HTML_multiarray($list);
						break;
					default:
						$html='[UNKNOWN ARRAY]';
						break;
				}
				break;
			case 'est.imagen':
				$graficos=& new GraficosRserve($this);
				$img=$graficos->guardar_grafico_img($nombre);
				$url = IMAGES_URL . $img;
				$webroot_url=WEBROOT_DIR;

				if (in_array($nombre.'_map',$this->obtener_lista_variables()))
				{
					$graficos->_map=$this->obtener_valor_variable($nombre . '_map');
					$html_map=$graficos->print_map($nombre);
					$html="<div class=\"estatusr\"><img src=\"/{$url}\" border=\"0\" alt=\"\" usemap=\"$nombre\"/></div>$html_map";
				}
				else
				{
					$html="<div class=\"estatusr\"><img src=\"/{$url}\" border=\"0\" alt=\"\" /></div>";
				}

				//$html="<r:grafico nombre=\"$nombre\" />";
				break;
			case 'numeric':
			case 'vector':
			case 'character':
			default:
				$html=$this->obtener_valor_variable($nombre);
				if (is_array($html))
				{
					$html=join(", " , $html);
				}
				break;
		}
		return $html;
	}

	function obtener_HTML_matrix($list)
	{
		$matrix=array();
		$nrows=$list['attr']['dim'][0];
		$ncols=$list['attr']['dim'][1];

		$html='<table class="estatusr_table">';
		if (isset($list['attr']['dimnames']))
		{
			$html.='<tr>';
			if (isset($list['attr']['dimnames'][0][0]))
			{
				$html.= '<td class="estatusr_blanco">&nbsp;</td>';
			}

                        if (isset($list['attr']['dimnames'][1][0])) {
			  foreach ($list['attr']['dimnames'][1] as $colname)
			  {
				$html.="<td class=\"estatusr_colname\">$colname</td>";
			  }
			  $html.='</tr>';
                        }
		}

		$k=1;
		for ($i=0;$i<$ncols;$i++)
		{
			for ($j=0;$j<$nrows;$j++)
			{
				$matrix[$j][$i]=$list['value'][$k];
				$k++;
			}
		}

		foreach ($matrix as $i=>$row)
		{
			$html.='<tr>';
			if (isset($list['attr']['dimnames'][0][0]))
			{
				$html.="<td class=\"estatusr_rowname\">{$list['attr']['dimnames'][0][$i]}</td>";
			}
			foreach ($row as $field)
			{
				$html.="<td class=\"estatusr_field\">$field</td>";
			}
			$html.='</tr>';
		}
		$html.='</table>';
		return $html;
	}

	function obtener_HTML_multiarray($list)
	{
		$k=1;
		$html="";
		for($m=0;$m<$list['attr']['dim'][2];$m++)
		{
			$html.='<table class="estatusr_table">';
			if (isset($list['attr']['dimnames']))
			{
				$html.='<tr>';
				if (isset($list['attr']['dimnames'][0][0]))
				{
					$html.= '<td class="estatusr_blanco">' . $list['attr']['dimnames'][2][$m]. '</td>';
				}
				foreach ($list['attr']['dimnames'][1] as $colname)
				{
					$html.="<td class=\"estatusr_colname\">$colname</td>";
				}
				$html.='</tr>';
			}

			$matrix=array();
			$nrows=$list['attr']['dim'][0];
			$ncols=$list['attr']['dim'][1];
			for ($i=0;$i<$ncols;$i++)
			{
				for ($j=0;$j<$nrows;$j++)
				{
					$matrix[$j][$i]=$list['value'][$k];
					$k++;
				}
			}

			foreach ($matrix as $i=>$row)
			{
				$html.='<tr>';
				if (isset($list['attr']['dimnames'][0][0]))
				{
					$html.="<td class=\"estatusr_rowname\">{$list['attr']['dimnames'][0][$i]}</td>";
				}
				foreach ($row as $field)
				{
					$html.="<td class=\"estatusr_field\">$field</td>";
				}
				$html.='</tr>';
			}
			$html.='</table>';
		}
		return $html;
	}


	/**
	 * Devuelve el tipo de variable
	 * @param string $nombre
	 * @return string Clase de la variable (ej: 'matrix','est.imagen','numeric','vector')
	 */
	function obtener_tipo_variable($nombre)
	{
		$res = $this->evaluar("class($nombre)");
		$tipo = $res->imprimir();

		if ($tipo == 'numeric') {
		  $res = $this->evaluar($nombre);
		  $valor = $res->imprimir();

                  if (is_array($valor)) $tipo = 'vector';
		}

		return $tipo;
	}


	/**
	 * Devuelve un fichero ubicado dentro del directorio de sesiÃƒÂ³n actual
	 *
	 * @param string $file Nombre del fichero
	 * @return string Contenido del fichero
	 */
	function obtener_fichero($file)
	{
		$dir=$this->obtener_directorio();
		return file_get_contents($dir . DS . $file);
	}

	/**
	 * Devuelve la lista de variables que tenemos definidas
	 * en la sesion actual de R
	 * @return array Array de nombres de variables declaradas
	 */
	function obtener_lista_variables()
	{
		return array_keys($this->obtener_lista_valores_tipos_variables());
	}

	/**
	 * Devuelve un array asociativo de variable=>valor con todas las
	 * variables que tenemos definidas en la sesion actual de R
	 *
	 * @return array Array asociativo variable=>valor
	 */
	function obtener_lista_valores_variables()
	{
		$temp=$this->obtener_lista_valores_tipos_variables();

		$arr=array();
		foreach ($temp as $nombre=>$info)
		{
			$arr[$nombre]=$info['valor'];
		}
		return $arr;
	}


	/**
	 * Devuelve un array asociativo de variable=>array('tipo'=>$tipo, 'valor'=>$valor) con todas las
	 * variables que tenemos definidas en la sesion actual de R
	 *
	 * @return array Array asociativo de variable=>array('tipo'=>$tipo, 'valor'=>$valor)
	 */

	function obtener_lista_valores_tipos_variables()
	{
		$res = $this->evaluar("estatus.ListaVariables()");
		if (!$res || $res->hayError()) return array();
		$ret=$res->imprimir();
		if (! is_array($ret)) $ret=array();

		$arr=array();
		for ($i=0;$i<count($ret);$i+=3)
		{
			$arr[$ret[$i]]=array(	'valor'=>$ret[$i+1],
									'tipo'=>$ret[$i+2]);
		}
		return $arr;
	}


	/**
	 * Obtiene el valor de una variable
	 * @param string $nombre Nombre de la variable
	 * @return mixed Valor de la variable
	 */
	function obtener_valor_variable($nombre)
	{
		$res = $this->evaluar($nombre);
		return $res->imprimir();
	}

	/**
	 * Recupera una variable a partir del cÃƒÂ³digo
	 * generado en la funcion obtener_codigo_variable
	 *
	 * @param string $nombre Nombre de la variable
	 * @param string $codigo CÃƒÂ³digo de la variable
	 * @param boolean $es_string $codigo es directamente un string
	 *
	 * @return string Resultado
	 */
	function recuperar_variable($nombre, $codigo, $es_string=false)
	{
		if (is_array($codigo))
		{
			return $this->enviar_array($codigo,$nombre);
		}
		else
		{
			if (! $es_string)
			{
				if ($codigo==='') $codigo="''";
				else if ($codigo===NULL) $codigo="NULL";
			}
			else
			{
				$codigo="'$codigo'";
			}
			$res = $this->evaluar("$nombre<-$codigo");
			return $res->imprimir();
		}
	}

	/**
	 * Obtiene el directorio de trabajo de la sesiÃƒÂ³n actual
	 *
	 * @return string Directorio de trabajo (ruta absoluta).
	 * El directorio serÃƒÂ¡ similar a /ruta_absoluta_cake/RSERVE_TMP/connXXX
	 */
	function obtener_directorio(){
		$res = $this->evaluar('getwd()');
		if ($res) return $res->imprimir();
		else return null;
	}

	/**
	 * Estable el directorio de trabajo actual en la sesiÃƒÂ³n
	 * @param string $dir
	 */
	function establecer_directorio($dir)
	{
		$res = $this->evaluar("setwd(\"$dir\")");
	}


	/*
	 * Elimina un directorio
	 * ???
	 * @param string $dir Nombre del directorio
	 * TODO: Posiblemente retornar cierto si correcto
	 */
	/*
	function eliminar_directorio($dir)
	{
		unlink($dir);
		$res = $this->evaluar("unlink(\"$dir\", TRUE)");
	}
	*/

	/**
	 * Devuelve el error relativo cometido por el alumno
	 * @param string $nombre Nombre de la variable
	 * @param real $valor Respuesta del alumno
	 */
	function obtener_error_relativo($nombre, $valor)
	{
		$res = $this->evaluar("if(inherits(try(round(abs($nombre-as.numeric(\"$valor\"))*100/$nombre, digits=3)),\"try-error\")){'-'}else{round(abs($nombre-as.numeric(\"$valor\"))*100/$nombre, digits=3)}");
		return $res->imprimir();
	}

	/**
	 * Enviar un array a R
	 * @param array $array Array a enviar. Debe ser un vector de enteros/strings mezclados
	 * @param string $nombre Nombre de la variable destino en R
	 * @param boolean $es_string El array a enviar tiene strings
	 */
	function enviar_array($array,$nombre,$es_string=true)
	{
		$codigo='c(';
		for($i=0;$i<count($array);$i++)
		{
			if ($i!=0)
			{
				$codigo.=',';
			}

			if (is_numeric($array[$i]))
			{
				$codigo.= $array[$i];
			}
			elseif ($es_string)
			{
				$str=$array[$i];
				$str=str_replace('"','\"',$str);
				$codigo.="\"{$str}\"";
			}
			else
			{
				$codigo.=$array[$i];
			}

		}
		$codigo.=')';
		return $this->recuperar_variable($nombre,$codigo);
	}

	/**
	 * EnvÃƒÂ­a a R sentencias
	 * @param mixed $codigo Un string con la sentencia o un array de sentencias
	 * @param boolean $resultado Ã‚Â¿Se desea obtener el resultado? Solo para no-cache
	 * @return object Resultado
	 */
	function evaluar($codigo,$resultado=true)
	{
		if (is_array($codigo))
		{
			$codigo=join("\n",$codigo);
		}
		if ($this->cache)
		{
			$this->_cached_inst[]=$codigo;
			//hay que responder con un objeto RespuestaR
			return new RespuestaR(XT_STR,'cached');
		}
		else
		{
			$this->historia[]=$codigo;
			if ($resultado)
			{
				$res=$this->_conexion->evaluar($codigo);
				$this->_ultima_respuesta=$res;
				return $res;
			}
			else
			{
				$this->_conexion->evaluarVoid($codigo);
				return new RespuestaR(XT_STR,'void');
			}
		}
	}

	/**
	 * EvalÃƒÂºa las instrucciones cacheadas.
	 * AutomÃƒÂ¡ticamente restaura $cache a false
	 * @return object ÃƒÅ¡ltima respuesta
	 */
	function evaluar_cache()
	{
		$this->cache=false;
		$this->evaluar_instrucciones($this->_cached_inst);
		$this->_cached_inst=array();
		return $this->ultima_respuesta();
	}

	/**
	 * Establece la semilla de generaciÃƒÂ³n de nÃƒÂºmeros
	 * aleatorios de R.
	 * @param integer $semilla Si es null, genera una nueva semilla aleatoriamente
	 * @return integer Semilla generada (en caso de $semilla=null)
	 */
	function establecer_semilla($semilla=null)
	{
		if ($semilla===null || ! is_numeric($semilla))
		{
			$semilla=rand();
		}
		$this->evaluar("set.seed($semilla)");
		return $semilla;
	}

	/**
	 * Devuelve una estructura compleja sobre una variable tipo lista
	 * (matrix, array, ...) incluyendo todos los atributos 'dim' , 'dimnames' ...
	 * @param string $nombre Nombre de variable R
	 * @return array
	 * 	$result['value'] => Valor de la variable
	 *  $result['attr'] => Atributos extras
	 */
	function obtener_lista($nombre)
	{
		$res = $this -> evaluar($nombre);

		$value = $res -> valor;
		if (isset($res -> attr -> valor))
		{
                  if ($res -> attr -> getTipo() == 17) {
                    // S'estÃƒÂ  fent servir R antic, cal parsejar atributs (tenen estructura head, tail, tag)
		    $attr = $this -> _parsear_attr($res -> attr -> valor);
                  } else $attr = $res -> attr -> valor;
		}
		else
		{
			$attr=array();
		}
		return array('value'=>$value , 'attr'=>$attr);
	}

	/**
	 * Parsea los atributos extra de una lista
	 * @param string $body Valor Body de una XT_LIST
	 * @return array Array asociativo nombre_atributo => valor
	 */
	function _parsear_attr($body)
	{
		$attr=array();
		if (is_array($body) && isset($body['head']))
		{
			$name=$body['tag']['resp']->valor;
			$value=$body['head']->imprimir_php();
			$attr[$name]=$value;
			return am($attr,$this->_parsear_attr($body['body']->valor));
		}
		return $attr;
	}
}


class ExtensionRserve
{
	var $_rserve=null;

	/**
	 * @param object $rserve Instancia de EstatusRserve o null para crear una nueva conexión
	 */
	function __construct($rserve=null)
	{
		if ($rserve===null)
		{
			$this->_rserve = new EstatusRserve();
		}
		else
		{
			$this->_rserve = $rserve;
		}
	}
}
?>
