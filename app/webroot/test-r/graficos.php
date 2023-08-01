<?php

include("estatus_rserve.php");

/**
 * Gráficos en R
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package estatus
 */
class GraficosRserve extends ExtensionRserve
{
	var $ancho=400;
	var $alto=400;

	/**
	 * Ultimo mapa generado con links y titulos
	 * @var string
	 */
	var $_map=null;

	/**
	 * Genera un gráfico tipo histograma de los valores
	 * @param array $valores Array de valores integer
	 * @param integer $marca Marcar un valor en concreto (null para no marcar, valor para marcar)
	 * @param array $nombre_ejes array(título eje X, título eje Y)
	 *
	 * @return string Nombre del fichero creado. Null si hay error
	 */
	function histograma($valores,$marca=null,$nombre_ejes=null)
	{
		$this->_rserve->cache=true;
		$this->_rserve->enviar_array($valores,'valores');
		$this->_rserve->evaluar('valores=as.numeric(valores)');

		$extra='';
		if (is_array($nombre_ejes) && count($nombre_ejes)==2)
		{
			$extra.=",xlab='{$nombre_ejes[0]}',ylab='{$nombre_ejes[1]}'";
		}

		$this->_ini_imagen();
		$comandos=array();
		$comandos[]="hist(valores,main='' $extra)";
		if ($marca)
		{
			$comandos[]="points($marca, 0, pch=23, bg=\"yellow\", cex=3)";
			$comandos[]="points($marca, 0, pch=\"*\", col=\"red\", cex=3)";
		}
		$this->_rserve->evaluar($comandos);
		$this->_fin_imagen();
		$this->_rserve->evaluar_cache();


		$grafico=$this->obtener_grafico();
		$nombre_temp = tempnam(RSERVE_TMP, 'grafico');
		file_put_contents($nombre_temp,$grafico);
		return $nombre_temp;
	}
	
	/**
	 * Genera un gráfico tipo pastel de los valores
	 * @param array $valores Array de valores integer
	 * @param array $nombres nombres de las partes del grafico pastel
	 *
	 * @return string Nombre del fichero creado. Null si hay error
	 */
	function pastel($valores,$nombres=null)
	{
		$color = array("red", "green", "blue", "yellow", "white", "cyan", "black");
		$this->_rserve->cache=true;
		$this->_rserve->enviar_array($valores,'valores');
		$this->_rserve->enviar_array($nombres,'nombres');
		$this->_rserve->enviar_array($color, 'color');
		$this->_rserve->evaluar('valores=as.numeric(valores)');

		$this->_ini_imagen();
		
	/*	$extra = 'labels = names(';
		foreach($nombres as $nom)
		{
			$extra .= $nom .'",';
		}
		$extra = substr($extra,0,-1) . ')';
		*/
		$comandos=array();
		$comandos[]="oldpar <- par(mar=c(2,2,2,2))";
		$comandos[]="pie(valores, labels = nombres, clock = TRUE, col = color)";
		$comandos[]="par(oldpar)";
		
		$this->_rserve->evaluar($comandos);
		$this->_fin_imagen();
		$this->_rserve->evaluar_cache();

		$grafico=$this->obtener_grafico();
		$nombre_temp = tempnam(RSERVE_TMP, 'grafico');
		file_put_contents($nombre_temp,$grafico);
		return $nombre_temp;
	}


	/**
	 * Genera un gráfico que muestra la distribución del número de repeticiones
	 * de una serie de valores.
	 * Y=# de elementos de $valores repetidos X veces
	 *
	 * @param array $valores Array de enteros
	 * @param array $nombre_ejes array(título eje X, título eje Y)
	 *
	 * @return string Nombre del fichero creado. Null si hay error
	 */
	function distribucion_num_repeticiones($valores,$nombre_ejes=null)
	{
		$this->_ini_imagen();
		$this->_rserve->cache=true;
		$this->_rserve->enviar_array($valores,'Ni',false);

		$extra='';
		if (is_array($nombre_ejes) && count($nombre_ejes)==2)
		{
			$extra.=",xlab='{$nombre_ejes[0]}',ylab='{$nombre_ejes[1]}'";
		}
		$comando_plot="plot(table(tN),type='h',yaxp=c(0,top,top) $extra )\n";

		$comandos[]='tN = table(Ni)';
		$comandos[]='top = max(table(tN))';
		$comandos[]=$comando_plot;
		$this->_rserve->evaluar($comandos);
		$this->_rserve->evaluar_cache();
		$this->_fin_imagen();
		return $this->guardar_grafico_temporal();
	}

	/**
	 * Genera un gráfico tipo plot X-Y
	 *
	 * @param array $puntos Array de parejas. Ejemplo:
	 * $puntos=array( array(2,3) , array(3,4))
	 * Creará un gráfico con los puntos (x=2, y=3); (x=3,y=4)
	 *
	 * @param string $tipo 'p'=>Puntos, 'l'=>Líneas , 'b'=>'Lineas + puntos'
	 * @param array $nombre_ejes array(título eje X, título eje Y)
	 * @param array $params Parametros extra
	 * 		$params['leyenda'] Array asociativo #idcolor=>nombre a mostrar
	 * 		$params['colores'] Array de enteros, cardinalidad igual a $puntos, asocia un color a cada punto
	 *      $params['href'] Array de links asociados a los puntos. Debe tener mismo tamaño que $puntos, pero puede tener valores vacíos
	 *      $params['title'] Array de tooltips asociados a los puntos. Debe tener mismo tamaño que $puntos, pero puede tener valores vacíos
	 * 		$params['filled'] Se rellenan los puntos del gráfico si vale cierto. Incompatible con parámetro chars
	 *      $params['no_eje_x'] Elimina los números del eje X
	 *      $params['marcas'] Array de puntos especiales a remarcar, cada elemento del array tiene la estructura: array(X,Y,COLOR)
	 * 		$params['chars'] Número de formas diferentes de los puntos, obligatorio el parámetro colores
	 *
	 * @return string Nombre del fichero creado. Null si hay error
	 *
	 */
	function plot($puntos,$tipo='p',$nombre_ejes=null,$params=array())
	{
		$extra='';

		$x=array();
		$y=array();

		$comandos=array();

		//Mapear los colores a 1,2,3,4,5...
		if (isset($params['colores']))
		{
			$tmp=$params['colores'];
			$colores_hash=array();
			$colores=array();
			$num_colores=1;
			foreach ($tmp as $color)
			{
				if (! isset($colores_hash[$color]))
				{
					$colores_hash[$color]=$num_colores;
					$num_colores++;
				}
				$colores[]=$colores_hash[$color];
			}
			$params['colores']=$colores;

			if (isset($params['leyenda']))
			{
				$leyenda=array();
				foreach ($params['leyenda'] as $color=>$nombre)
				{
					if (! isset($colores_hash[$color]))
					{
						$colores_hash[$color]=$num_colores;
						$num_colores++;
					}
					$leyenda[$colores_hash[$color]]=$nombre;
				}
				$params['leyenda']=$leyenda;
			}
		}

		foreach ($puntos as $p)
		{
			$x[]=$p[0];
			$y[]=$p[1];
		}
		$this->_rserve->cache=true;

		$this->_rserve->enviar_array($x,'x',false);
		$this->_rserve->enviar_array($y,'y',false);

		if (isset($params['no_eje_x']))
		{
			$extra.=',xaxt="n"';
		}

		if (isset($params['filled'])&&$params['filled'])
		{
			$extra.=',pch=19,cex=1.5';
		}

		if (isset($params['colores']))
		{
			$this->_rserve->enviar_array($params['colores'],'c',false);
			if (isset($params['chars']))
			{
				$num_formas=$params['chars'];
				//los colores definitivos (c es el numero de colores inicial sin formas)
				$comandos[]="cl <- trunc((c - 1) / $num_formas) + 2";
				$comandos[]="dimcol <- floor($num_colores/$num_formas)+1";
				//los simbolos
				$comandos[]="chars = c%%$num_formas + 15";
				$comandos[]="palette(c(\"black\",rainbow(dimcol)))";

				$comandos[]="x = c(x[1],x)";
				$comandos[]="y = c(y[1],y)";
				$comandos[]="chars = c(chars[1],chars)";
				$comandos[]="cl=c(1,cl)";
				$comandos[]="href=c(href[1],href)";
				$comandos[]="title=c(title[1],title)";

				$extra.=',pch=chars,col=cl,cex=1.5';
			}
			else
			{
				if (isset($params['leyenda']) && $params['leyenda'])
				{
					$comandos[]="palette(rainbow(length(tl)))";
				}
				$extra.=',col=c';
			}

		}


		if (is_array($nombre_ejes) && count($nombre_ejes)==2)
		{
			$extra.=",xlab='{$nombre_ejes[0]}',ylab='{$nombre_ejes[1]}'";
		}

		$this->_ini_imagen();

		$comando_plot="plot(x,y,type='$tipo' $extra )\n";

		//Enviar arrays para generar mapa de puntos posteriormente
		if (isset($params['title']) || isset($params['href']))
		{
			if (isset($params['title']))
			{
				$this->_rserve->enviar_array($params['title'],'title',true);
			}
			else
			{
				$this->_rserve->enviar_array(array_fill(0,count($puntos),''),'title');
			}
			if (isset($params['href']))
			{
				$this->_rserve->enviar_array($params['href'],'href');
			}
			else
			{
				$this->_rserve->enviar_array(array_fill(0,count($puntos),''),'href');
			}
			$generar_mapa=true;
		}
		else
		{
			$generar_mapa=false;
		}

		$this->_rserve->cache=true;


		if (isset($params['leyenda']))
		{
			if (isset($params['chars']))
			{
				$this->_rserve->enviar_array(array_keys($params['leyenda']),'cl_leyenda');

				$comandos[]="cl_2 <- trunc((cl_leyenda - 1) / $num_formas) + 2";
				$comandos[]="chars_2 = cl_leyenda %%$num_formas + 15";
			}
			else
			{
				$this->_rserve->enviar_array(array_keys($params['leyenda']),'cl_leyenda');
			}
			$this->_rserve->enviar_array(array_values($params['leyenda']),'tl');

			$tam=count($params['leyenda']);

			$comandos[]='m <- matrix(1:2, 1, 2); layout(m, widths=c(7, 2))';
			$comandos[]="sx<- rep(1,$tam)";
			$comandos[]="sy<- $tam:1";
			$comandos[]='par(mar=c(2.5,4,1,0.5))';
			$comandos[]=$comando_plot;

			if (isset($params['marcas']))
			{
				foreach ($params['marcas'] as $marca)
				{
					$comandos[]="points(" . $marca[0] . ',' . $marca[1] . ', pch=23, col=' . $marca[2]. ', cex=3)';
				}
			}

			if ($generar_mapa)
			{
				$this->_rserve->evaluar($comandos);
				$this->_rserve->evaluar_cache();
				$ret=$this->_rserve->evaluar('estatus.image_map(x,y,href,title)');
				$this->_map=$ret->imprimir();
				$comandos=array();
				$this->_rserve->cache=true;
			}

			$comandos[]='par(mar=c(2.5,1,1,0.5))';
			if (isset($params['chars']))
			{
				$comandos[]='plot(sx,sy,xlim=c(0.99,1.5), pch=chars_2,col=cl_2,cex=1.5,xlab="",ylab="",xaxt="n",yaxt="n",axes=FALSE)';
			}
			else
			{
				$comandos[]='plot(sx,sy,xlim=c(0.99,1.5), pch=19,cex=1.5,col=cl_leyenda,xlab="",ylab="",xaxt="n",yaxt="n",axes=FALSE)';
			}

			$comandos[]='text(sx,sy,tl, pos=4,cex=0.7)';
		}
		else
		{
			$comandos[]=$comando_plot;
			if (isset($params['marcas']))
			{
				foreach ($params['marcas'] as $marca)
				{
					$comandos[]="points(" . $marca[0] . ',' . $marca[1] . ', pch=19, col=' . $marca[2]. ', cex=1.5)';
				}
			}

			$this->_rserve->evaluar($comandos);
			$this->_rserve->evaluar_cache();
			$ret=$this->_rserve->evaluar('estatus.image_map(x,y,href,title)');
			$this->_map=$ret->imprimir();
			$comandos=array();
			$this->_rserve->cache=true;
		}

		/* leyenda tipo "dentro del grafico"
		 * if (isset($params['leyenda']))
		{
			$this->_rserve->enviar_array(array_keys($params['leyenda']),'c_leyenda');
			$this->_rserve->enviar_array(array_values($params['leyenda']),'t_leyenda');
			$comando[]='legend("bottomright", "(x,y)", pch=1, title="",col=c_leyenda,legend=t_leyenda)';
		}*/

		$this->_rserve->evaluar($comandos);
		$this->_rserve->evaluar_cache();

		$this->_fin_imagen();

		return $this->guardar_grafico_temporal();
	}

	/**
	 * Hace un boxplot útil para variables continuas
	 * @param array $valores Cada elemento es un array($valor_continuo_X,$var_categorica_Y)
	 * @param array $nombre_ejes array(título eje X, título eje Y)
	 *
	 * @return string Nombre del fichero creado. Null si hay error
	 */
	function boxplot($valores,$nombre_ejes=null)
	{
		$this->_ini_imagen();
		$this->_rserve->cache=true;

		//obtener un array con todas las categorias ordenadas de menor a mayor
		$categorias=array();
		$categoria=array();
		$resp=array();
		foreach ($valores as $v)
		{
			$categorias[$v[1]]=1;
		}
		$categorias=array_keys($categorias);
		sort($categorias);

		$categoria=array();
		$resp=array();
		foreach ($valores as $v)
		{
			$resp[]=$v[0];
			//$index=array_search($v[1],$categorias);
			$categoria[] = count($categorias) -1 -array_search($v[1],$categorias);
			//$categoria[]=$categorias[ count($categorias) -1 -$index];
		}
		$this->_rserve->enviar_array($resp,'resp',false);
		$this->_rserve->enviar_array($categoria,'categoria',true);
		$this->_rserve->enviar_array($categorias,'categorias',true);

		$extra='';
		if (is_array($nombre_ejes) && count($nombre_ejes)==2)
		{
			$extra.=",xlab='{$nombre_ejes[0]}',ylab='{$nombre_ejes[1]}'";
		}
		$comandos[]="categorias=rev(categorias);categorias";
		$comandos[]="boxplot(resp ~ categoria, horizontal=TRUE,names=categorias $extra )";
		$comandos[]='stripchart(resp ~ categoria, method="stack", col="blue", add=TRUE)';
		$this->_rserve->evaluar($comandos);
		$this->_rserve->evaluar_cache();
		$this->_fin_imagen();
		return $this->guardar_grafico_temporal();
	}

	/**
	 * Hace un barplot útil para variables discretas
	 * @param array $valores Cada elemento es un array($valor_discreto_X,$nombre_categoria)
	 * @param array $categorias Array de nombres de categorías
	 * @param array $nombre_ejes array(título eje X, título eje Y)
	 *
	 * @return string Nombre del fichero creado. Null si hay error
	 */
	function barplot($valores,$categorias,$nombre_ejes=null,$opciones=array())
	{
		$this->_ini_imagen();
		$this->_rserve->cache=true;

		$categoria=array();
		$resp=array();
		foreach ($valores as $v)
		{
			$resp[]=$v[0];
			$categoria[]=array_search($v[1],$categorias);
		}
		$this->_rserve->enviar_array($resp,'resp',false);
		$this->_rserve->enviar_array($categoria,'categoria',true);
		$this->_rserve->enviar_array($categorias,'categorias',true);
		$this->_rserve->enviar_array(array_keys($categorias),'colores',true);

		$extra='';
		if (is_array($nombre_ejes) && count($nombre_ejes)==2)
		{
			$extra.=",xlab='{$nombre_ejes[0]}',ylab='{$nombre_ejes[1]}'";
		}

		$comando=array();
		if (count($categorias)>1)
		{
			$comando[]="palette(rainbow(length(categorias)))";
		}

		if (! isset($opciones['leyenda']) || $opciones['leyenda']==true)
		{
			$extra.=', legend=categorias';
		}
		
		$comando[]="barplot(table(categoria,resp),col=(colores + 1),beside=TRUE $extra)";
		$this->_rserve->evaluar($comando);
		$this->_rserve->evaluar_cache();
		$this->_fin_imagen();
		return $this->guardar_grafico_temporal();
	}
	
	/**
	* Gráfico personalizado
	* 
	* @param array $valores
	* @param array $categorias
	* @param array $comandos array con comandos personalizados se pueden usar los nombres categorias y valores
	* 
	* @return string Nombre del fichero creado. Null si hay error
	*/
	function grafico_personalizado($valores, $categorias, $comandos)
	{
		$this->_ini_imagen();
		$this->_rserve->cache=true;
		
		$this->_rserve->enviar_array($valores,'valores', false);
		$this->_rserve->enviar_array($categorias,'categorias');
		//$this->_rserve->evaluar('valores=as.numeric(valores)');
		
		$this->_rserve->evaluar($comandos);
		$this->_rserve->evaluar_cache();
		$this->_fin_imagen();
		return $this->guardar_grafico_temporal();
	}
	
	/**
	 * Devuelve el mapa generado por los gráficos
	 *
	 * @return string Mapa en HTML
	 */
	function print_map($nombre='map')
	{
		$c='<map name="' . $nombre . '" id="map_' . $nombre .'">' . $this->_map . '</map>';
		return $c;
	}


	/**
	 * Guarda un gráfico en un fichero temporal
	 * @param string $nombre Nombre de la variable
	 * @return string Nombre del fichero guardado
	 */
	function guardar_grafico_temporal($nombre='grafico')
	{
		$grafico=$this->obtener_grafico($nombre);
		$nombre_temp = tempnam(RSERVE_TMP, 'grafico');
		file_put_contents($nombre_temp,$grafico);
		return $nombre_temp;
	}

	/**
	 * Guarda un gráfico en un fichero temporal dentro de img/rserve y devuelve la ruta relativa a img
	 * @param string $nombre Nombre de la variable
	 * @return string Nombre del fichero guardado relativo a img/
	 */
	function guardar_grafico_img($nombre='grafico')
	{
		$grafico=$this->obtener_grafico($nombre);
		$nombre_temp='graficos' . DS . md5($grafico) . '.png';
		file_put_contents(WWW_ROOT . DS . 'img' . DS . $nombre_temp,$grafico);
		return $nombre_temp;
	}


	function _ini_imagen()
	{
		$this->_rserve->evaluar("grafico=ini_imagen({$this->ancho},{$this->alto})");
	}

	function _fin_imagen()
	{
		$this->_rserve->evaluar('fin_imagen()');
	}

	/**
	 * Devuelve en un string el contenido del gráfico en formato PNG
	 *
	 * @param string $nombre Nombre de variable
	 * @return string Contenido de la imagen en formato PNG. Null si no es una imagen
	 */
	function obtener_grafico($nombre='grafico')
	{
		$valor=$this->_rserve->obtener_codigo_variable($nombre);
		if (preg_match('/^structure\("(.*?)", class = "est\.imagen"\)$/',$valor,$matches))
		{
			$fichero=$matches[1];
			return $this->_rserve->obtener_fichero($fichero);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Muestra un PNG y lo elimina a continuación
	 * @param string $grafico Fichero a mostrar / variable del grafico / contenido del gráfico
	 */
	function mostrar($grafico)
	{
    	header('Content-Type: image/png');
    	if (is_file($grafico))
    	{
    		//se trata de un fichero
    		echo file_get_contents($grafico);
    		@unlink($grafico);
    	}
    	elseif ($contenido=$this->obtener_grafico($grafico))
    	{
    		//se trata de una variable
    		echo $contenido;
    	}
    	else
    	{
    		//se trata del contenido directamente
    		echo $grafico;
    	}
		exit;
	}
}
?>
