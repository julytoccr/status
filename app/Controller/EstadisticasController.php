<?php
App::import('Vendor','estatus/rserve/graficos');

class EstadisticasController extends AppController
{
    var $name = 'Estadisticas';
    var $uses=array('Asignatura','Grupo', 'Alumno',
					'ViewEstadisticasProblema','ViewLoginAlumnosDiario','EstEjecucion','ViewEjecucionesDiario',
					'Problema', 'ViewLoginsAsignatura', 'ViewEjecucionesAsignatura');

	var $access = array
	(
	'*' => array('profesor'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('estadisticas_asignatura'));
    }

	/**
	 * Muestra las estadísticas generales de la asignatura
	 * y la lista de problemas de la asignatura para
	 * obtener más detalles de cada problema
	 *
	 */
	function asignatura()
	{
		$asignatura_id=$this->_profesor_asignatura_actual();
		$estadisticas=$this->Asignatura->estadisticas_problemas($asignatura_id);
		$this->set('estadisticas',$estadisticas);
	}
	
	function problemas_grafico($asignatura_id=null)
	{
		$this->set('title_for_layout',__('grafico_nota_vs_ejecuciones'));
		if(!isset($asignatura_id))
			$asignatura_id=$this->_profesor_asignatura_actual();
		$data=$this->ViewEstadisticasProblema->problemas($asignatura_id,array('nota_media'=>'desc'));
		$puntos=array();
		$titles=array();
		$href=array();

		//uses('sanitize');
		foreach ($data as $line)
		{
			$numero_ejecuciones=$line['Estadisticas']['numero_ejecuciones'];
			$nota_media=round($line['Estadisticas']['nota_media'],3);

			$puntos[]=array($numero_ejecuciones,$nota_media);
			$nombre=$line['Problema']['nombre'];
			$nombre=str_replace('"','',$nombre);
			$nombre=str_replace("'",'',$nombre);

			$titles[] = $nombre. " (ejec $numero_ejecuciones,nota $nota_media)";
			$href[] = "/estadisticas_problemas/index/{$line['Problema']['id']}";
		}

		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;
		$params=array(	'title'=>$titles,
						'href'=>$href);

		$file=$graficos->plot($puntos,'p',array('numero ejecuciones','Nota media'),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map();

		$this->set('img_url',$img_url);
		$this->set('map',$map);
	}
	
	function problemas_listado_nota()
	{
		$this->set('title_for_layout',__('estadisticas_de_los_problemas'));
		$asignatura_id=$this->_profesor_asignatura_actual();
		$problemas=$this->ViewEstadisticasProblema->problemas($asignatura_id,array('nota_media'=>'desc'));
		//$problemas=$this->myPagination->paginate_data($problemas,30);
		$this->set('page_url','/estadisticas/problemas_listado_nota');
		$this->set('problemas',$problemas);
		$this->render('problemas_listado');
	}
	
	function problemas_listado_ejecuciones()
	{
		$this->set('title_for_layout',__('estadisticas_de_los_problemas'));
		$asignatura_id=$this->_profesor_asignatura_actual();
		$problemas=$this->ViewEstadisticasProblema->problemas($asignatura_id,array('numero_ejecuciones'=>'desc'));
		//$problemas=$this->myPagination->paginate_data($problemas,30);
		$this->set('page_url','/estadisticas/problemas_listado_ejecuciones');
		$this->set('problemas',$problemas);
		$this->render('problemas_listado');
	}
	
	function historial_login()
	{
		$this->set('title_for_layout',__('menu_historial_login'));
		//uses('sanitize');

		if ($this->request->is('post'))
		{
			//$data = $this->_clean_data();
			$data = $this->request->data;
			
			$fechaini =  $data['Usuario']['fecha_inicio'];
			$fechafin = $data['Usuario']['fecha_fin'];

			$error = false;
			if($fechaini != '')
			{
				if(($fechaini = date_create($fechaini)) == FALSE)
				{
					$error = true;
					$fechaini = '_';
				}
				else
					$fechaini = date_format($fechaini, "d-m-Y");
			}
			else
				$fechaini = '_';
					
			if($fechafin != '')
			{
				if(($fechafin = date_create($fechafin)) == FALSE)
				{
					$error = true;
					$fechafin = '_';
				}
				else
					$fechafin = date_format($fechafin, "d-m-Y");
			}
			else
				$fechafin = '_';
		
			if($error)
			{
				$this->Session->setFlash(__('fecha_invalida'));
			}		
			$this->redirect('/estadisticas/historial_login_post/'. $fechaini .'/' . $fechafin);
		}
	}
	
	function historial_login_post($fechaini = null, $fechafin = null, $asignatura_id=null)
	{
		$this->set('title_for_layout',__('menu_historial_login'));
		if(!isset($asignatura_id))
			$asignatura_id=$this->_profesor_asignatura_actual();

		$cond=array('ViewLoginAlumnosDiario.asignatura_id'=>$asignatura_id);

		$existen_datos=$this->ViewLoginAlumnosDiario->find('count',array('conditions'=>$cond));

		$this->set('existen_datos',$existen_datos);
		$this->set('fechaini', $fechaini);
		$this->set('fechafin', $fechafin);
		$res1 = $this->grafico_logins(true, $fechaini, $fechafin, $asignatura_id);
		$res2 = $this->grafico_ejecuciones($fechaini, $fechafin, $asignatura_id);
		$this->set('img1', $res1['image']);
		$this->set('map1', $res1['map']);
		
		$this->set('img2', $res2['image']);
		$this->set('map2', $res2['map']);
	}
	
	function grafico_logins($alumnos_diferentes=false, $fechaini=null, $fechafin=null, $asignatura_id=null)
	{
		if(!isset($asignatura_id))
			$asignatura_id=$this->_profesor_asignatura_actual();
		
		$ffin = true;
		$fini = true;
		
		if($fechaini != '_')
			$fechaini = date_create($fechaini);
		else
			$fini = false;
		if($fechafin != '_')
			$fechafin = date_create($fechafin);
		else
			$ffin = false;
			

		$data = $this->ViewLoginAlumnosDiario->filtrar_por_fecha($fini, $ffin, $fechaini, $fechafin, $asignatura_id);
		

		$puntos=array();
		foreach ($data as $row)
		{
			$d = $row['ViewLoginAlumnosDiario']['dia'];
			$m = $row['ViewLoginAlumnosDiario']['mes'];
			$yr = $row['ViewLoginAlumnosDiario']['anyo'];
			
			$x='as.Date("' . $yr .'-'.$m . '-' .$d . '")';
			if ($alumnos_diferentes)
			{
				$y=$row['ViewLoginAlumnosDiario']['num_alumnos'];
			}
			else
			{
				$y=$row['ViewLoginAlumnosDiario']['num_logins'];
			}

			$puntos[]=array($x,$y);
			$href[]="/estadisticas/listado_login/$d/$m/$yr";
		}

		if (!$puntos)
		{
			die("No ha habido ningún login en el sistema");
		}
		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;
		$params=array('href'=>$href);

		if ($alumnos_diferentes)
		{
			$eje_y=__('numero alumnos difentes');
		}
		else
		{
			$eje_y=__('numero logins');
		}
		
		$file=$graficos->plot($puntos,'b',array(__('Fecha'),$eje_y),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map();

		$res = array();
		$res['image']= $img_url;
		$res['map'] = $map;

		return $res;
	}
	
		
	/**
	 * gráfico de ejecuciones por día
	 * @fechaini fecha de inicio
	 * @fechafin fecha de fin
	 */
	function grafico_ejecuciones($fechaini=null, $fechafin=null, $asignatura_id=null)
	{
		if(!isset($asignatura_id))
			$asignatura_id=$this->_profesor_asignatura_actual();
		
		$ffin = true;
		$fini = true;
		
		if($fechaini != '_')
			$fechaini = date_create($fechaini);
		else
			$fini = false;
			
		if($fechafin != '_')
			$fechafin = date_create($fechafin);
		else
			$ffin = false;
			
		$data = $this->ViewEjecucionesDiario->filtrar_por_fecha($fini, $ffin, $fechaini, $fechafin, $asignatura_id);

		$puntos=array();
		foreach ($data as $row)
		{
			$d = $row['ViewEjecucionesDiario']['dia'];
			$m = $row['ViewEjecucionesDiario']['mes'];
			$yr = $row['ViewEjecucionesDiario']['anyo'];
			
			$x='as.Date("' . $yr .'-'.$m . '-' .$d . '")';
			$y=$row['ViewEjecucionesDiario']['num_ejecuciones'];

			$puntos[]=array($x,$y);
			$href[]="/estadisticas/listado_ejecuciones/$d/$m/$yr";
		}
		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;
		$params=array('href'=>$href);
		
		$eje_y = 'num_ejecuciones';
		$eje_x = __('Fecha');

		$file=$graficos->plot($puntos,'b',array($eje_x,$eje_y),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map('map2');

		$res = array();
		$res['image']= $img_url;
		$res['map'] = $map;
	
		return $res;
	}
}
