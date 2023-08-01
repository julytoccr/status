<?php
/**
 * Programa Model
 * @package models
 */
App::import('Vendor','estatus/rserve/estatus_rserve');

class Programa extends AppModel
{
	var $name = 'Programa';
	var $displayField='nombre';

	/**
	 * Comprueba si las expresiones son válidas
	 */
	function beforeValidate()
	{
		if (isset($this->data[$this->name]['expresiones']))
		{
			$rserve = new EstatusRserve();

			$rserve->cache=true;
			for ($i=1;$i<10;$i++)
			{
				$rserve->recuperar_variable("param{$i}_",0);
			}
			$rserve->evaluar_cache();

			$ret=$rserve->evaluar_instrucciones($this->data[$this->name]['expresiones'],true);
			if (! $ret)
			{
				$this->invalidate('expresiones',__('inv_expresiones_no_validas',array(h($rserve->ultimo_error()))));
			}
		}
		return true;
	}


	/**
	 * Evalúa un programa en una cierta sesión rserve
	 * @param integer $id Problema
	 * @param array $parametros Array de parámetros $parametros[0],$parametros[1]...
	 * @param Object $rserve Sesión de Rserve
	 *
	 * @return array
	 * 	$result['mensajes']=Strings retornados por la ejecución
	 *  $result['grafico']=Gráfico retornado por la ejecución
	 */
	function evaluar_programa($id , $parametros,&$rserve)
	{
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return false;

		$rserve->cache=true;
		for ($i=0;$i<10;$i++)
		{
			if (isset($parametros[$i]))
			{
				$param=$parametros[$i];
			}
			else
			{
				$param=0;
			}
			$j=$i+1;
			$rserve->recuperar_variable("param{$j}_",$param);
		}
		$rserve->evaluar_cache();

		$expresiones=str_replace("\r",'',$data['Programa']['expresiones']);
		$array_exp=split("\n",$expresiones);

		$rserve->evaluar("estatus.init_salida()");
		$salida=array();

		for ($i=0;$i<count($array_exp);$i++)
		{
			$exp=$array_exp[$i];
			if ($exp)
			{
				/*
				//$ret=$rserve->evaluar($array_exp[$i]);
				$exp=preg_replace('/;$/','',$exp);
				$rserve->evaluar("temp___=$exp; capture.output(temp___)");
				$output=$rserve->obtener_HTML_variable2('temp___');
				if ($output) $salida[]=print_r($output,true);*/

				$rserve->evaluar($exp);

			}
		}
		$ret=$rserve->evaluar("estatus.get_salida()");
		$content=$rserve->obtener_fichero($ret->imprimir());
		$salida_tmp=split("\n",$content);
		unset($salida_tmp[count($salida_tmp)-1]);
		unset($salida_tmp[0]);
		unset($salida_tmp[1]);

		$salida=array();
		foreach ($salida_tmp as $line)
		{
			$salida[]=preg_replace('/^(\d+) /','',$line);
		}
		//obtener las imagenes
		if (in_array('grafico',$rserve->obtener_lista_variables()))
		{
			$grafico=$rserve->obtener_HTML_variable2('grafico');
		}
		else
		{
			$grafico='';
		}
		return (array('grafico'=>$grafico,'mensajes'=>$salida));
	}
	
	function ordenar($orden){
		$i=1;
		$error=false;
		foreach ($orden as $id){
			$this->recursive=-1;
			$data=$this->findById($id);
			if ($data){
				$data['Programa']['orden']=$i;
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
