<?php
class ViewEjecucionesDiario extends AppModel
{
	var $name = 'ViewEjecucionesDiario';
	var $useTable = 'view_ejecuciones_diario';

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Asignatura'
	);
	
	/*
	 * Filtra resultados por fecha
	 * @fini tiene fecha de inicio
	 * @ffin tiene fecha de fin
	 * @fechaini fecha de inicio
	 * @fechafin fecha de fin
	 * @asignatura_id id de la asignatura
	*/
	function filtrar_por_fecha($fini = false, $ffin = false, $fechaini, $fechafin, $asignatura_id)
	{
		$this->recursive = -1;
		$cond=array('ViewEjecucionesDiario.asignatura_id'=>$asignatura_id);
		
		if($fini)
		{
			$anyo = date_format($fechaini, "Y");
			$mes = date_format($fechaini, "m");
			$dia = date_format($fechaini, "d");

			$cond['or']=array
			(
				'ViewEjecucionesDiario.anyo >' => $anyo,
				'and'=>array
				(
					'ViewEjecucionesDiario.anyo' => $anyo, 
					'or'=> array
					(
						'ViewEjecucionesDiario.mes >'=> $mes, 
						'and'=>	array
						(
							'ViewEjecucionesDiario.mes'=> $mes, 
							'ViewEjecucionesDiario.dia >=' => $dia
						)
					)
				)
			);
		}
	
		$data=$this->find('all',array('conditions'=>$cond, 'order'=>array('ViewEjecucionesDiario.anyo','ViewEjecucionesDiario.mes','ViewEjecucionesDiario.dia')));

		$res=array();
		if($ffin)
		{
			$anyof = date_format($fechafin, "Y");
			$mesf = date_format($fechafin, "m");
			$diaf = date_format($fechafin, "d");
			
			foreach ($data as $row)
			{
				$dentrointervalo = false;
				if($row['ViewEjecucionesDiario']['anyo'] <= $anyof)
				{
					if($row['ViewEjecucionesDiario']['anyo'] < $anyof)
						$dentrointervalo = true;
					else if($row['ViewEjecucionesDiario']['mes'] <= $mesf)
						if($row['ViewEjecucionesDiario']['mes'] < $mesf)
							$dentrointervalo = true;
						else if($row['ViewEjecucionesDiario']['dia'] <= $diaf)
							$dentrointervalo = true;
				}
							
				if($dentrointervalo)
				{
					$res[]=$row;
				}
			}
		}
		else
		{
			return $data;
		}
		
		return $res;
	}

}
?>
