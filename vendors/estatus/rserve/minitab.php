<?php

class MinitabRserve extends ExtensionRserve
{
	var $simbolo_decimal='.';
	var $separador_columnas="&#09;";

	/**
	 * Obtiene una tabla HTML de un array de nombres de variables
	 * lista para copiar a excel desde mozilla
	 * @param array $vars Array de nombres de variables
	 * @return string Tabla HTML
	 */
	function obtener_html_mozilla_excel($vars)
	{
		$arr=$this->obtener_array($vars);
		return $this->_convertir_tabla_html($arr);
	}

	/**
	 * Obtiene una tabla HTML de un array de nombres de variables
	 * lista para copiar a minitab desde mozilla
	 * @param array $vars Array de nombres de variables
	 * @return string Tabla HTML
	 */
	function obtener_html_mozilla_minitab($vars)
	{
		$arr=$this->obtener_array($vars);
		return $this->_convertir_tabla_html($arr);
	}


	/**
	 * Obtiene una tabla HTML de un array de nombres de variables
	 * lista para copiar a excel desde internet explorer
	 * @param array $vars Array de nombres de variables
	 * @return string Tabla HTML
	 */
	function obtener_html_explorer_excel($vars)
	{
		$arr=$this->obtener_array($vars);
		return $this->_convertir_tabla_html($arr);
	}


	/**
	 * Obtiene una tabla HTML de un array de nombres de variables
	 * lista para copiar a minitab desde internet explorer
	 * @param array $vars Array de nombres de variables
	 * @return string Tabla HTML
	 */
	function obtener_html_explorer_minitab($vars)
	{
		$arr=$this->obtener_array($vars);
		return $this->_convertir_preformateado_html($arr);
	}

	function _convertir_tabla_html($arr)
	{
		$html='<table border="1">';
		for ($i=0;$i<count($arr);$i++)
		{
			$html.='<tr>';
			for($j=0;$j<count($arr[$i]);$j++)
			{
				if ($arr[$i][$j]!=='')
				{
					$value=$arr[$i][$j];
					if (is_numeric($value))
					{
						$value=str_replace('.',$this->simbolo_decimal,$value);
					}
					$html.='<td>' . $value. '</td>';
				}
				else
				{
					$html.='<td>&nbsp;</td>';
				}
			}
			$html.='</tr>';
		}
		$html.='</table>';
		return $html;
	}

	function _convertir_preformateado_html($arr)
	{
		$html='<pre>';
		for ($i=0;$i<count($arr);$i++)
		{
			for($j=0;$j<count($arr[$i]);$j++)
			{
			      $value = "";
			      if ($arr[$i][$j]!=='')
			      {
				    $value=$arr[$i][$j];
				    if (is_numeric($value))
				    {
					  $value=str_replace('.',$this->simbolo_decimal, $value);
				    }else if (is_object($value)){
					  $value=$value->valor;
				    }

			      }
			      if ($j != count($arr[$i]) - 1)
				    $html.=$value . $this->separador_columnas;
			      else
				    $html.=$value;

			}
			$html.='&#10;';
		}
		$html.='</pre>';
		return $html;
	}


	/**
	 * Obtiene un array bidimensional con todos los valores de las variables
	 * indicadas
	 * @param array $variables Array de nombres de variables
	 * @return array Array bidimensional en el orden de $variables[$i]
	 */
	function obtener_array($variables)
	{
		$rserve=& $this->_rserve;

		$vars=array();
		foreach ($variables as $name)
		{
			$vars[]=array(
				'name'=>$name,
				'list'=>$rserve->obtener_lista($name),
				'tipo'=>$rserve->obtener_tipo_variable($name),
				);
		}

		$valor=array();
		$col=0;

		foreach ($vars as $v)
		{
			$name=$v['name'];
			$list=$v['list'];
			$tipo=$v['tipo'];


			switch ($tipo)
			{
				case 'matrix':
					$row=0;

					$nrows=$list['attr']['dim'][0];
					$ncols=$list['attr']['dim'][1];

					$valor[$row][$col]='';


					for ($i=0;$i<$ncols;$i++)
					{
						if (isset($list['attr']['dimnames'][1][$i]))
						{
							$colname=$list['attr']['dimnames'][1][$i];
						}
						else
						{
							$colname='col' . ($i + 1);
						}
						$valor[$row][$col + $i + 1]=$colname;
					}

					$row=1;

					for($i=0;$i<$nrows;$i++)
					{
						if (isset($list['attr']['dimnames'][0][$i]))
						{
							$rowname=$list['attr']['dimnames'][0][$i];
						}
						else
						{
							$rowname='col' . ($i + 1);
						}
						$valor[$row + $i][$col]=$rowname;
					}

                    if (isset($list['value'][0])) $k=0;
                    else $k=1;

					for ($m=0;$m<$ncols;$m++)
					{
						for ($j=0;$j<$nrows;$j++)
						{
							$valor[$j + $row][$col + $m + 1]=$list['value'][$k];
							$k++;
						}
					}
					$col += $ncols + 2; //se crea un hueco entre variables
					//$col += $ncols +1;
					break;
				case 'character':
					$valor[0][$col]=$name;
					$i=0;
					foreach ($list['value'] as $v)
					{
						$valor[$i + 1][$col]=$v->valor;
						$i++;
					}
					$col+=2;
					//$col+=1;
					break;					
				case 'numeric':
					$valor[0][$col]=$name;
					$i=0;
					foreach ($list['value'] as $v)
					{
						$valor[$i + 1][$col]=$v;
						$i++;
					}
					$col+=2;
					//$col+=1;
					break;
				case 'array':
					$k=1;

					$nrows=$list['attr']['dim'][0];
					$ncols=$list['attr']['dim'][1];
					$nreps=$list['attr']['dim'][2];

					for ($n=0;$n<$nreps;$n++)
					{
						$row=0;

						if (isset($list['attr']['dimnames'][2][$n]))
						{
							$valor[$row][$col]=$list['attr']['dimnames'][2][$n];
						}
						else
						{
							$valor[$row][$col]='';
						}

						for ($i=0;$i<$ncols;$i++)
						{
							if (isset($list['attr']['dimnames'][1][$i]))
							{
								$colname=$list['attr']['dimnames'][1][$i];
							}
							else
							{
								$colname='col' . ($i + 1);
							}
							$valor[$row][$col + $i + 1]=$colname;
						}

						$row=1;

						for($i=0;$i<$nrows;$i++)
						{
							if (isset($list['attr']['dimnames'][0][$i]))
							{
								$rowname=$list['attr']['dimnames'][0][$i];
							}
							else
							{
								$rowname='col' . ($i + 1);
							}
							$valor[$row + $i][$col]=$rowname;
						}

						for ($m=0;$m<$ncols;$m++)
						{
							for ($j=0;$j<$nrows;$j++)
							{
								$valor[$j + $row][$col + $m + 1]=$list['value'][$k];
								$k++;
							}
						}
						$col += $ncols + 2; //se crea un hueco entre variables
						//$col += $ncols + 1;
					}
					break;
			}
		}
		return $this->_rellenar_huecos($valor);
	}

	/**
	 * Rellena los índices inexistentes en la matriz
	 * @param array $matriz Array bidimensional
	 * @return array Array bidimensional sin huecos
	 */
	function _rellenar_huecos($matriz)
	{
		foreach ($matriz as $i=>$row)
		{
			$max=max(array_keys($row));
			for($j=0;$j<$max;$j++)
			{
				if (!isset($matriz[$i][$j]))
				{
					$matriz[$i][$j]='';
				}
			}
		}
		return $matriz;
	}
}
?>