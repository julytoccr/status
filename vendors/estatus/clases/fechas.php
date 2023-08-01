<?php
/**
 * IntervaloFechas
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */
//TODO: Hacer pruebas exhaustivas
class IntervaloFechas extends Object
{
	/**
	 * Devuelve cierto si intervalo1 está incluido en el intervalo2
	 * @param array $intervalo1 (fecha_ini,fecha_fin) cada fecha en timestamp
	 * @param array $intervalo2 (fecha_ini,fecha_fin) cada fecha en timestamp
	 * @return boolean el $intervalo1 está incluido en el $intervalo2
	 */
	function incluido($intervalo1 , $intervalo2)
	{
		return
		( 	($intervalo1[0]!=null && $intervalo1[0]>$intervalo2[0])
			|| $intervalo2[0]==null)
		&&
		(	($intervalo1[1]!=null && $intervalo1[1]<$intervalo2[1])
			|| $intervalo2[1]==null);
	}

	/**
	 * Devuelve cierto si intervalo1 y intervalo2 están contiguos (el orden no importa)
	 * @param array $intervalo1 (fecha_ini,fecha_fin) cada fecha en timestamp
	 * @param array $intervalo2 (fecha_ini,fecha_fin) cada fecha en timestamp
	 *
	 * @return array Retorna el intervalo resultante si son contiguos o false
	 */
	function contiguo($intervalo1, $intervalo2)
	{
		if ($intervalo1[0]>$intervalo2[0])
		{
			//intercambiar
			$aux=$intervalo1;
			$intervalo1=$intervalo2;
			$intervalo2=$aux;
		}
		if (
				(
					//Empieza antes que el segundo
					$intervalo1[0]==null || $intervalo1[0]<$intervalo2[0]
				)
				&&
				(
					$intervalo1[1]==null //Acaba después que el segundo( lo incluye)
					||
					(
						//Termina despues de empezar el otro
						($intervalo1[1]>$intervalo2[0] || $intervalo2[0]==null)
						&&
						//Termina antes de terminar el otro
						($intervalo1[1]<$intervalo2[1] || $intervalo2[1]==null)
					)
				)
			)
		{
			return array($intervalo1[0],$intervalo2[1]);
		}
		else
		{
			return false;
		}
	}
}

?>
