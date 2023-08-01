<?php
/**
 * EstadisticasGrupos Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */
App::import('Vendor','estatus/rserve/graficos');

class EstadisticasGruposController extends AppController
{
    var $name = 'EstadisticasGrupos';
    var $uses=array('Asignatura', 'Grupo',	'Alumno', 'EstAlumno');
    var $components=array('CustomPaginate');
    var $helpers=array('CustomPagination');
	var $access = array
	(
	'*' => array('profesor'),
	);
                
	function index($asignatura_id=null)
	{
		$this->set('title_for_layout',__('estadisticas_grupos'));
		
		if(!isset($asignatura_id))
			$asignatura_id=$this->_profesor_asignatura_actual();
		$data =$this->Grupo->estadisticas_grupos($asignatura_id);
		$estadisticas=array();

		$total['numero_alumnos']=0;
		$total['numero_usuarios']=0;
		$media['problemas_ejecutados']=0;
		$total['numero_ejecuciones']=0;
		$total['puntuacion_total']=0;
		$media['nota_media_ejecuciones']=0;

		foreach ($data as $row)
		{
			$est=$row['Estadisticas'];
			$estadisticas[$row['Grupo']['nombre']]=$est;

			$total['numero_alumnos']+=$est['numero_alumnos'];
			$total['numero_usuarios']+=$est['numero_usuarios'];
			$media['problemas_ejecutados']+=$est['problemas_ejecutados'];
			$total['numero_ejecuciones']+=$est['numero_ejecuciones'];
			$media['nota_media_ejecuciones']+= $est['nota_media_ejecuciones'] * $est['numero_usuarios'];
			$total['puntuacion_total']+=$est['puntuacion_total'];
		}
		if ($total['numero_usuarios']>0)
		{
			$media['nota_media_ejecuciones']=round($media['nota_media_ejecuciones'] / $total['numero_usuarios'],3);
		}
		if (count($data))
		{
			$media['problemas_ejecutados']=round($media['problemas_ejecutados'] / count($data),2);
		}
		if ($total['numero_alumnos']>0)
		{
			$media['porcentaje_usuarios']=round( ($total['numero_usuarios'] / $total['numero_alumnos'])*100,1);
		}
		else
		{
			$media['porcentaje_usuarios']='';
		}

		$this->set('total',$total);
		$this->set('media',$media);
		$this->set('estadisticas',$estadisticas);
	}

	function grupo($grupo)
	{
		$asignatura_id=$this->_profesor_asignatura_actual();

		$estadisticas=$this->Grupo->estadisticas_grupo($asignatura_id,$grupo);
		$this->set('estadisticas',$estadisticas);

		$this->set('grupo',$grupo);
		$this->set('title_for_layout',__('estadisticas_grupo',array($grupo)));
		
		$grupos=$this->Asignatura->grupos($asignatura_id);
		$this->set('grupos',$grupos);

		$alumnos=$this->Alumno->estadisticas_alumnos_grupo($asignatura_id,$grupo,array('EstAlumno'));

		$alumnos=$this->CustomPaginate->paginate_data($alumnos,20);
		$this->set('page_url',"/estadisticas_grupos/grupo/$grupo");

		$this->set('alumnos',$alumnos);
	}

	/**
	 * Gráfico de los grupos: nota vs puntuación
	 */
	function grafico_nota_puntuacion()
	{
		$this->set('title_for_layout',__('grafico_nota_vs_puntuacion'));
		$asignatura_id=$this->_profesor_asignatura_actual();
		$data =$this->Grupo->estadisticas_grupos($asignatura_id);

		$puntos=array();
		$titles=array();
		$href=array();

		//uses('sanitize');
		foreach ($data as $line)
		{
			$puntuacion=round($line['Estadisticas']['puntuacion_total'],3);
			$nota_media=round($line['Estadisticas']['nota_media_ejecuciones'],3);
			$numero_ejecuciones=round($line['Estadisticas']['numero_ejecuciones'],3);

			$puntos[]=array($nota_media,$puntuacion);
			$nombre=$line['Grupo']['nombre'];

			$titles[]=$nombre. " (punt $puntuacion, nota $nota_media, ejec $numero_ejecuciones)";
			$href[]="/estadisticas_grupos/grupo/{$line['Grupo']['nombre']}";
		}
		
		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;
		$params=array(	'title'=>$titles,
						'href'=>$href);

		$file=$graficos->plot($puntos,'p',array(__('Nota media'),__('Puntuación')),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map();

		$this->set('img_url',$img_url);
		$this->set('map',$map);
	}

	/**
	 * Boxplot de un gráfico de la nota media de los grupos
	 */
	function grafico_nota()
	{
		$this->set('title_for_layout',__('grafico_boxplot_nota'));
		$asignatura_id=$this->_profesor_asignatura_actual();

		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;

		$this->EstAlumno->recursive=-1;
		$data=$this->EstAlumno->find('all',array('conditions'=>array('EstAlumno.asignatura_id'=>$asignatura_id)));
		$valores=array();
		foreach ($data as $row)
		{
			$valores[]=array($row['EstAlumno']['nota_media'],$this->Grupo->nombre_grupo($row['EstAlumno']['grupo_id']));
		}

		$file=$graficos->boxplot($valores,array(__('Nota media'),__('Grupos')));
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();

		$this->set('img_url',$img_url);
	}

	/**
	 * Boxplot de un gráfico de la puntuación de los grupos
	 */
	function grafico_puntuacion()
	{
		$this->set('title_for_layout',__('grafico_boxplot_puntuacion'));
		$asignatura_id=$this->_profesor_asignatura_actual();

		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;

		$this->EstAlumno->recursive=-1;
		$data=$this->EstAlumno->find('all',array('conditions'=>array('EstAlumno.asignatura_id'=>$asignatura_id)));
		$valores=array();
		foreach ($data as $row)
		{
			$valores[]=array($row['EstAlumno']['puntuacion_total'],$this->Grupo->nombre_grupo($row['EstAlumno']['grupo_id']));
		}

		$file=$graficos->boxplot($valores,array(__('Puntuación'),__('Grupos')));
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();

		$this->set('img_url',$img_url);
	}


	/**
	 * Barplot de un gráfico de numero de problemas por alumno
	 */
	function grafico_problemas_por_alumno()
	{
		$this->set('title_for_layout',__('grafico_problemas_alumno'));
		$asignatura_id=$this->_profesor_asignatura_actual();
		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;

		$grupos=$this->Asignatura->grupos($asignatura_id);

		$this->EstAlumno->recursive=-1;
		$data=$this->EstAlumno->find('all',array('conditions'=>array('EstAlumno.asignatura_id'=>$asignatura_id)));
		$valores=array();
		foreach ($data as $row)
		{
			$valores[]=array($row['EstAlumno']['num_problemas_ejecutados'],$this->Grupo->nombre_grupo($row['EstAlumno']['grupo_id']));
		}

		$file=$graficos->barplot($valores,$grupos,array(__('# Problemas'),__('# Alumnos')));
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();

		$this->set('img_url',$img_url);
	}

}
