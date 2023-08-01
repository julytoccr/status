<?php
/**
 * TiposRespuesta Model
 * @package models
 */
App::import('Vendor', 'estatus/rserve/estatus_rserve');
App::uses('TipoRespuestaForm', 'Form');
class TiposRespuesta extends AppModel
{
	var $name = 'TiposRespuesta';
	var $order = 'orden';
	var $displayField = 'nombre';

	function __construct(){
		parent::__construct();
		$this->validate = TipoRespuestaForm::validation();
	}
	
	function setValidation($action){
		$this->validate = TipoRespuestaForm::$action();
	}
	/**
	 * Comprueba si las expresiones son válidas
	 */
	function beforeValidate()
	{
		if (isset($this->data[$this->name]['expresiones']))
		{
			$rserve = new EstatusRserve();

			$rserve->cache=true;
			$rserve->recuperar_variable('solucion_',0);
			$rserve->recuperar_variable('respuesta_',0);
			$rserve->recuperar_variable('respuestas_',array_fill(0,100,0));
			$rserve->recuperar_variable('resultados_',array_fill(0,100,0));
			$rserve->recuperar_variable('chances_', 0);
			$rserve->recuperar_variable('parametro_',0);
			$rserve->recuperar_variable('num_pregunta_',50);
			$rserve->recuperar_variable('mensaje_','');
			$rserve->recuperar_variable('resultado_','');
			$rserve->evaluar_cache();

			if (! $rserve->evaluar_instrucciones($this->data[$this->name]['expresiones']))
			{
				$this->invalidate('expresiones',__('inv_expresiones_no_validas',array($rserve->ultimo_error())));
			}
		}
		return true;
	}
	
	function ordenar($orden){
		$i=1;
		$error=false;
		foreach ($orden as $id){
			$this->recursive=-1;
			$data=$this->findById($id);
			if ($data){
				$data['TiposRespuesta']['orden']=$i;
				$ret=$this->save($data);
				if (!$ret) $error=true;
				$i++;
			}
			else $error=true;
		}
		return ! $error;
	}
}
?>
