<?php

/**
 * Convierte un array a XML
 *
 * @param array $array Datos tipo findAll
 * @param string $model Nombre del modelo principal del array
 * @param string $root Nombre de la etiqueta root del XML
 * @return string XML
 */
function array2xml($array,$model,$root)
{
	$converter= new ArrayToXmlConverter;
	return $converter->to_xml($array,$model,$root);
}

class ArrayToXmlConverter
{

	/**
	 * @param $model Model name
	 * @param $root Name of root
	 */
	function to_xml($data,$model,$root=null)
	{
		ob_start();
		if (! $root)
		{
			$root="{$model}_data";
		}
		if (count($data)==0)
		{
			echo "<$root></$root>\n";
		}
		else
		{
			echo "<$root>";
			if (isset($data[0]))
			{
				//simple array
				foreach ($data as $row)
				{
					$this->convert_row($row,$model);
				}
			}
			else
			{
				//assoc array
				$this->convert_row($data,$model);
			}
			echo "</$root>\n";
		}
		return ob_get_clean();
	}

	/**
	 * Like htmlentities
	 * XML Entity Mandatory Escape Characters
	 * @param string $string
	 * @return string
	 */
	function xmlentities($string)
	{
	   return str_replace ( array ( '&', '"', "'", '<', '>', '�' ), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;', '&apos;' ), $string );
	}

	function convert_array($data)
	{
		foreach ($data as $name=>$value)
		{
			echo "<$name>";
			if (is_array($value))
			{
				echo $this->convert_array($value);
			}
			else echo $this->xmlentities($value);
			echo "</$name>\n";
		}
	}

	function convert_row($data,$root,$need_root=true)
	{
		if (isset($data[$root]))
		{
			echo "<$root>";
			foreach ($data[$root] as $name=>$value)
			{
				if (is_numeric($name))
				{
					$etiq_name="${root}_${name}";

				}
				else $etiq_name=$name;

				echo "<$etiq_name>";

				if (is_array($value))
				{
					echo $this->convert_array($value);
				}
				else
				{
					echo $this->xmlentities($value);
				}
				echo "</$etiq_name>\n";
			}
			echo "</$root>\n";

			foreach ($data as $name=>$array)
			{
				if ($name != $root)
				{
					$this->convert_row(array($name=>$array),$name);
				}
			}

		}
	}
}