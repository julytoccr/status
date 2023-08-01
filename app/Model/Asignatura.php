<?php
/**
 * Asignatura Model
 * @package models
 */
App::uses('AsignaturaForm', 'Form');
class Asignatura extends AppModel
{
	var $name = 'Asignatura';
	public $actsAs = array('Containable');
	var $displayField='nombre';

	var $hasMany = array(
		'Bloque' => array('order' => 'Bloque.orden ASC', 'dependent' => true),
		'Alumno',
		'Grupo',
		'ViewProblemasAsignatura',
		'ViewAlumnosUsuario',
	);

	var $hasAndBelongsToMany = array(
		'Profesor' => array(
			'joinTable' => 'docencia',
			'foreignKey' => 'asignatura_id',
			'associationForeignKey' => 'profesor_id'
		),
	);

	var $hasOne=array(
		'ViewPuntuacionesProblemasAlumno',
		'ViewNotaMediaEjecucionesAsignatura',
		'ViewNumeroEjecucionesAsignatura',
		'ViewNumeroEjecucionesAprobadasAsignatura',
		'ViewNumeroProblemasDisponiblesAsignatura',
		'ViewNumeroProblemasEjecutadosAsignatura',
	);

	function __construct(){
		parent::__construct();
		$this->validate = AsignaturaForm::validation();
	}

	/**
	 * Devuelve los grupos de una asignatura
	 *
	 * @param integer $id Asignatura
	 * @return array de nombres de grupo
	 */
	function grupos($id=null)
	{
		if ($id===null) $id=$this->id;
		return $this->Grupo->grupos_asignatura($id);
	}
	
	function ids_grupos($id=null)
	{
		if ($id===null) $id=$this->id;
		$recursive = -1;
		return $this->Grupo->find('all',array('conditions'=>array('Grupo.asignatura_id'=>$id)));
	}

	function ranking_ejecuciones($id=null, $nombreyapellidos = false)
	{
		if ($id===null) $id=$this->id;
	 	
		$asi = $id;
		$this->ViewPuntuacionesProblemasAlumno->recursive=-1;
		$punts = $this->ViewPuntuacionesProblemasAlumno->find('all',array('conditions'=>array('ViewPuntuacionesProblemasAlumno.asignatura_id'=>$asi)));
		
		$ejecuciones = array();
		
		$ejecutados = 0;
		$anterior_alum = false;
		foreach($punts as $p)
		{
			$alum = $p['ViewPuntuacionesProblemasAlumno']['alumno_id'];

			if($alum != $anterior_alum)
			{
				$ejecuciones[$alum] = 0;
				$alumns[$alum]['alias'] = $p['ViewPuntuacionesProblemasAlumno']['alias'];
				$alumns[$alum]['avatar'] = $p['ViewPuntuacionesProblemasAlumno']['avatar'];
				
				if($nombreyapellidos == true)
				{
					$alumns[$alum]['nombre'] = $this->Alumno->nombre($p['ViewPuntuacionesProblemasAlumno']['usuario_id']);		
					$alumns[$alum]['apellidos'] = $this->Alumno->apellidos($p['ViewPuntuacionesProblemasAlumno']['usuario_id']);
				}
			}

			$ejecuciones[$alum] += $p['ViewPuntuacionesProblemasAlumno']['num_ejecuciones_problema'];
			
			$anterior_alum = $alum;
		}
		
		arsort($ejecuciones, SORT_NUMERIC);
		
		$alum = -1;
		$estad = array();
		reset($ejecuciones);
		
		$i = 1;
		$end_ejec = false;
		foreach($ejecuciones as $k=>$eje) //$i = 0; $end_ejec == false; $i++)
		{
			if($nombreyapellidos == false)
				$estad[]= array('Alumno'=>array('alias'=> $alumns[$k]['alias'],'avatar' => $alumns[$k]['avatar']), 'EstAlumno'=>array('alumno_id'=> $k, 'num_ejecuciones' => $eje), 'EstRankingEjecucion'=> array('posicion_ranking_ejecuciones' => $i));
			else
				$estad[]= array('Alumno'=>array('id'=> $k, 'alias'=> $alumns[$k]['alias'],'avatar' => $alumns[$k]['avatar']), 'Usuario'=>array('nombre' => $alumns[$k]['nombre'], 'apellidos' => $alumns[$k]['apellidos']), 'EstAlumno'=>array('alumno_id'=> $k, 'num_ejecuciones' => $eje), 'EstRankingEjecucion'=> array('posicion_ranking_ejecuciones' => $i));
			$i++;
		}
	
	 	return $estad;
	}
	
	function ranking_nota($id=null, $nombreyapellidos = false)
	{
		if ($id===null) $id=$this->id;
	 	
		$asi = $id;
		$this->ViewPuntuacionesProblemasAlumno->recursive=-1;
		$punts = $this->ViewPuntuacionesProblemasAlumno->find('all',array('conditions'=>array('ViewPuntuacionesProblemasAlumno.asignatura_id'=>$asi)));
		
		$nota = array();
		$anterior_alum = false;
		$factor = array();
		foreach($punts as $p)
		{
			$alum = $p['ViewPuntuacionesProblemasAlumno']['alumno_id'];
			if($alum != $anterior_alum)
			{
				$nota[$alum] = 0;
				$factor[$alum] = 0;
				$alumns[$alum]['alias'] = $p['ViewPuntuacionesProblemasAlumno']['alias'];
				$alumns[$alum]['avatar'] = $p['ViewPuntuacionesProblemasAlumno']['avatar'];
				
				if($nombreyapellidos)
				{
					$alumns[$alum]['nombre'] = $this->Alumno->nombre($p['ViewPuntuacionesProblemasAlumno']['usuario_id']);		
					$alumns[$alum]['apellidos'] = $this->Alumno->apellidos($p['ViewPuntuacionesProblemasAlumno']['usuario_id']);
				}
			}

			$nota[$alum] += $p['ViewPuntuacionesProblemasAlumno']['puntuacion_problema'];
			$factor[$alum] += $p['ViewPuntuacionesProblemasAlumno']['factor'];
			
			$anterior_alum = $alum;
		}
		
		foreach($nota as $alum=>$nm)
		{
			$nota[$alum] = round($nm / $factor[$alum], 3);
		}
		
		arsort($nota, SORT_NUMERIC);
		
		$alum = -1;
		$estad = array();
		reset($nota);
		
		$i = 1;
		$end_ejec = false;
		foreach($nota as $k=>$eje) //$i = 0; $end_ejec == false; $i++)
		{
			if(!$nombreyapellidos)
				$estad[]= array('Alumno'=>array('alias'=> $alumns[$k]['alias'],'avatar' => $alumns[$k]['avatar']), 'EstAlumno'=>array('alumno_id'=> $k, 'nota_media' => $eje), 'EstRankingNota'=> array('posicion_ranking_nota' => $i));
			else
				$estad[]= array('Alumno'=>array('id'=> $k, 'alias'=> $alumns[$k]['alias'],'avatar' => $alumns[$k]['avatar']), 'Usuario'=>array('nombre' => $alumns[$k]['nombre'], 'apellidos' => $alumns[$k]['apellidos']), 'EstAlumno'=>array('alumno_id'=> $k, 'nota_media' => $eje), 'EstRankingNota'=> array('posicion_ranking_nota' => $i));
		
			$i++;
		}
		
		return $estad;
	}
	
	function ranking_puntuacion($id=null, $nombreyapellidos = false)
	{
		if ($id===null) $id=$this->id;
	 	
		$asi = $id;
		$this->ViewPuntuacionesProblemasAlumno->recursive=-1;
		$punts = $this->ViewPuntuacionesProblemasAlumno->find('all',array('conditions'=>array('ViewPuntuacionesProblemasAlumno.asignatura_id'=>$asi)));
		
		$puntuacion = array();
		$anterior_alum = false;
		foreach($punts as $p)
		{
			$alum = $p['ViewPuntuacionesProblemasAlumno']['alumno_id'];
			if($alum != $anterior_alum)
			{
				$puntuacion[$alum] = 0;
				$alumns[$alum]['alias'] = $p['ViewPuntuacionesProblemasAlumno']['alias'];
				$alumns[$alum]['avatar'] = $p['ViewPuntuacionesProblemasAlumno']['avatar'];
				
				if($nombreyapellidos)
				{
					$alumns[$alum]['nombre'] = $this->Alumno->nombre($p['ViewPuntuacionesProblemasAlumno']['usuario_id']);		
					$alumns[$alum]['apellidos'] = $this->Alumno->apellidos($p['ViewPuntuacionesProblemasAlumno']['usuario_id']);
				}
			}

			$puntuacion[$alum] += $p['ViewPuntuacionesProblemasAlumno']['puntuacion_problema'];
			
			$anterior_alum = $alum;
		}
		
		arsort($puntuacion, SORT_NUMERIC);
		
		$alum = -1;
		$estad = array();
		reset($puntuacion);
		
		$i = 1;
		$end_ejec = false;
		foreach($puntuacion as $k=>$eje) //$i = 0; $end_ejec == false; $i++)
		{
			if(!$nombreyapellidos)
				$estad[]= array('Alumno'=>array('alias'=> $alumns[$k]['alias'],'avatar' => $alumns[$k]['avatar']), 'EstAlumno'=>array('alumno_id'=> $k, 'puntuacion_total' => $eje), 'EstRankingPuntuacion'=> array('posicion_ranking_puntuacion' => $i));
			else
				$estad[]= array('Alumno'=>array('id'=> $k, 'alias'=> $alumns[$k]['alias'],'avatar' => $alumns[$k]['avatar']), 'Usuario'=>array('nombre' => $alumns[$k]['nombre'], 'apellidos' => $alumns[$k]['apellidos']), 'EstAlumno'=>array('alumno_id'=> $k, 'puntuacion_total' => $eje), 'EstRankingPuntuacion'=> array('posicion_ranking_puntuacion' => $i));
				
			$i++;
		}
	
	 	return $estad;
	}

	/**
	 * Devuelve los bloques de la asignatura
	 * ejemplo: Para acceder a un id $result[$i]['Bloque']['id']
	 *
	 * @param string $id Asignatura
	 * @param string $grupo. Si no es null, entonces muestra únicamente los bloques permitidos al grupo indicado
	 * @return array de bloques.
	 */
	function bloques($id=null,$grupo=null)
	{
		if ($id===null) $id=$this->id;
		$this->contain('Bloque');
		$this->recursive=1;
		$data=$this->findById($id);
		//$data=$this->Bloque->findAll(array('Bloque.asignatura_id'=>$id),null,'orden');
		$bloques=array();
		foreach ($data['Bloque'] as $bloque)
		//foreach ($data as $bloque)
		{
			if ($grupo===null
				|| $this->Bloque->permiso($bloque['id'],$grupo))
			{
				$bloques[]['Bloque']=$bloque;
			}

		}
		return $bloques;
	}

	/**
	 * Devuelve todos los problemas asignados a algún bloque
	 * de la asignatura
	 *
	 * @param integer $id Asignatura
	 * @param array $orden Orden tipo findAll
	 * @return array Array de problemas
	 * 	$result[$i]['enunciado']
	 */
	function problemas($id=null,$orden=null,$recursive=null)
	{
		if ($id===null) $id=$this->id;
		$data=$this->ViewProblemasAsignatura->problemas_asignatura($id,$orden,$recursive);
		$problemas=array();
		foreach ($data as $line)
		{
			$problemas[]=$line['Problema'];
		}
		return $problemas;
	}

	/**
	 * Estadísticas de los problemas de la asignatura
	 *
	 * @param integer $id Asignatura
	 * @return array Estadísticas
	 * 	$result['problemas_disponibles']
	 *  $result['numero_ejecuciones_aprobadas']
	 *  ...
	 */
	function estadisticas_problemas($id=null)
	{
		if ($id===null) $id=$this->id;

		$this->recursive=1;

		$this->contain('ViewNumeroEjecucionesAsignatura',
					'ViewNotaMediaEjecucionesAsignatura',
					'ViewNumeroProblemasDisponiblesAsignatura',
					'ViewNumeroProblemasEjecutadosAsignatura');
		$data=$this->findById($id);
		$estadisticas=array();
		$estadisticas['problemas_disponibles'] =$data['ViewNumeroProblemasDisponiblesAsignatura']['problemas_disponibles'];
		$estadisticas['problemas_ejecutados'] =$data['ViewNumeroProblemasEjecutadosAsignatura']['problemas_ejecutados'];
		$estadisticas['numero_ejecuciones'] =$data['ViewNumeroEjecucionesAsignatura']['numero_ejecuciones'];
		//$estadisticas['numero_ejecuciones_aprobadas'] =$data['ViewNumeroEjecucionesAprobadasAsignatura']['numero_ejecuciones_aprobadas'];
		$estadisticas['nota_media_ejecuciones'] =$data['ViewNotaMediaEjecucionesAsignatura']['nota_media_ejecuciones'];

		//nulos se convierten a ceros
		foreach ($estadisticas as $nombre=>$valor)
		{
			if ($valor=='') $estadisticas[$nombre]=0;
		}

		if ($estadisticas['problemas_ejecutados']>0)
		{
			$estadisticas['media_ejecuciones_por_problema']=round($estadisticas['numero_ejecuciones'] / $estadisticas['problemas_ejecutados'],1);
		}
		else
		{
			$estadisticas['media_ejecuciones_por_problema']=null;
		}

		return $estadisticas;
	}

	/**
	 * Devuelve todos los alumnos asociados a una asignatura
	 *
	 * @param integer $asignatura_id Asignatura
	 * @param mixed $orden Orden tipo findAll (opcional)
	 * @return array
	 * 	$result[$i]['Asignatura']['id'] Asignatura
	 *  $result[$i]['Alumno']['id'] Alumno
	 *  $result[$i]['Grupo']['id'] Grupo
	 */
	function alumnos($asignatura_id,$orden=null)
	{
		if ($orden)
		{
			return $this->ViewAlumnosUsuario->alumnos_asignatura($asignatura_id,$orden);
		}
		else
		{
			return $this->ViewAlumnosUsuario->alumnos_asignatura($asignatura_id);
		}
	}
	
	function alumnos_password_plano($asignatura_id,$orden=null){
		if ($orden){
			return $this->ViewAlumnosUsuario->alumnos_asignatura_password_plano($asignatura_id,$orden);
		}
		else{
			return $this->ViewAlumnosUsuario->alumnos_asignatura_password_plano($asignatura_id);
		}
	}

	/**
	 * Autoasigna el problema publicado a las asignaturas con
	 * autoasignación de problemas
	 *
	 * @param integer $problema_id Problema
	 * @return boolean Éxito
	 */
	function autoasignar($problema_id)
	{
		$agrupacion = ClassRegistry::init('Agrupacion');
		$this->contain('Bloque');
		$asignaturas=$this->find('all',array('conditions'=>array('asignar_todos_publicados'=>1)));
		foreach ($asignaturas as $asig){
			foreach ($asig['Bloque'] as $bloque){
				if (!$agrupacion->buscar($bloque['id'],$problema_id)){
					$agrupacion->guardar($bloque['id'],$problema_id,45,true);
				}
			}
		}

	}

	/**
	 * Devuelve si está activa (según la fecha actual) una cierta Asignatura
	 * @param mixed $data Asignatura ($data['id']) / $id
	 * @return boolean Está activa
	 */
	function es_activa($data)
	{
		if (! is_array($data))
		{
			$this->recursive=-1;
			$data=$this->findById($data);
			if (!$data) return false;
			$data=$data['Asignatura'];
		}
		if ($data['fecha_inicio'])
		{
			if (	(strtotime($data['fecha_inicio'])<= time())
					&&
					( (	($data['fecha_fin'])
						&&
						(strtotime($data['fecha_fin'])>=time())
					  )
					  ||
					  (! $data['fecha_fin'])

					)
				)
			{
				//Si la fecha de inicio es anterior, y la fecha final es nulo o bien posterior
				//entonces lo añadimos
				return true;
			}
		}
		elseif ((strtotime($data['fecha_fin'])>=time()) ||  (! $data['fecha_fin']))
		{
			return true;
		}
		return false;
	}

}
?>
