<?php
/**
 * TablasEstadisticas Model
 * @package models
 */
App::import('Vendor', 'estatus/rserve/estatus_rserve');
class TablasEstadisticas extends AppModel
{
	var $name = 'TablasEstadisticas';

	var $fields = array(
    		'normal'=>array
    		(
				'x'	=> array(	'titulo'=>'Valor X',
								'size'=>10),
			),
    		'normal_inversa'=>array
    		(
				'p'	=> array(	'titulo'=>'p-valor',
								'size'=>10),
			),
    		'tstudent'=>array
    		(
				'x'	=> array(	'titulo'=>'Valor X',
								'size'=>10),
				'glib'	=> array(	'titulo'=>'G.de libertad de la muestra',
									'size'=>10),
			),
    		'tstudent_inversa'=>array
    		(
				'p'	=> array(	'titulo'=>'p-valor',
								'size'=>10),
				'glib'	=> array(	'titulo'=>'G.de libertad de la muestra',
									'size'=>10),
			),
    		'chi2'=>array
    		(
				'x'	=> array(	'titulo'=>'Valor X',
								'size'=>10),
				'glib'	=> array(	'titulo'=>'G.de libertad de la muestra',
									'size'=>10),
			),
    		'chi2_inversa'=>array
    		(
				'p'	=> array(	'titulo'=>'p-valor',
								'size'=>10),
				'glib'	=> array(	'titulo'=>'G.de libertad de la muestra',
									'size'=>10),
			),
    		'fisher'=>array
    		(
				'x'	=> array(	'titulo'=>'Valor X',
								'size'=>10),
				'glib1'	=> array(	'titulo'=>'G.de libertad de la muestra 1',
									'size'=>10),
				'glib2'	=> array(	'titulo'=>'G.de libertad de la muestra 2',
									'size'=>10),

			),
    		'fisher_inversa'=>array
    		(
				'p'	=> array(	'titulo'=>'p-valor',
								'size'=>10),
				'glib1'	=> array(	'titulo'=>'G.de libertad de la muestra 1',
									'size'=>10),
				'glib2'	=> array(	'titulo'=>'G.de libertad de la muestra 2',
									'size'=>10),
			),


	);

	var $useTable=false;

	/**
	 * Envía a R las expresiones y retorna la respuesta
	 * @param string $expresiones Expresiones escritas en R
	 * @return string Resultado
	 */
	function _evaluar($expresiones)
	{
		$rserve= new EstatusRserve();
		$res=$rserve->evaluar($expresiones);
		$valor=$res->imprimir();
		return round($valor,5);
	}

	/**
	 * Obtiene el valor de la función de distribución = P(x ≤ $x )
	 * de la Normal
	 * @param integer $x Cuantil
	 * @return integer Valor probabilístico
	 */
	function normal($x)
	{
		if (! is_numeric($x)) return null;

		return $this->_evaluar("pnorm($x)");
	}

	/**
	 * Es la función inversa de la función de probabilidad
	 * normal_inversa(normal(x))=x
	 * @param integer $p Probabilidad
	 * @return integer Valor X (cuantil)
	 */
	function normal_inversa($p)
	{
		if (! is_numeric($p) || $p<0 || $p>1) return null;
		return $this->_evaluar("qnorm($p)");
	}

	/**
	 * Obtiene el valor de la función de distribución = P(x≤$x)
	 * de la t-Student
	 * @param integer $x Cuantil
	 * @param integer $glib Grados de libertad de la muestra
 	 * @return integer Valor probabilístico
	 */
	function tstudent($x,$n)
	{
		if (! is_numeric($x) || ! is_numeric($n)) return null;

		return $this->_evaluar("pt($x,$n)");
	}


	/**
	 * Obtiene el valor de la inversa de la función de distribución = P(x≤$x)
	 * de la t-Student
	 * @param integer $p p-valor
	 * @param integer $glib Grados de libertad de la muestra
 	 * @return integer Valor X (cuantil)
	 */
	function tstudent_inversa($p,$n)
	{
		if (! is_numeric($p) || ! is_numeric($n)) return null;

		return $this->_evaluar("qt($p,$n)");
	}



	/**
	 * Obtiene el valor de la función de distribución = P(x≤$x)
	 * de la Chi-cuadrado
	 * @param integer $x Cuantil
	 * @param integer $glib Grados de libertad de la muestra
 	 * @return integer Valor probabilístico
	 */
	function chi2($x,$glib)
	{
		if (! is_numeric($x) || ! is_numeric($glib)) return null;

		return $this->_evaluar("pchisq($x,$glib)");
	}


	/**
	 * Obtiene el valor de la inversa de la función de distribución = P(x≤$x)
	 * de la Chi-cuadrado
	 * @param integer $p p-valor
	 * @param integer $glib Grados de libertad de la muestra
 	 * @return integer Valor X (cuantil)
	 */
	function chi2_inversa($p,$glib)
	{
		if (! is_numeric($p) || ! is_numeric($glib)) return null;

		return $this->_evaluar("qchisq($p,$glib)");
	}



	/**
	 * Obtiene el valor de la función de distribución = P(x≤$x)
	 * de la F de Fisher
	 * @param integer $x Cuantil
	 * @param integer $glib1 Grados libertad numerador
	 * @param integer $glib2 Grados libertad denominador
 	 * @return integer Valor probabilístico
	 */
	function fisher($x,$glib1,$glib2)
	{
		if (! is_numeric($x)
		|| ! is_numeric($glib1)
		|| ! is_numeric($glib2)) return null;

		return $this->_evaluar("pf($x,$glib1,$glib2)");
	}


	/**
	 * Obtiene el valor de la inversa de la función de distribución = P(x≤$x)
	 * de la F de Fisher
	 * @param integer $p p-valor
	 * @param integer $glib1 Grados libertad numerador
	 * @param integer $glib2 Grados libertad denominador
 	 * @return integer Valor X (cuantil)
	 */
	function fisher_inversa($p,$glib1,$glib2)
	{
		if (! is_numeric($p)
		|| ! is_numeric($glib1)
		|| ! is_numeric($glib2)) return null;


		return $this->_evaluar("qf($p,$glib1,$glib2)");
	}
}
?>
