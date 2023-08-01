<?php
/**
 * Historial Alumnos Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */
App::import('Vendor', 'estatus/rserve/graficos');

class HistorialAlumnosController extends AppController
{
    var $name = 'HistorialAlumnos';
    var $uses=array('Ejecucion','Alumno','Grupo','Problema','EstAlumno','EstEjecucion','Problema','ViewPuntuacionBloque','EstRankingEjecucion','ViewPuntuacionesProblemasAlumno','ViewEjecucionesPerdidas','ViewEjecucionesDatosExtra','Bloque','Asignatura');
	var $components = array('CustomPaginate');
	var $helpers = array('Html','Formato','CustomPagination');
	var $access = array
	(
	'*' => array('alumno','profesor'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('menu_historial'));
    }
	
	function index($alumno_id=0){
		$this->_sidebar($alumno_id);
		$es_profe = $alumno_id != 0;
		$this->set('es_profe', $es_profe);

		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		$this->Alumno->recursive=0;
		$this->Alumno->contain('Grupo','Usuario');
		$alumno=$this->Alumno->findById($alumno_id);
		$this->set('alumno',$alumno);

		$estadisticas=$this->Alumno->estadisticas($alumno_id);
		$this->set('estadisticas',$estadisticas);

		$grupo_id=$this->Alumno->grupo($alumno_id);
		$grupo=$this->Grupo->nombre_grupo($grupo_id);

		$this->ViewPuntuacionesProblemasAlumno->recursive=-1;
		$tmp=$this->ViewPuntuacionesProblemasAlumno->find('all',array('conditions'=>
			array('ViewPuntuacionesProblemasAlumno.alumno_id'=>$alumno_id),
			array('problema_id','puntuacion_problema','nota_media_problema','num_ejecuciones_problema','tiempo_total')
			));

		$estadisticas=array();
		foreach($tmp as $row)
		{
			$row=$row['ViewPuntuacionesProblemasAlumno'];
			$estadisticas[$row['problema_id']]=$row;
		}

		$tieneDatos = false;
		$data=$this->Asignatura->bloques($asignatura_id,null);
		$k = 0;
		for ($i=0;$i<count($data);$i++)
		{
			$data[$k]['Problema']=$this->Bloque->problemas($data[$i]['Bloque']['id'], false);
			foreach ($data[$k]['Problema'] as $j=>$prob)
			{
				if (isset($estadisticas[$prob['Problema']['id']]))
				{
					$data[$k]['Problema'][$j]['Estadisticas']=$estadisticas[$prob['Problema']['id']];
					$tieneDatos = true;
				}
			}
			
			if(!$tieneDatos)//Solo añadimos los bloques que tienen ejecuciones para este alumno
			{
				unset($data[$k]['Problema']);
			}
			else
			{
				$data[$k]['Bloque'] = $data[$i]['Bloque'];
				$k++;
			}
			
			$tieneDatos = false;
		}
		$this->set('data',$data);
	}

	function ejecuciones_perdidas($alumno_id=0){
		$this->_sidebar($alumno_id);
		$es_profe = $alumno_id != 0;
		$this->set('es_profe', $es_profe);
		$this->set('alumno_id',$alumno_id);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);
		$this->ViewEjecucionesPerdidas->recursive=-1;
		$tmp=$this->ViewEjecucionesPerdidas->find('all',array('conditions'=>
			array('ViewEjecucionesPerdidas.alumno_id'=>$alumno_id)
		));
		$counter=0;
		$lost=array();
		foreach($tmp as $row){
			$row=$row['ViewEjecucionesPerdidas'];
			//$row['nota'] = $this -> Ejecucion -> devolver_nota($row['ejecucion_id'], $row['problema_id'], $this -> _usuario_actual(), $this -> _es_alumno(), $this -> _es_profesor(), $this -> _es_administrador());
			$lost[$row['problema_id']][$counter++]=$row;
		}
		$tieneDatos = false;
		$tmp=$this->Asignatura->bloques($asignatura_id,null);
		$data=array();
		$k = 0;
		for ($i=0;$i<count($tmp);$i++){
			$tmp2=$this->Bloque->problemas($tmp[$i]['Bloque']['id'], false);
			$data[$k]=array();
			$data[$k]['Problemas']=array();
			foreach ($tmp2 as $j=>$prob){
				if (isset($lost[$prob['Problema']['id']])){
					$data[$k]['Problemas'][$prob['Problema']['id']] = $lost[$prob['Problema']['id']];
					$data[$k]['Problemas'][$prob['Problema']['id']]['nombre'] = $prob['Problema']['nombre'];
					$tieneDatos = TRUE;
				}
			}
			if(!$tieneDatos)//Solo añadimos los bloques que tienen ejecuciones para este alumno
			{
				unset($data[$k]);
			}
			else
			{
				$data[$k]['Bloque'] = $tmp[$i]['Bloque'];
				$k++;
			}
			
			$tieneDatos = false;
		}
		
		$this->set('data',$data);
		$this->render('ejecuciones_perdidas/mostrar');
	}

	function confirmar_recuperacion($ejecucion_id, $alumno_id=0){
		$this->_sidebar($alumno_id);
		$es_profe = $alumno_id != 0;
		$this->set('es_profe', $es_profe);
		$this->set('alumno_id', $alumno_id);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		$this->ViewEjecucionesPerdidas->recursive=-1;
		$row = $this->ViewEjecucionesPerdidas->find('first',array('conditions'=>array('ejecucion_id'=>$ejecucion_id)));
    	$this -> ViewEjecucionesDatosExtra -> recursive = -1;
		$extra_data = $this -> ViewEjecucionesDatosExtra -> findByEjecucionId($ejecucion_id);
		if (empty($extra_data)) {
			$this -> _mensaje(__('error_general'), '/', true);
		}
		$extra_data = $extra_data['ViewEjecucionesDatosExtra'];
		$nota   = $this -> Ejecucion -> devolver_nota($row['ViewEjecucionesPerdidas']['ejecucion_id'],
									    $row['ViewEjecucionesPerdidas']['problema_id'],
									    $extra_data['usuario_id'],
									    TRUE,
									    ($extra_data['es_profe'] == 1),
									    ($extra_data['es_admin'] == 1));
		$problema = $this->Problema->findById($row['ViewEjecucionesPerdidas']['problema_id'],array('nombre'));
		if ($nota === NULL) {
			// La ejecución no es del alumno o no está perdida o no existe
			$this -> _mensaje(__('error_no_acceso'), '/', true);
		}
		$this->set('problema_nombre',$problema['Problema']['nombre']);
		$this->set('nota',$nota);
		$this->set('ejecucion_id',$ejecucion_id);
		$this->render('ejecuciones_perdidas/confirmar');
	}

	function grafico_alumnos_activos_form($alumno_id=0)
	{
		$this->_sidebar($alumno_id);
	}

	function grafico_alumnos_activos($alumno_id=0)
	{
		$this->_sidebar($alumno_id, true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		$this->EstAlumno->recursive=-1;
		$data=$this->EstAlumno->find('all',array('conditions'=>array('EstAlumno.asignatura_id'=>$asignatura_id),'order'=>array('EstAlumno.num_ejecuciones'=>'desc')));
		$data_y=array();
		foreach ($data as $line)
		{
			$data_y[]=$line['EstAlumno']['num_ejecuciones'];
		}

		vendor('/estatus/rserve/estatus_rserve');

		$rserve=& new EstatusRserve();

		$rserve->enviar_array($data_y,'data_y');

		$rserve->cache=true;
		$rserve->evaluar('grafico<-ini_imagen(700,500)');
		$rserve->evaluar("barplot(data_y,space=1,
				main=__('Alumnos más activos'),ylab=__('Problemas ejecutados'), xlab=__('Alumnos'))");
		$rserve->evaluar('fin_imagen()');
		$rserve->evaluar_cache();
		vendor('/estatus/rserve/graficos');
		$graficos=& new GraficosRserve($rserve);
		$graficos->mostrar('grafico');
		exit;
	}

	function grafico_ejecuciones_vs_puntuacion($alumno_id=0){
		$this->set('title_for_layout',__('grafico_ejecuciones_vs_puntuacion'));
		$this->_sidebar($alumno_id, true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);
		$this->EstAlumno->contain('Alumno');
		$data=$this->EstAlumno->find('all',array('conditions'=>array('EstAlumno.asignatura_id'=>$asignatura_id)));

		$puntos=array();
		$titles=array();
		$colores=array();
		$href=array();
		$marcas=array();

		//uses('sanitize');
		foreach ($data as $line)
		{
			$x=$line['EstAlumno']['num_ejecuciones'];
			$y=$line['EstAlumno']['puntuacion_total'];

			$puntos[]=array($x,$y);
			//$alias=Sanitize::paranoid($line['Alumno']['alias'],array(' '));
			$alias = $line['Alumno']['alias'];
			$titles[]=$alias . " (nota {$line['EstAlumno']['nota_media']},punt {$line['EstAlumno']['puntuacion_total']}, ejec {$line['EstAlumno']['num_ejecuciones']})";
			$href[]='#';
			if ($alumno_id!=$line['EstAlumno']['alumno_id'])
			{
				$colores[]=1;
			}
			else
			{
				$colores[]=2;
				$marcas[]=array($x,$y,2);
			}
		}

		$graficos= new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;
		$params=array(	'title'=>$titles,
						'href'=>$href,
						'colores'=>$colores,
						'marcas'=>$marcas);


		$file=$graficos->plot($puntos,'p',array(__('Número ejecuciones'),__('Puntuación')),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map();

		$this->set('img_url',$img_url);
		$this->set('map',$map);


	}

	/**
	 * Sobre un alumno, Y: nota, X: fecha ejecución
	 */
	function grafico_nota_tiempo_ejecuciones($alumno_id=0)
	{
		$this->set('title_for_layout',__('grafico_nota_vs_tiempo'));
		$es_profesor= ($alumno_id != 0);
		$this->_sidebar($alumno_id, true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		//Extraer los nombres de los problemas
		$nombre_problema=array();
		$this->Problema->recursive=-1;
		$data=$this->Problema->find('all',array('fields' => array('id', 'nombre')));
		foreach ($data as $row) $nombre_problema[$row['Problema']['id']]=$row['Problema']['nombre'];

		$cond=array('EstEjecucion.alumno_id'=>$alumno_id);
		$this->EstEjecucion->recursive=-1;
		$data=$this->EstEjecucion->find('all',array('conditions'=>$cond,'order'=>array('EstEjecucion.created'=>'ASC')));
		if (!$data){
			$this->set('no_data',true);
			return;
		}
		$puntos=array();
		$colores=array();
		$leyenda=array();
		$href=array();
		$title=array();
		//uses('sanitize');

		foreach ($data as $row)
		{
			$x=strftime("%Y-%m-%d",strtotime($row['EstEjecucion']['created']));
			$x='as.Date("' . $x . '")';

			$y=$row['EstEjecucion']['nota'];
			$problema_id=$row['EstEjecucion']['problema_id'];
			$colores[]=$problema_id;

			$leyenda[$problema_id]=trim($nombre_problema[$problema_id]);

			$puntos[]=array($x,$y);

			if ($es_profesor!=0)
			{	//soy profesor
				$href[]="/ejecuciones/profesor_mostrar_resultado/{$row['EstEjecucion']['ejecucion_id']}";
			}
			else
			{
				$href[]="/ejecuciones/alumno_mostrar_resultado/{$row['EstEjecucion']['ejecucion_id']}";
			}

			$fecha=strftime("%d-%m-%Y",strtotime($row['EstEjecucion']['created']));
			$title[]=trim($nombre_problema[$problema_id]). " (Nota: $y, Fecha: $fecha)";
		}
		$graficos= new GraficosRserve();
		$graficos->ancho=720;
		$graficos->alto=600;

		$params=array(	'leyenda'=>$leyenda,
						'colores'=>$colores,
						'href'=>$href,
						'title'=>$title,
						'chars'=>4);

		$file=$graficos->plot($puntos,'b',array(__('Fecha'),__('Nota')),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map();

		$this->set('img_url',$img_url);
		$this->set('map',$map);

	}

	/**
	 * Sobre un alumno, Y: nota, X: orden ejecución (eje oculto)
	 */
	function grafico_nota_orden_ejecuciones($alumno_id=0)
	{
		$this->set('title_for_layout',__('grafico_nota_vs_orden_ejecucion'));
		$es_profesor= ($alumno_id != 0);
		$this->_sidebar($alumno_id, true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);
		//Extraer los nombres de los problemas
		$nombre_problema=array();
		$this->Problema->recursive=-1;
		$data=$this->Problema->find('all',array('fields' => array('id', 'nombre')));
		foreach ($data as $row) $nombre_problema[$row['Problema']['id']]=$row['Problema']['nombre'];

		$cond=array('EstEjecucion.alumno_id'=>$alumno_id);
		$this->EstEjecucion->recursive=-1;
		$data=$this->EstEjecucion->find('all',array('conditions'=>$cond,'order'=>array('EstEjecucion.created'=>'ASC')));
		if (!$data)
		{
			$this->set('no_data',true);
			return;
		}

		$puntos=array();
		$colores=array();
		$leyenda=array();
		$href=array();
		$title=array();
		//uses('sanitize');

		$i=1;
		foreach ($data as $row)
		{
			$x=$i++;

			$y=$row['EstEjecucion']['nota'];
			$problema_id=$row['EstEjecucion']['problema_id'];
			$colores[]=$problema_id;

			$leyenda[$problema_id]=trim($nombre_problema[$problema_id]);


			$puntos[]=array($x,$y);
			if ($es_profesor)
			{	//soy profesor
				$href[]="/ejecuciones/profesor_mostrar_resultado/{$row['EstEjecucion']['ejecucion_id']}";
			}
			else
			{
				$href[]="/ejecuciones/alumno_mostrar_resultado/{$row['EstEjecucion']['ejecucion_id']}";
			}

			//$href[]=WEBROOT_URL . "index.php/ejecuciones/alumno_mostrar_resultado/{$row['EstEjecucion']['ejecucion_id']}";

			$fecha=strftime("%d-%m-%Y",strtotime($row['EstEjecucion']['created']));
			$title[]=trim($nombre_problema[$problema_id]). " (Nota: $y, Fecha: $fecha)";
		}
		$graficos= new GraficosRserve();
		$graficos->ancho=720;
		$graficos->alto=600;

		$params=array(	'leyenda'=>$leyenda,
						'colores'=>$colores,
						'href'=>$href,
						'title'=>$title,
						'no_eje_x'=>true,
						'chars'=>4);

		$file=$graficos->plot($puntos,'b',array(__('Fecha'),__('Nota')),$params);
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$map=$graficos->print_map();

		$this->set('img_url',$img_url);
		$this->set('map',$map);

	}


	function listado_ejecuciones($alumno_id=0){
		$this->_sidebar($alumno_id);
		$es_profesor= ($alumno_id != 0);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);
		$this->paginate = array(
			'conditions' => array('EstEjecucion.alumno_id'=>$alumno_id),
			'order' => array('EstEjecucion.created'=>'ASC'),
			'limit' => 10
		);
		$this->EstEjecucion->contain('Problema');
		$data = $this->paginate('EstEjecucion');
		$this->set('es_profesor',$es_profesor);
		$this->set('data',$data);
	}
	
	function listado_ejecuciones_problema($alumno_id=0, $problema_id)
	{
		$this->set('page_url',"/historial_alumnos/listado_ejecuciones_problema/$alumno_id/$problema_id");

		$this->_sidebar($alumno_id);
		$es_profesor= ($alumno_id != 0);

		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		//$cond = "alumno_id = $alumno_id AND problema_id = $problema_id";
		$cond=array('EstEjecucion.alumno_id'=>$alumno_id, 'EstEjecucion.problema_id' => $problema_id);
		$this->EstEjecucion->contain('Problema');
		//$data=$this->EstEjecucion->findAll($cond,null,"created = ASC");
		$data=$this->EstEjecucion->find('all',array('conditions'=>array($cond),'order'=>array('EstEjecucion.created'=>'ASC')));

		$data=$this->CustomPaginate->paginate_data($data,20);

		$this->set('es_profesor',$es_profesor);
		$this->set('data',$data);
	}

	function puntuacion_bloques($alumno_id=0)
	{
		$this->_sidebar($alumno_id);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);
		$this->ViewPuntuacionBloque->contain('Bloque');
		$data=$this->ViewPuntuacionBloque->find('all',array('conditions'=>array('alumno_id'=>$alumno_id)));
		$this->set('data',$data);
	}

	function ranking_ejecuciones($alumno_id=0){
		$this->set('title_for_layout',__('menu_ranking_ejecuciones'));
		$this->_sidebar($alumno_id, true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		$data = $this->Asignatura->ranking_ejecuciones($asignatura_id, false);
		$img_url=$this->_ranking_grafico($data,$alumno_id,'num_ejecuciones',array(__('Num ejecuciones'),__('# Alumnos')));
		$this->set('img_url',$img_url);
		$this->set('data',$data);
	}


	function ranking_nota($alumno_id=0){
		$this->set('title_for_layout',__('menu_ranking_nota'));
		$this->_sidebar($alumno_id,true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);

		$data = $this->Asignatura->ranking_nota($asignatura_id, false);
		$img_url=$this->_ranking_grafico($data,$alumno_id,'nota_media',array(__('Nota media'),__('# Alumnos')));
		$this->set('img_url',$img_url);
		$this->set('data',$data);
	}

	function ranking_puntuacion($alumno_id=0){
		$this->set('title_for_layout',__('menu_ranking_puntuacion'));
		$this->_sidebar($alumno_id,true);
		list($alumno_id,$asignatura_id)=$this->_extraer_identificadores($alumno_id);
		$data = $this->Asignatura->ranking_puntuacion($asignatura_id, false);
		
		$img_url=$this->_ranking_grafico($data,$alumno_id,'puntuacion_total',array(__('Puntuación'),__('# Alumnos')));
		$this->set('img_url',$img_url);
		$this->set('data',$data);
	}
	
	function graficos($alumno_id=0){
		$this->_sidebar($alumno_id, true);
	}

	function _ranking_grafico($data,$alumno_id,$campo,$nombre_ejes){
		$graficos= new GraficosRserve();
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


	function _sidebar($alumno_id, $grafico=false){
		if (!$alumno_id){
			$alumno_id='';
			$id=$this->_alumno_actual();
		}
		else{
			$id=$alumno_id;
		}
		$this->Alumno->contain('Usuario');
		$data=$this->Alumno->findById($id);
		$login=$data['Usuario']['login'];
		$this->set('login',$login);
		$this->set('alumno_id',$alumno_id);
		if($grafico)
			$this->set('grafico',true);
	}

	function _extraer_identificadores($alumno_id){
		if ($alumno_id){
			$asignatura_id=$this->Alumno->asignatura($alumno_id);
			if ($asignatura_id != $this->_profesor_asignatura_actual()){
				$this->_mensaje(__('error_no_acceso'),'/',true);
			}
		}
		else{
			$alumno_id=$this->_alumno_actual();
			$asignatura_id=$this->_alumno_asignatura_actual();
		}
		$asignatura_id=$this->Alumno->asignatura($alumno_id);
		return array($alumno_id,$asignatura_id);
	}

}
