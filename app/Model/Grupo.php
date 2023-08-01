<?php
/**
 * Grupo Model
 */
class Grupo extends AppModel
{
	var $name = 'Grupo';
	public $actsAs = array('Containable');
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array('Asignatura');

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $hasMany = array('Alumno');

	var $hasOne=array(
			'ViewNotaMediaEjecucionesGrupo',
			'ViewNumeroEjecucionesGrupo',
			'ViewPuntuacionTotalGrupo',
			'ViewNumeroEjecucionesAprobadasGrupo',
			'ViewNumeroProblemasDisponiblesGrupo',
			'ViewNumeroProblemasEjecutadosGrupo',
			'ViewNumeroAlumnosGrupo',
			'ViewNumeroUsuariosGrupo',
	);

	/**
	 * Busca un grupo dado su nombre y su asignatura
	 * @param integer $asignatura_id Asignatura
	 * @param string $nombre Nombre del grupo
	 *
	 * @return integer $grupo_id
	 */
	function buscar($asignatura_id,$nombre)
	{
		$this->recursive=-1;
		$data=$this->find('first', array('conditions'=>array('Grupo.asignatura_id'=>$asignatura_id,'Grupo.nombre'=>$nombre)));
		if (!$data) return null;
		return $data['Grupo']['id'];
	}

	/**
	 * Devuelve el nombre del grupo dado el identificador
	 *
	 * @param integer $grupo_id
	 *
	 * @return string nombre
	 */
	function nombre_grupo($grupo_id)
	{
		$this->recursive=-1;
		$data=$this->find('first',array('conditions'=>array('Grupo.id'=>$grupo_id),'fields'=>array('nombre')));
		return $data['Grupo']['nombre'];
	}

	/**
	 * Crea un grupo dado su nombre y su asignatura
	 * @param integer $asignatura_id Asignatura
	 * @param string $nombre Nombre del grupo
	 *
	 * @return integer Devuelve $grupo_id si se ha creado, null en caso de error
	 */
	function crear($asignatura_id,$nombre)
	{
		$this->create();
		if ($this->save(array(	'asignatura_id'=>$asignatura_id,
							'nombre'=>$nombre)))
		{
			return $this->getLastInsertID();
		}
		return null;
	}

	/**
	 * Busca o crea un grupo dado su nombre y su asignatura. Si no existe, lo crea.
	 *
	 * @param integer $asignatura_id Asignatura
	 * @param string $nombre Nombre del grupo
	 *
	 * @return integer $grupo_id creado/encontrado, null en caso de error
	 */
	function buscar_crear($asignatura_id,$nombre)
	{
		$this->cacheQueries=false;
		$grupo_id=$this->buscar($asignatura_id,$nombre);
		if (!$grupo_id)
		{
			$grupo_id=$this->crear($asignatura_id,$nombre);
		}
		return $grupo_id;
	}


	/**
	 * Devuelve los grupos de una asignatura
	 *
	 * @param integer $asignatura_id Asignatura
	 * @return array Array de nombres de grupos
	 */
	function grupos_asignatura($asignatura_id)
	{
		$this->recursive=-1;
		$data=$this->find('all',array('conditions'=>array('Grupo.asignatura_id'=>$asignatura_id)),null,array('Grupo.nombre'));
		$grupos=array();
		foreach ($data as $line)
		{
			$grupos[]=$line['Grupo']['nombre'];
		}
		return $grupos;
	}


	/**
	 * Devuelve todas las estadísticas de los grupos de una asignatura
	 *
	 * @param integer $asignatura_id Asignatura
	 * @param string $grupo Nombre del grupo. Si null, entonces todos los grupos
	 *
	 * @return array
	 *
	 *  $result[$i]['Grupo']['id']
	 * 	$result[$i]['Estadisticas']['NotaMediaEjecucionesGrupo']
	 */
	function estadisticas_grupos($asignatura_id,$grupo=null)
	{
		$models=array(	'ViewNotaMediaEjecucionesGrupo',
						'ViewNumeroEjecucionesGrupo',
						'ViewPuntuacionTotalGrupo',
						'ViewNumeroAlumnosGrupo',
						'ViewNumeroUsuariosGrupo',
						'ViewNumeroProblemasDisponiblesGrupo',
						'ViewNumeroProblemasEjecutadosGrupo');
		
	 	$this->contain($models);
	 	$this->recursive=1;

	 	$cond=array('Grupo.asignatura_id'=>$asignatura_id);
	 	if ($grupo!==null) $cond['Grupo.nombre']=$grupo;
	 	$data=$this->find('all', array('conditions'=>$cond));
	 	foreach ($data as $i=>$row)
	 	{
	 		$estadisticas=array();
	 		foreach ($models as $name)
	 		{
		 		if (isset($row[$name]))
	 			{
		 			$estadisticas=am($estadisticas,$row[$name]);
		 			unset($data[$i][$name]);
	 			}
	 		}

			if (isset($estadisticas['problemas_ejecutados'])
			&& isset($estadisticas['numero_ejecuciones'])
			&& $estadisticas['problemas_ejecutados'] >0)
			{
				$estadisticas['media_ejecuciones_por_problema']=
					round($estadisticas['numero_ejecuciones'] / $estadisticas['problemas_ejecutados'],1);
			}
			else
			{
				$estadisticas['media_ejecuciones_por_problema']=null;
			}

	 		$data[$i]['Estadisticas']=$estadisticas;
	 	}
		return $data;
	}


	/**
	 * Devuelve todas las estadísticas de un grupo de una asignatura
	 * @param integer $asignatura_id Asignatura
	 * @param string $grupo Grupo
	 *
	 * @return array Array asociativo estadísticas
	 * 	$result['NotaMediaEjecucionesGrupo']
	 */
	function estadisticas_grupo($asignatura_id,$grupo)
	{
		$data=$this->estadisticas_grupos($asignatura_id,$grupo);
		if (! $data) return null;
		return $data[0]['Estadisticas'];
	}


	/**
	 * Función Auxiliar a estadisticas_grupo(s). Recolecta información de los
	 * modelos de estadísticas de grupos
	 * @param array $estadisticas Resultados parciales de modelos anteriores
	 * @param integer $asignatura_id Asignatura
	 * @param string $model Nombre del modelo de estadísticas
	 * @param array $fields Campos del modelo
	 * @param string $grupo Grupo del cual se desean las estadísticas. Si null, entonces todos
	 *
	 * @return array $estadisticas Resultados parciales de modelos anteriores más el actual
	 */
	function _estadisticas_grupos_aux($estadisticas,$asignatura_id,$model,$fields,$grupo=null)
	{
		$this->$model->recursive=-1;
		$cond=array("$model.asignatura_id"=>$asignatura_id);
		if ($grupo!==null)
		{
			$cond["$model.grupo"]=$grupo;
		}

		$data=$this->$model->findAll($cond);

		foreach ($data as $row)
		{
			foreach ($fields as $f)
			{
				$estadisticas[$row[$model]['grupo']][$f]=
						$row[$model][$f];
			}
		}
		return $estadisticas;
	}

	/**
	 * Devuelve todos los alumnos de un cierto grupo junto con el usuario asociado
	 * @param integer $id Grupo
	 * @return array Alumnos
	 * 	$result[$i]['nombre']
	 */
	function alumnos($id=null)
	{
		$this->recursive=2;
		$this->contain('Alumno');
		$this->Alumno->contain('Usuario');
		$data=$this->findById($id);
		if (! $data || ! isset($data['Alumno'])) return null;

		return $data['Alumno'];
	}


	/**
	 * Elimina el grupo si está vacío (no tiene alumnos)
	 *
	 * @param integer $id Grupo
	 * @return boolean Éxito (no han habido errores)
	 */
	function eliminar_si_vacio($id)
	{
		$this->Alumno->contain();
		if (!$this->Alumno->hasAny(array('Alumno.grupo_id'=>$id)))
		{
			return $this->delete($id);
		}
		return true;
	}

}
?>
