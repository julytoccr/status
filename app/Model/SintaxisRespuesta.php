<?php
/**
 * SintaxisRespuesta Model
 * @package models
 */
App::import('Vendor', 'estatus/rserve/estatus_rserve');
App::uses('SintaxisRespuestaForm', 'Form');
class SintaxisRespuesta extends AppModel
{
	var $name = 'SintaxisRespuesta';
	var $displayField = 'nombre';
	var $order='orden';

	var $hasMany = array('Pregunta');

	/**
	 * Ordena los sintaxis según el orden indicado en el array
	 *
	 * @param array $orden Orden de los sintaxis. El array contiene los identificadores de los sintaxis en orden
	 * @return boolean Éxito
	 */
	function __construct(){
		parent::__construct();
		$this->validate = SintaxisRespuestaForm::validation();
	}
	
	function setValidation($action){
		$this->validate = SintaxisRespuestaForm::$action();
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
			$rserve->recuperar_variable('parametro_',0);
			$rserve->recuperar_variable('respuesta_',0);
			$rserve->recuperar_variable('mensaje_','');
			$rserve->evaluar_cache();

			if (! $rserve->evaluar_instrucciones($this->data[$this->name]['expresiones']))
			{
				$this->invalidate('expresiones',__('inv_expresiones_no_validas',array(h($rserve->ultimo_error()))));
			}
		}
		return true;
	}

	function obtener_resumen()
	{
		$this->recursive = -1;
		$sintaxis = $this->find('all');
		$sintaxis_final = array();
		foreach ($sintaxis as $entrada) {
			$sintaxis_final[$entrada['SintaxisRespuesta']['id']] = $entrada['SintaxisRespuesta'];
		}
		return $sintaxis_final;
	}
	
	function ordenar($orden){
		$i=1;
		$error=false;
		foreach ($orden as $id){
			$this->recursive=-1;
			$data=$this->findById($id);
			if ($data){
				$data['SintaxisRespuesta']['orden']=$i;
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
