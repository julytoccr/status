<?php
class ViewLoginAlumnosDiario extends AppModel
{
	var $name = 'ViewLoginAlumnosDiario';
	var $useTable = 'view_login_alumnos_diario';

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
		$cond=array('ViewLoginAlumnosDiario.asignatura_id'=>$asignatura_id);
		
		if($fini)
		{
			$anyo = date_format($fechaini, "Y");
			$mes = date_format($fechaini, "m");
			$dia = date_format($fechaini, "d");

			$cond['OR']=array
			(
				'ViewLoginAlumnosDiario.anyo >' => $anyo,
				'AND'=>array
				(
					'ViewLoginAlumnosDiario.anyo' => $anyo, 
					'OR'=> array
					(
						'ViewLoginAlumnosDiario.mes >'=> $mes, 
						'AND'=>	array
						(
							'ViewLoginAlumnosDiario.mes'=> $mes, 
							'ViewLoginAlumnosDiario.dia >=' => $dia
						)
					)
				)
			);
		}

		$data=$this->find('all',array('conditions'=>$cond,'order'=>array('ViewLoginAlumnosDiario.anyo','ViewLoginAlumnosDiario.mes','ViewLoginAlumnosDiario.dia')));
		$res=array();
		if($ffin)
		{
			$anyof = date_format($fechafin, "Y");
			$mesf = date_format($fechafin, "m");
			$diaf = date_format($fechafin, "d");
			
			foreach ($data as $row)
			{
				$dentrointervalo = false;
				if($row['ViewLoginAlumnosDiario']['anyo'] <= $anyof)
				{
					if($row['ViewLoginAlumnosDiario']['anyo'] < $anyof)
						$dentrointervalo = true;
					else if($row['ViewLoginAlumnosDiario']['mes'] <= $mesf)
						if($row['ViewLoginAlumnosDiario']['mes'] < $mesf)
							$dentrointervalo = true;
						else if($row['ViewLoginAlumnosDiario']['dia'] <= $diaf)
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
