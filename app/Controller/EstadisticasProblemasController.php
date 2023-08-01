<?php
/**
 * EstadisticasProblemas Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */
App::import('Vendor','estatus/rserve/graficos');

class EstadisticasProblemasController extends AppController
{
    var $name = 'EstadisticasProblemas';
    var $uses=array('Problema',
					'ViewResultadosPreguntasProblema',
					'ViewMediaResultadosPreguntasProblema',
					'ViewPuntuacionesProblemasAsignatura',
					'Ejecucion',
					'PuntuacionProblema',
					'EstEjecucion');
	var $access = array
	(
	'*' => array('profesor'),
	'alumno_listado_valoraciones' => array('alumno')
	);

	function index($problema_id)
	{
		$asignatura_id = $this->_profesor_asignatura_actual();
		$this->Problema->recursive=-1;
		$problema=$this->Problema->findById($problema_id);
		$this->set('title_for_layout',__('estadisticas_problema_problema',$problema['Problema']['nombre']));
		$estadisticas=$this->Problema->estadisticas($problema_id,$asignatura_id);
		$this->set('estadisticas',$estadisticas);
		$this->set('nombre_problema',$problema['Problema']['nombre']);
		$this->set('problema_id',$problema_id);
	}

	function listado_ejecuciones($problema_id,$orden='created')
	{
		$asignatura_id = $this->_profesor_asignatura_actual();
		$this->Problema->recursive=-1;
		$data=$this->Problema->findById($problema_id);
		$this->set('title_for_layout',__('ejecuciones_problema_problema',$data['Problema']['nombre']));
		switch($orden)
		{
			case 'created':
			case 'nota':
				$o=array("EstEjecucion.$orden"=>'DESC');
				break;
			case 'login':
				$o=array("Usuario.login"=>'ASC');
				break;
			default:
				$o=null;
		}

		$ejecuciones=$this->Ejecucion->ejecuciones_problema($asignatura_id,$problema_id,$o);

		//$ejecuciones=$this->myPagination->paginate_data($ejecuciones,20);
		$this->set('page_url',"/estadisticas_problemas/listado_ejecuciones/$problema_id/$orden");

		$this->set('ejecuciones',$ejecuciones);
		$this->set('problema',$data['Problema']);
		$this->set('problema_id',$problema_id);
	}

	function preguntas($problema_id)
	{
		$asignatura_id = $this->_profesor_asignatura_actual();

		$this->Problema->recursive=-1;
		$data=$this->Problema->findById($problema_id);
		$this->set('title_for_layout',__('estadisticas_preguntas_problema',$data['Problema']['nombre']));
		$this->set('nombre_problema',$data['Problema']['nombre']);

		$estadisticas=$this->ViewMediaResultadosPreguntasProblema->estadisticas($asignatura_id,$problema_id);
		$preguntas=$this->Problema->preguntas($problema_id);

		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=400;

		$estad2=$this->ViewResultadosPreguntasProblema->estadisticas($asignatura_id,$problema_id);

		$valores = array();
		$numpregs = array();
		foreach ($estad2 as $est)
		{
			$valores[] = $est['resultado'];
			$numpregs[] = $est['numero_pregunta'] + 1; //empieza por 0
		}
		
		//Comandos personalizados
		$comandos = array();
				
		$comandos[] = "oldpar <- par(mar=c(2,2,2,2))";
		$comandos[] = "NBands = 11";
		$comandos[] = "resp = round(valores * (NBands - 1)) / (NBands - 1)";
		$comandos[] = "Niv = seq(0, 1, length = NBands)";
		$comandos[] = "N.preg=max(categorias)";
		$comandos[] = "categorias=factor(categorias,level=N.preg:1)";
		$comandos[] = "resp = factor(resp, levels = Niv)";
		$comandos[] = 'labs=paste("#",N.preg:1, sep="")';
		$comandos[] = 'T = table(resp, categorias)';
		$comandos[] = "col.sum <- apply(T, 2, sum)";
		$comandos[] = "col.proportion <- t(t(T) / col.sum)";
		$comandos[] = 'Mypal=colorRampPalette(c("red2", "yellow","green3"))';
		$comandos[] = "barplot(col.proportion, spa=0.8, horiz=TRUE, names=labs, col=Mypal(NBands))";
		$comandos[] = "par(oldpar)";
		
		$file=$graficos->grafico_personalizado($valores, $numpregs, $comandos);
		
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();
		$this->set('img_url',$img_url);

		$this->set('estadisticas',$estadisticas);
		$this->set('problema_id',$problema_id);
		$this->set('preguntas',$preguntas);
	}

	/**
	 * Histograma del número de repeticiones que han hecho los alumnos.
	 * X=número de alumnos distintos que han hecho Y repeticiones
	 */
	function grafico_repeticiones($problema_id)
	{
		$asignatura_id = $this->_profesor_asignatura_actual();

		$this->Problema->recursive=-1;
		$data=$this->Problema->findById($problema_id);
		$this->set('title_for_layout',__('grafico_repeticiones_problema',$data['Problema']['nombre']));
		$this->set('nombre_problema',$data['Problema']['nombre']);

		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;

		$valores=array();
		$this->EstEjecucion->recursive=-1;
		$ejecuciones=$this->EstEjecucion->find('all',array('conditions'=>
				array(	'EstEjecucion.asignatura_id'=>$asignatura_id,
						'EstEjecucion.problema_id'=>$problema_id)));
		foreach ($ejecuciones as $ejec)
		{
			$valores[]=$ejec['EstEjecucion']['alumno_id'];
		}
		$file=$graficos->distribucion_num_repeticiones($valores,array(__('Número repeticiones'),__('Número de alumnos')));
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();

		$this->set('img_url',$img_url);
		$this->set('problema_id',$problema_id);
	}


	/**
	 * Histograma de la opinión de los alumnos sobre la utilidad del problema.
	 * Las opiniones van de 1 (poco útil) a 5 (muy útil)
	 *
	 */
	function grafico_opiniones($problema_id)
	{
		$asignatura_id = $this->_profesor_asignatura_actual();

		$this->Problema->recursive=-1;
		$data=$this->Problema->findById($problema_id);
		$this->set('title_for_layout',__('grafico_opiniones_problema',$data['Problema']['nombre']));
		$this->set('nombre_problema',$data['Problema']['nombre']);

		$graficos = new GraficosRserve();
		$graficos->ancho=700;
		$graficos->alto=600;

		$valores=array();
		$this->EstEjecucion->recursive=-1;
		$puntuaciones=$this->PuntuacionProblema->puntuaciones_problema($problema_id,$asignatura_id);
		foreach ($puntuaciones as $punt)
		{
			//$valores[]=$punt['puntuacion'];
			$valores[]=array($punt['puntuacion'],1);
		}
		$file=$graficos->barplot($valores,array(1),array(__('Puntuación'),__('Número de alumnos')),array('leyenda'=>false));
		//$file=$graficos->histograma($valores,null,array('Puntuación','Número de alumnos'));
		unlink($file);
		$img_url=$graficos->guardar_grafico_img();

		$this->set('img_url',$img_url);
		$this->set('problema_id',$problema_id);
	}
	
	function alumno_listado_valoraciones()
	{
		if (!$this->_es_alumno()) {
			$this->profesor_listado_valoraciones();
		}
		else {
			$this->set('title_for_layout',__('listado_valoracion_problemas'));
			$asignatura_id = $this->_alumno_asignatura_actual();
			$this->recursive = -1;
			$puntuaciones=$this->ViewPuntuacionesProblemasAsignatura->find('all',array('conditions'=>array('ViewPuntuacionesProblemasAsignatura.asignatura_id'=>$asignatura_id)));
			$this->set('puntuaciones', $puntuaciones);
		}
	}
	
	function profesor_listado_valoraciones()
	{
		$this->set('title_for_layout',__('listado_valoracion_problemas'));
		$asignatura_id = $this->_profesor_asignatura_actual();
		$this->recursive = -1;
		$puntuaciones=$this->ViewPuntuacionesProblemasAsignatura->find('all',array('conditions'=>array('ViewPuntuacionesProblemasAsignatura.asignatura_id'=>$asignatura_id)));
		$this->set('puntuaciones', $puntuaciones);
	}
}
