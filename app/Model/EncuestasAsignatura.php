<?php
App::import('Vendor', 'estatus/clases/estadisticas');

class EncuestasAsignatura extends AppModel
{
	var $name = 'EncuestasAsignatura';
	public $actsAs = array('Containable');

	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Encuesta',
			'Asignatura'
	);

	var $hasMany = array(
			'VotacionesEncuesta',
			'EstAlumno'
	);

	var $uses=array('EstAlumno');


	/**
	 * Devuelve un array con todas las encuestas de la asignatura indicada
	 * @param integer $asignatura_id Asignatura
	 * @param boolean $solo_activas Únicamente devuelve las que están activas a fecha de hoy
	 * @return array
	 * 	Ejemplo: 	$result[$i]['EncuestasAsignatura']['encuesta_id']
	 * 				$result[$i]['EncuestasAsignatura']['fecha_inicio']
	 * 				$result[$i]['EncuestasAsignatura']['fecha_fin']
	 * 				$result[$i]['Encuesta']['nombre']
	 */
	function encuestas_asignatura($asignatura_id,$solo_activas=true)
	{
		$this->recursive=1;
		$this->contain('Encuesta');
		$data_tmp=$this->find('all',array('conditions'=>array('EncuestasAsignatura.asignatura_id'=>$asignatura_id)));
		if (!$solo_activas){
			return $data_tmp;
		}
		//hay que buscar las activas
		$data=array();
		foreach ($data_tmp as $row)
		{
			if ($this->es_activa($row['EncuestasAsignatura'])){
				$data[]=$row;
			}
		}
		return $data;
	}

	/**
	 * Devuelve si está activa (según la fecha actual) una cierta EncuestaAsignatura
	 * @param array $data EncuestaAsignatura ($data['id'])
	 * @return boolean Está activa
	 */
	function es_activa($data)
	{
		if ($data['activo']){
			if ($data['fecha_inicio']){
				if ((strtotime($data['fecha_inicio'])<= time()) && ((($data['fecha_fin']) && (strtotime($data['fecha_fin'])>=time())) || (! $data['fecha_fin']))){
					//Si la fecha de inicio es anterior, y la fecha final es nulo o bien posterior
					//entonces lo añadimos
					return true;
				}
			}
			elseif ((strtotime($data['fecha_fin'])>=time()) ||  (!$data['fecha_fin'])){
				return true;
			}
		}
		return false;
	}

	/**
	 * Devuelve los resultados de la votación.
	 * Por cada pregunta devuelve sus votos (con alumno cifrado)
	 *
	 * @param integer $encuestas_asignatura_id
	 * @return array Opciones de la pregunta + Votos de la opción
	 *  $result[$pregunta_i]['id'/'contenido'] Pregunta
	 * 	$result[$pregunta_i]['OpcionesEncuesta']$opcion_j]['id'/'contenido'],
	 * 	$result[$pregunta_i]['OpcionesEncuesta']$opcion_j]['VotacionesEncuesta'][$j]['alumno_id_cifrado']
	 */
	function resultados_simples_votacion($encuestas_asignatura_id){
		$this->recursive=-1;
		$data=$this->findById($encuestas_asignatura_id);
		if (! $data) return null;

		$preguntas=$this->Encuesta->PreguntasEncuesta->preguntas_encuesta($data['EncuestasAsignatura']['encuesta_id']);
		foreach ($preguntas as $i=>$preg){
			$pregunta_id=$preg['PreguntasEncuesta']['id'];
			$opciones=$this->Encuesta->PreguntasEncuesta->OpcionesEncuesta->opciones_encuesta($pregunta_id);
			$n_total=0;
			foreach ($opciones as $j=>$opc){
				$opcion_id=$opc['OpcionesEncuesta']['id'];
				$this->VotacionesEncuesta->recursive=-1;
				$tmp_votaciones=$this->VotacionesEncuesta->find('all',array('conditions'=>
					array(	'VotacionesEncuesta.encuestas_asignatura_id'=>$encuestas_asignatura_id,
							'VotacionesEncuesta.opciones_encuesta_id'=>$opcion_id)));
				$votaciones=array();
				foreach ($tmp_votaciones as $voto) $votaciones[]=$voto['VotacionesEncuesta'];
				$opciones[$j]['VotacionesEncuesta']=$votaciones;
				$opciones[$j]['numero_votos']=count($votaciones);
				$n_total+=count($votaciones);
			}
			if ($n_total>0){
				foreach ($opciones as $j=>$opc){
					$opciones[$j]['porcentaje_votos']=round(($opc['numero_votos'] / $n_total)*100,1);
				}
			}
			else{
				foreach ($opciones as $j=>$opc){
					$opciones[$j]['porcentaje_votos']=0;
				}
			}
			$preguntas[$i]['OpcionesEncuesta']=$opciones;
		}
		return $preguntas;
	}

	/**
	 * Devuelve los resultados de la votación. Por cada pregunta desglosa los votos en función del grupo
	 *
	 * @param integer $encuestas_asignatura_id
	 * @return array Opciones de la pregunta + Votos de la opción
	 * 		$result[$i]['id'], $result[$i]['contenido']
	 * 		$result[$i]['votos_grupos']['grupo_id'] = numero votos del grupo grupo_id
	 */
	function resultados_grupos_votacion($encuestas_asignatura_id)
	{
		$resultados=$this->resultados_simples_votacion($encuestas_asignatura_id);
		foreach ($resultados as $k=>$pregunta){
			$resultados[$k]['porcentaje_obs'] = array();
			$resultados[$k]['votos_grupo'] = array();
			$total_votos = 0;
			foreach ($pregunta['OpcionesEncuesta'] as $i=>$opcion){
				foreach ($opcion['VotacionesEncuesta'] as $j=>$voto){
					$alumno_id=$this->VotacionesEncuesta->_desencriptar_identificador($voto['alumno_id_cifrado'],$voto['encuestas_asignatura_id']);
					$this->VotacionesEncuesta->Alumno->contain('Grupo');
					$data=$this->VotacionesEncuesta->Alumno->findById($alumno_id);
					$grupo=$data['Grupo']['nombre'];
					if(isset($resultados[$k]['votos_grupo'][$grupo])){
						$resultados[$k]['votos_grupo'][$grupo]++;
					}
					else{
						$resultados[$k]['votos_grupo'][$grupo] = 1;
					}
					if (isset($resultados[$k]['OpcionesEncuesta'][$i]['votos_grupos'][$grupo])){
						$resultados[$k]['OpcionesEncuesta'][$i]['votos_grupos'][$grupo]++;
					}
					else{
						$resultados[$k]['OpcionesEncuesta'][$i]['votos_grupos'][$grupo]=1;
					}
					$total_votos++;
				}
				if($total_votos > 0)
				foreach($resultados[$k]['votos_grupo'] as $id_gr=>$num_gr){
					$resultados[$k]['porcentaje_obs'][$id_gr] = round($num_gr / $total_votos * 100, 2); 
				}
			}
		}
		return $resultados;
	}


	/**
	 * Devuelve los resultados de la votación.
	 * Por cada pregunta calcula la media y desviación de varios estadísticos de los votantes: puntuación total, nota media, número de ejecuciones
	 *
	 * @param integer $encuestas_asignatura_id
	 * @return array Opciones de la pregunta + Votos de la opción
	 * 		$result[$i]['id'], $result[$i]['contenido']
	 * 		$result[$i]['estadisticos']['puntuacion_total']['media']
	 *      $result[$i]['estadisticos']['puntuacion_total']['desviacion']
	 * Estadísticos: 'puntuacion_total','nota_media','num_ejecuciones'
	 */
	function resultados_puntuaciones_votacion($encuestas_asignatura_id){
		$resultados=$this->resultados_simples_votacion($encuestas_asignatura_id);

		$alumnos=array();
		foreach ($resultados as $k=>$pregunta)
		{
			foreach ($pregunta['OpcionesEncuesta'] as $i=>$opcion)
			{
				foreach ($opcion['VotacionesEncuesta'] as $j=>$voto)
				{
					$alumno_id=$this->VotacionesEncuesta->_desencriptar_identificador($voto['alumno_id_cifrado'],$voto['encuestas_asignatura_id']);
					$alumnos[]=$alumno_id;
					$resultados[$k]['OpcionesEncuesta'][$i]['VotacionesEncuesta'][$j]['alumno_id']=$alumno_id;
				}
			}
		}


		$data=$this->EstAlumno->estadisticas_alumnos(array_unique($alumnos));

		$estadisticas_alumnos=array();
		foreach ($data as $row)
		{
			$estadisticas_alumnos[$row['EstAlumno']['alumno_id']]=$row['EstAlumno'];
		}

		foreach ($resultados as $k=>$pregunta)
		{
			foreach ($pregunta['OpcionesEncuesta'] as $i=>$opcion)
			{
				$a_puntuacion_total=array();
				$a_nota_media=array();
				$a_num_ejecuciones=array();

				foreach ($opcion['VotacionesEncuesta'] as $j=>$voto)
				{
					if(isset($estadisticas_alumnos[$voto['alumno_id']]))			
					{
						$estadisticas=$estadisticas_alumnos[$voto['alumno_id']];

						$resultados[$k]['OpcionesEncuesta'][$i]['VotacionesEncuesta'][$j]['puntuacion_total']=$estadisticas['puntuacion_total'];
						$a_puntuacion_total[]=$estadisticas['puntuacion_total'];

						$resultados[$k]['OpcionesEncuesta'][$i]['VotacionesEncuesta'][$j]['nota_media']=$estadisticas['nota_media'];
						$a_nota_media[]=$estadisticas['nota_media'];

						$resultados[$k]['OpcionesEncuesta'][$i]['VotacionesEncuesta'][$j]['num_ejecuciones']=$estadisticas['num_ejecuciones'];
						$a_num_ejecuciones[]=$estadisticas['num_ejecuciones'];
					}
				}
				if (count($opcion['VotacionesEncuesta']))
				{
					$resultados[$k]['OpcionesEncuesta'][$i]['puntuacion_total']['mean']=Estadisticas::mean($a_puntuacion_total);
					$resultados[$k]['OpcionesEncuesta'][$i]['puntuacion_total']['stdev']=Estadisticas::stdev($a_puntuacion_total);

					$resultados[$k]['OpcionesEncuesta'][$i]['nota_media']['mean']=Estadisticas::mean($a_nota_media);
					$resultados[$k]['OpcionesEncuesta'][$i]['nota_media']['stdev']=Estadisticas::stdev($a_nota_media);

					$resultados[$k]['OpcionesEncuesta'][$i]['num_ejecuciones']['mean']=Estadisticas::mean($a_num_ejecuciones);
					$resultados[$k]['OpcionesEncuesta'][$i]['num_ejecuciones']['stdev']=Estadisticas::stdev($a_num_ejecuciones);
				}

			}
		}
		return $resultados;
	}

}
?>
