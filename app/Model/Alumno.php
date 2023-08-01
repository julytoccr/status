<?php
/**
 * Alumno Model
 * @package models
 */
App::uses('Bloque', 'Model');
App::uses('Asignatura', 'Model');

class Alumno extends AppModel
{
	var $name = 'Alumno';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Grupo',
			'Usuario'
	);

	var $hasMany = array(
			'Ejecucion',
			'ViewPuntuacionBloque',
			'ViewPuntuacionesProblemasAlumno',

	);
	var $hasOne=array(
			'EstAlumno',
			'ViewPuntuacionesProblemasAlumno',
			'EstRankingPuntuacion',
			'EstRankingEjecucion',
			'EstRankingNota',
			'ViewAlumnosUsuario',
	);


	/**
	 * Devuelve el grupo de un alumno
	 *
	 * @param integer $id Alumno
	 *
	 * return integer $grupo_id Identificador de grupo del alumno
	 */
	function grupo($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=-1;
		$data=$this->findById($id);
		if (!$data) return null;

		return $data['Alumno']['grupo_id'];
	}
	/**
	 * Devuelve la asignatura de un alumno
	 *
	 * @param integer $id Alumno
	 *
	 * return integer $asignatura_id Identificador de asignatura del alumno
	 */
	function asignatura($id=null)
	{
		if ($id===null) $id=$this->id;
		$this->recursive=0;
		$this->contain('Grupo');
		$data=$this->findById($id);
		if (!$data) return null;
		return $data['Grupo']['asignatura_id'];
	}


	 /**
	  * Estadísticos totales de un alumno
	  * @param integer $id Alumno
	  * @return array Array asociativo con los estadísticos
	  *
	  * @see function obtener_puntuacion_y_media_alumno($idasig,$idalumno)
	  * @see estadisticas_alumno
	  */
	function estadisticas($id=null)
	{
	 	if ($id===null) $id=$this->id;
	 	/*$models=array(
			'EstAlumno',
	 		'EstRankingPuntuacion',
	 		'EstRankingEjecucion',
	 		'EstRankingNota'
	 	);
	 	$data=array();
	 	foreach ($models as $m)
	 	{
	 		$this->$m->recursive=-1;
	 		$data=am($data,$this->$m->find(array("$m.alumno_id"=>$id)));
	 	}
	 	*/
		$asi = $this->asignatura($id);
		$this->ViewPuntuacionesProblemasAlumno->recursive=-1;
		$punts = $this->ViewPuntuacionesProblemasAlumno->find('all',array('conditions'=>array('ViewPuntuacionesProblemasAlumno.asignatura_id'=>$asi)));
		
		$nota_media = array();
		$puntuacion = array();
		$ejecuciones = array();
		$tiempo = array();
		$cuenta_tiempos = array();
		//$tiempo_medio = array();
		$factor = array();
		$ejecutados = 0;
		$anterior_alum = false;
		foreach($punts as $p)
		{
			$alum = $p['ViewPuntuacionesProblemasAlumno']['alumno_id'];
			if($alum != $anterior_alum)
			{
				$nota_media[$alum] = 0;
				$puntuacion[$alum] = 0;
				$ejecuciones[$alum] = 0;
				$tiempo[$alum] = 0;
				$cuenta_tiempos[$alum] = 0;
				$factor[$alum] = 0;
			}
			if($alum == $id)
				$ejecutados++;
			$puntuacion[$alum] += $p['ViewPuntuacionesProblemasAlumno']['puntuacion_problema'];
			$nota_media[$alum] += $p['ViewPuntuacionesProblemasAlumno']['nota_media_problema'] * $p['ViewPuntuacionesProblemasAlumno']['factor'];
			$ejecuciones[$alum] += $p['ViewPuntuacionesProblemasAlumno']['num_ejecuciones_problema'];
			//$tiempo[$alum] += $p['ViewPuntuacionesProblemasAlumno']['tiempo_total'];
			//$cuenta_tiempos[$alum] += $p['ViewPuntuacionesProblemasAlumno']['cuenta_tiempos'];
			$factor[$alum] += $p['ViewPuntuacionesProblemasAlumno']['factor'];
			
			$anterior_alum = $alum;
		}
		
		foreach($nota_media as $alum=>$nm)
		{
			$nota_media[$alum] = $nm / $factor[$alum];
			//$tiempo_medio[$alum] = $tiempo[$alum]/$ejecuciones[$alum];
		}
		
		arsort($nota_media, SORT_NUMERIC);
		arsort($puntuacion, SORT_NUMERIC);
		arsort($ejecuciones, SORT_NUMERIC);
		//arsort($tiempo, SORT_NUMERIC);
		//arsort($tiempo_medio, SORT_NUMERIC);
		
		$alum = -1;
		$estad = array('num_problemas_ejecutados'=>$ejecutados, 'puntuacion_total'=>array_key_exists($id, $puntuacion) ? $puntuacion[$id] : 0, 
			'num_ejecuciones'=>array_key_exists($id, $ejecuciones) ? $ejecuciones[$id] : 0,
			'nota_media'=>array_key_exists($id, $nota_media) ? $nota_media[$id] : 0,
			'tiempo_medio'=>(array_key_exists($id, $tiempo) && $cuenta_tiempos[$id] > 0) ? $tiempo[$id]/$cuenta_tiempos[$id] : 0,
			'tiempo_total'=>array_key_exists($id, $tiempo) ? $tiempo[$id] : 0,
			'posicion_ranking_nota' => 0, 'posicion_ranking_puntuacion'=>0, 'posicion_ranking_ejecuciones'=>0, 'posicion_ranking_tiempo'=>0);
		reset($nota_media);
		reset($puntuacion);
		reset($ejecuciones);
		//end($tiempo); // el ranking de tiempo va de menos tiempo a mas, no de mas a menos -> empezamos por el final
		//end($tiempo_medio);
		$end = false;
		$end_nota = false;
		$end_ejec = false;
		//$end_tiempo = false;
		//$end_tiempo_medio = false;
		//$total_alumnos = count($tiempo);
		$end_punt = false;
		for($i = 0; $end == false; $i++)
		{
			if(key($nota_media) == $id)
			{
				$end_nota = true;
				$estad['posicion_ranking_nota'] = $i + 1;
			}
			if(next($nota_media) == false)
				$end_nota = true;
			
			if(key($puntuacion) == $id)
			{
				$end_punt = true;				
				$estad['posicion_ranking_puntuacion'] = $i + 1;
			}
			if(next($puntuacion) == false)
				$end_punt = true;
			
			//if(key($tiempo) == $id)
			//{
			//	$end_tiempo = true;
			//	$estad['posicion_ranking_tiempo'] = $i + 1;
			//}			
			//if(prev($tiempo) == false)
			//	$end_tiempo = true;
			
			//if(key($tiempo_medio) == $id)
			//{
			//	$end_tiempo_medio = true;
			//	$estad['posicion_ranking_tiempo_medio'] = $i + 1;
			//}			
			//if(prev($tiempo_medio) == false)
			//	$end_tiempo_medio = true;

			if(key($ejecuciones) == $id)
			{
				$end_ejec = true;
				$estad['posicion_ranking_ejecuciones'] = $i + 1;
			}			
			if(next($ejecuciones) == false)
				$end_ejec = true;
			
			$end = $end_punt && $end_nota && $end_ejec;
		}

	 	//$this->unbindAll(array('hasOne'=>$models));
	 	$this->recursive=-1;
	 	$estad=am($estad,$this->findById($id));

	 	/*$estadisticas=array();
	 	foreach ($models as $name)
	 	{
	 		if (isset($data[$name]))
	 		{
	 			$estadisticas=am($estadisticas,$data[$name]);
	 		}
	 	}*/
	 	return $estad;
	}


	 /**
	  * Estadísticos totales de todos los alumnos de un grupo
	  * @param integer $asignatura_id Asignatura
	  * @param string $grupo Grupo
	  * @param array $estadisticas Array de modelos de estadísticas a obtener
	  *
	  * @return array
	  * 	$result[$i]['Alumno']['id']
	  * 	$result[$i]['Estadisticas']['nota_media']
	  */
	function estadisticas_alumnos_grupo($asignatura_id, $grupo,$estadisticas=array('EstAlumno','EstRankingPuntuacion','EstRankingEjecucion','EstRankingNota'))
	{
	 	$grupo_id=$this->Grupo->buscar($asignatura_id,$grupo);

	 	$this->contain('Usuario',$estadisticas);
	 	$this->recursive=1;

	 	$data=$this->find('all', array('conditions'=>array('Alumno.grupo_id'=>$grupo_id)));

	 	foreach ($data as $i=>$row)
	 	{
	 		$data[$i]['Estadisticas']=array();
		 	foreach ($estadisticas as $name)
		 	{
		 		if (isset($row[$name]))
		 		{
		 			$data[$i]['Estadisticas']=am($data[$i]['Estadisticas'],$row[$name]);
		 			unset($data[$i][$name]);
		 		}
		 	}
	 	}
	 	return $data;
	}

	 /**
	  * Estadísticos totales de todos los alumnos de una asignatura
	  * @param integer $asignatura_id Asignatura
	  * @param array $estadisticas Array de modelos de estadísticas a obtener
	  *
	  * @return array
	  * 	$result[$i]['Alumno']['id']
	  * 	$result[$i]['Estadisticas']['nota_media']
	  */
	function estadisticas_alumnos_asignatura($asignatura_id, $estadisticas=array('EstAlumno','EstRankingPuntuacion','EstRankingEjecucion','EstRankingNota'))
	{
	 	$this->contain('Usuario','Grupo',$estadisticas);
	 	$this->recursive=1;

	 	$data=$this->find('all', array('conditions'=>array('Grupo.asignatura_id'=>$asignatura_id)));
	 	foreach ($data as $i=>$row)
	 	{
	 		$data[$i]['Estadisticas']=array();
		 	foreach ($estadisticas as $name)
		 	{
		 		if (isset($row[$name]))
		 		{
		 			$data[$i]['Estadisticas']=am($data[$i]['Estadisticas'],$row[$name]);
		 			unset($data[$i][$name]);
		 		}
		 	}
	 	}
	 	return $data;
	}



	/**
	 * Devuelve un array con todas las asignaturas del usuario
	 *
	 * @param string $usuario_id Usuario
	 * @param boolean $solo_activas Devolver únicamente las activas según la fecha actual
	 * @return array
	 * 	$result[$i]['id'] Asignatura
	 */
	function asignaturas($usuario_id,$solo_activas=false)
	{
		$this->Asignatura = new Asignatura();

		$data= $this->ViewAlumnosUsuario->alumnos_usuario($usuario_id);
		$asignaturas=array();
		foreach ($data as $row)
		{
			if ($solo_activas==false || $this->Asignatura->es_activa($row['Asignatura']))
			{
				$asignaturas[]=$row['Asignatura'];
			}
		}
		return $asignaturas;
 	}

	/**
	 * Devuelve el identificador de alumno para un
	 * usuario matriculado en una asignatura
	 *
	 * @param integer $usuario_id Usuario
	 * @param integer $asignatura_id Asignatura
	 *
	 * @return integer $alumno_id o null
	 */
	function buscar($usuario_id, $asignatura_id)
	{
		$this->recursive=1;
		$this->contain('Grupo');
		//$this->setConditions(array('Grupo.asignatura_id'=>$asignatura_id),'Grupo','belongsTo');
		$data=$this->find('first', array('conditions'=>array('Alumno.usuario_id'=>$usuario_id, 'Grupo.asignatura_id'=>$asignatura_id)));
		if (! $data) return null;
		return $data['Alumno']['id'];
	}

	/**
	 * Modifica los datos de alumno / usuario
	 * @param integer $alumno_id Alumno
	 * @param array $usuario Datos del usuario ($usuario['login'])
	 * @param string  $grupo Nombre del grupo de la asignatura del alumno
	 * @param boolean $repetidor Es repetidor
	 *
	 * @return boolean Éxito
	 *
	 */
	function modificar($data)
	{
		$alumno_id = $data['Alumno']['id'];
		$grupo_nombre = $data['Grupo']['nombre'];
		$repetidor = $data['Alumno']['repetidor'];
		$usuario = $data['Usuario'];

		$this->recursive=0;
		$this->contain('Usuario','Grupo');
		$data_actual=$this->findById($alumno_id);
		
		if (!$data_actual)
		{
			$this->invalidate('_modificar',__('inv_alumno_no_existe'));
			return false;
		}

		$usuario=am($data_actual['Usuario'],$usuario);
		if (!$this->Usuario->guardar($usuario))
		{
			$this->invalidate('_modificar',__('inv_usuario_no_guardado'));
			return false;
		}
		
		if ($data_actual['Grupo']['nombre']!=$grupo_nombre)
		{
			/*
			 * el alumno se ha cambiado de grupo.
			 * tras guardar el alumno es necesario comprobar si el grupo
			 * ha quedado vacío para eliminarlo
			 */
			$asignatura_id=$data_actual['Grupo']['asignatura_id'];
			$data_actual['Alumno']['grupo_id']=$this->Grupo->buscar_crear($asignatura_id,$grupo_nombre);
		}
		
		$data_actual['Alumno']['repetidor'] = $repetidor;
		if (!$this->save($data_actual)) return false;

		if ($data_actual['Grupo']['nombre']!=$grupo_nombre && !$this->Grupo->eliminar_si_vacio($data_actual['Grupo']['id']))
		{
			//eliminar el grupo antiguo si vacío
			return false;
		}
		return true;
	}

	function nombre($usuario_id)
	{
		$data = $this->Usuario->findById($usuario_id);
		return $data['Usuario']['nombre'];	
	}
	
	function apellidos($usuario_id)
	{
		$data = $this->Usuario->findById($usuario_id);
		
		return $data['Usuario']['apellidos'];
	}
	
	/**
	 * Crea un nuevo alumno. El alumno no debe existir.
	 * Crea el usuario automáticamente si no existe. Si ya existe, no se modifican los datos de usuario
	 * Crea el grupo automáticamente si no existe.
	 *
	 * @param array $usuario Datos del usuario ($usuario['Usuario']['login'])
	 * @param integer $asignatura_id Asignatura
	 * @param string  $grupo Nombre del grupo de la asignatura
	 * @param boolean $repetidor Es repetidor
	 *
	 * @return integer Devuelve $alumno_id si éxito, de lo contrario null
	 */
	function crear($usuario,$asignatura_id,$grupo,$repetidor)
	{
		$usuario_id=$this->Usuario->buscar($usuario['login']);
		if ($usuario_id)
		{
			if ($this->buscar($usuario_id,$asignatura_id))
			{
				$this->invalidate('_crear',__('inv_alumno_existe_asignatura'));
				return -1;
			}
		}
		// si es descomenta el troç de codi, en cas d'un usuari ja existent no es podrà canviar de tipus de login
		//if (! $usuario_id=$this->Usuario->buscar($usuario['login']))
		//{
            $usuario_id=$this->Usuario->guardar($usuario, $asignatura_id);
			if (! $usuario_id)
			{
				$this->invalidate('_crear',__('inv_usuario_no_creado'));
				return null;
			}
		//}
		$data['usuario_id']=$usuario_id;
		$data['repetidor']=$repetidor;

		if (!$grupo_id=$this->Grupo->buscar_crear($asignatura_id,$grupo))
		{
			$this->invalidate('_crear',__('inv_grupo_no_creado'));
			return null;
		}
		$data['grupo_id']=$grupo_id;
		$data['alias']='Alumno';
		$data['avatar']=null;
		$this->create();
		if ($this->save($data))
		{
			return $this->getLastInsertID();
		}
		return null;
	}

	function beforeDelete()
	{
		$this->recursive=-1;
		$this->data=$this->findById($this->id);
		return true;
	}
	function afterDelete()
	{
		$usuario_id = $this->data['Alumno']['usuario_id'];
		$grupo_id = $this->data['Alumno']['grupo_id'];
		if (! $this->Usuario->roles($usuario_id))
		{	//el usuario ya no tiene roles, eliminar
			$this->Usuario->delete($usuario_id);
		}
		$this->Grupo->eliminar_si_vacio($grupo_id);
	}

	function problemas($alumno_id, $asignatura_id){
		if(! $asignatura_id)
			return array();
			
		$asignatura = new Asignatura();
		$bloque = new Bloque();
		
		$grupo_id = $this->grupo($alumno_id);
		$grupo = $this->Grupo->nombre_grupo($grupo_id);

		$this->ViewPuntuacionesProblemasAlumno->recursive = -1;

		$tmp = $this->ViewPuntuacionesProblemasAlumno->find('all', array(
			'conditions'=>array('ViewPuntuacionesProblemasAlumno.alumno_id'=>$alumno_id),
			'fields'=>array('problema_id','puntuacion_problema','nota_media_problema','num_ejecuciones_problema'))
		);

		$estadisticas = array();
		foreach($tmp as $row){
			$row = $row['ViewPuntuacionesProblemasAlumno'];
			$estadisticas[$row['problema_id']] = $row;
		}

		$data = $asignatura->bloques($asignatura_id,$grupo);

		for ($i=0;$i<count($data);$i++)
		{
			$data[$i]['Problema'] = $bloque->problemas($data[$i]['Bloque']['id'],true);
			foreach ($data[$i]['Problema'] as $j=>$prob)
			{
				if (isset($estadisticas[$prob['Problema']['id']]))
				{
					$data[$i]['Problema'][$j]['Estadisticas'] = $estadisticas[$prob['Problema']['id']];
				}
			}
		}
		return $data;
	}

}
?>
