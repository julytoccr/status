<?php
/**
 * Tablas Estadísticas Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class TablasEstadisticasController extends AppController
{
    var $name = 'TablasEstadisticas';
    var $uses=array('TablasEstadisticas');
    var $helpers=array();
    
    public function beforeFilter(){
		parent::beforeFilter();
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}

	function normal(){
	}

	/**
	 * Cálculo del pvalor de una normal
	 */
	function post_normal(){
		$this->autoRender=false;
		if (!empty($this->request->data)){
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);
			$x=str_replace(',','.',$this->request->data['TablasEstadisticas']['x']);
			$valor=$this->TablasEstadisticas->normal($x);
			$this->_resultado('#resultado_normal',$valor);
		}
	}

	/**
	 * Cálculo del qvalor de una normal
	 */
	function post_normal_inversa()
	{
		$this->autoRender=false;
		if (! empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);
			$p=str_replace(',','.',$this->request->data['TablasEstadisticas']['p']);
			$valor=$this->TablasEstadisticas->normal_inversa($p);
			$this->_resultado('#resultado_normal_inversa',$valor);
		}
	}


	function tstudent()
	{
	}


	/**
	 * Cálculo del pvalor de una tstudent
	 */
	function post_tstudent()
	{
		$this->autoRender=false;
		if (! empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);
			$x=str_replace(',','.',$this->request->data['TablasEstadisticas']['x']);
			$glib=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib']);
			$valor=$this->TablasEstadisticas->tstudent($x,$glib);
			$this->_resultado('#resultado_tstudent',$valor);

		}
	}

	/**
	 * Cálculo del qvalor de una t-Student
	 */
	function post_tstudent_inversa(){
		$this->autoRender=false;
		if (!empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);

			$p=str_replace(',','.',$this->request->data['TablasEstadisticas']['p']);
			$glib=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib']);

			$valor=$this->TablasEstadisticas->tstudent_inversa($p,$glib);
			$this->_resultado('#resultado_tstudent_inversa',$valor);
		}
	}

	function chi2()
	{
	}


	/**
	 * Cálculo del pvalor de una Chi-cuadrado
	 */
	function post_chi2(){
		$this->autoRender=false;
		if (!empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);
			$x=str_replace(',','.',$this->request->data['TablasEstadisticas']['x']);
			$glib=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib']);
			$valor=$this->TablasEstadisticas->chi2($x,$glib);
			$this->_resultado('#resultado_chi2',$valor);
		}
	}

	/**
	 * Cálculo del qvalor de una Chi-cuadrado
	 */
	function post_chi2_inversa()
	{
		$this->autoRender=false;
		if (!empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);

			$p=str_replace(',','.',$this->request->data['TablasEstadisticas']['p']);
			$glib=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib']);

			$valor=$this->TablasEstadisticas->chi2_inversa($p,$glib);
			$this->_resultado('#resultado_chi2_inversa',$valor);
		}
	}

	function fisher(){
	}


	/**
	 * Cálculo del pvalor de una F de Fisher
	 */
	function post_fisher()
	{
		$this->autoRender=false;
		if (! empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);
			$x=str_replace(',','.',$this->request->data['TablasEstadisticas']['x']);
			$glib1=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib1']);
			$glib2=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib2']);
			$valor=$this->TablasEstadisticas->fisher($x,$glib1,$glib2);
			$this->_resultado('#resultado_fisher',$valor);
		}

	}

	/**
	 * Cálculo del qvalor de una F de Fisher
	 */
	function post_fisher_inversa(){
		$this->autoRender=false;
		if (! empty($this->request->data))
		{
			//uses('sanitize');
			//$data=Sanitize::paranoid($this->data['TablasEstadisticas'],$this->_sanitize_numeric);

			$p=str_replace(',','.',$this->request->data['TablasEstadisticas']['p']);
			$glib1=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib1']);
			$glib2=str_replace(',','.',$this->request->data['TablasEstadisticas']['glib2']);

			$valor=$this->TablasEstadisticas->fisher_inversa($p,$glib1,$glib2);
			$this->_resultado('#resultado_fisher_inversa',$valor);
		}
	}


	function _resultado($css,$valor)
	{
		//$this->autoRender=false;
		//$page=& $this->output->returnHelper('Page');
		echo $message="Resultado: $valor";
		//echo $page->replace_html($css,$message);
		//echo $page->effect($css,'highlight');
	}
}
?>
