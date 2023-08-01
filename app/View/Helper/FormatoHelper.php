<?php

class FormatoHelper extends AppHelper
{
	function euros($number)
	{
		return number_format($number, 2, ',', '.') . ' EUR';
	}

	function fecha($strtime)
	{
		 $time=strtotime($strtime);
		 if (!$time)
		 {
		 	return '(no restringido)'; //tiempo no valido
		 }
		 return strftime("%d/%m/%Y",$time);
	}

	function fecha_hora($strtime)
	{
		 $time=strtotime($strtime);
		 if (!$time)
		 {
		 	return '(no restringido)'; //tiempo no valido
		 }
		 return strftime("%d/%m/%Y %H:%M",$time);
	}

	function texto_multilinea($cadena)
	{
		return str_replace("\n","<br/>",$cadena);
	}

	function booleano($bool)
	{
		if ($bool) return 'Sí';
		else return 'No';
	}

	// To avoid HTML escaping
	function html($html)
	{
		return $html;
	}
	
	function grupo($value){
		if($value===null)
			return '(todos)';
		else
			return $value;
	}
}
?>
