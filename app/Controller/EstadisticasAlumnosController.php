<?php
/**
 * EstadisticasAlumnos Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */
App::import('Vendor','estatus/rserve/graficos');

class EstadisticasAlumnosController extends AppController
{
    var $name = 'EstadisticasAlumnos';
    var $uses=array('Alumno' ,'EstAlumno','Asignatura','ViewBuscarAlumno');
    var $components = array('CustomPaginate');
	var $helpers = array('CustomPagination');
	var $access = array
	(
	'*' => array('profesor'),
	);
                
	function index($asignatura_id=null)
	{
		$this->set('title_for_layout',__('listado_alumnos_asignatura'));
		if(!isset($asignatura_id))
			$asignatura_id=$this->_profesor_asignatura_actual();
		else
			$this->Session->write('log_asig_id',$asignatura_id);
		$alumnos=$this->Alumno->estadisticas_alumnos_asignatura($asignatura_id,array('EstAlumno'));
		$alumnos=$this->CustomPaginate->paginate_data($alumnos,30);
		$this->set('page_url','/estadisticas_alumnos/index');
		$this->set('alumnos',$alumnos);
	}

	function search()
	{
		$this->set('title_for_layout',__('menu_buscar_alumnos'));
		if ($this->request->is('post'))
		{
			$data = $this->request->data;
			if (!$data['Alumno']['campos'])
			{
				$this->Session->setFlash(__('error_indicar_campo'));
			}
			$data['Alumno']['search']=trim($data['Alumno']['search']);
			if (!$data['Alumno']['search'])
			{
				$this->Session->setFlash(__('error_indicar_termino'));
			}
			$asignatura_id=$this->_profesor_asignatura_actual();
			$this->ViewBuscarAlumno->recursive=-1;
			$alumnos=$this->ViewBuscarAlumno->buscar($data['Alumno']['search'],$data['Alumno']['campos'],$asignatura_id);

			$this->set('alumnos',$alumnos);
		}
	}

	function ranking_ejecuciones()
	{
		$this->set('title_for_layout',__('menu_ranking_ejecuciones'));
		$this->set('page_url','/estadisticas_alumnos/ranking_ejecuciones');

		$asignatura_id=$this->_profesor_asignatura_actual();

		$this->Alumno->contain('EstAlumno','EstRankingEjecucion','Usuario');
		//$data=$this->Alumno->findAll(array('EstAlumno.asignatura_id'=>$asignatura_id),null,array('EstRankingEjecucion.posicion_ranking_ejecuciones'=>'ASC'));
		$data = $this->Asignatura->ranking_ejecuciones($asignatura_id, true);

		$img_url=$this->_ranking_grafico($data,null,'num_ejecuciones',array(__('Num ejecuciones'),__('# Alumnos')));
		$this->set('img_url',$img_url);
		
		$data=$this->CustomPaginate->paginate_data($data,20);

		$this->set('data',$data);
	}


	function ranking_nota($alumno_id=null)
	{
		$this->set('title_for_layout',__('menu_ranking_nota'));
		$this->set('page_url','/estadisticas_alumnos/ranking_nota');

		$asignatura_id=$this->_profesor_asignatura_actual();

		$this->Alumno->contain('EstAlumno','EstRankingNota','Usuario');
		//$data=$this->Alumno->findAll(array('EstAlumno.asignatura_id'=>$asignatura_id),null,array('EstRankingNota.posicion_ranking_nota'=>'ASC'));
		
		$data = $this->Asignatura->ranking_nota($asignatura_id, true);
		
		$img_url=$this->_ranking_grafico($data,null,'nota_media',array(__('Nota media'),__('# Alumnos')));
		$this->set('img_url',$img_url);

		$data=$this->CustomPaginate->paginate_data($data,20);

		$this->set('data',$data);
	}

	function ranking_puntuacion($alumno_id=null)
	{
		$this->set('title_for_layout',__('menu_ranking_puntuacion'));
		$this->set('page_url','/estadisticas_alumnos/ranking_puntuacion');

		$asignatura_id=$this->_profesor_asignatura_actual();

		$this->Alumno->contain('EstAlumno','EstRankingPuntuacion','Usuario');
		//$data=$this->Alumno->findAll(array('EstAlumno.asignatura_id'=>$asignatura_id),null,array('EstRankingPuntuacion.posicion_ranking_puntuacion'=>'ASC'));
		
		$data = $this->Asignatura->ranking_puntuacion($asignatura_id, true);

		$img_url=$this->_ranking_grafico($data,null,'puntuacion_total',array(__('Puntuación'),__('# Alumnos')));
		$this->set('img_url',$img_url);

		$data=$this->CustomPaginate->paginate_data($data,20);
		$this->set('data',$data);
	}

	function _ranking_grafico($data,$alumno_id,$campo,$nombre_ejes)
	{
		$graficos = new GraficosRserve();
		$graficos->ancho=600;
		$graficos->alto=350;

		$valores=array();
		$valor_alumno=null;
		foreach ($data as $row)
		{
			$valores[]=$row['EstAlumno'][$campo];
			if ($row['EstAlumno']['alumno_id']==$alumno_id)
			{
				$valor_alumno=$row['EstAlumno'][$campo];
			}
		}

		$file=$graficos->histograma($valores,$valor_alumno,$nombre_ejes);
		unlink($file);
		return $graficos->guardar_grafico_img();
	}

}
