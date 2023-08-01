<?php
/**
 * ListadosAlumnos Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */
App::import('Vendor','pear/writer');

class ListadosAlumnosController extends AppController
{
    var $name = 'ListadosAlumnos';
    var $uses=array('Alumno' ,'EstAlumno','Asignatura','Grupo','ViewListadosEstadisticosAlumno');
	var $access = array
	(
	'*' => array('profesor'),
	);
	
	function index()
	{
		$this->set('title_for_layout',__('titulo_listados_alumnos'));
		$asignatura_id=$this->_profesor_asignatura_actual();
		$this->_asignatura_id=$asignatura_id;
		if ($this->request->is('post'))
		{
			$grupo=$this->request->data['Alumno']['grupos'];
			$estadisticos=$this->request->data['Alumno']['estadisticos'];
			switch ($grupo)
			{
				case '_TODOS_ID':
					$alumnos=$this->Asignatura->alumnos($asignatura_id,array('Usuario.login'=>'ASC'));
		 			$fichero="asig_{$asignatura_id}_TODOS_por_identificador.xls";
					break;
				case '_TODOS_GR':
					$alumnos=$this->Asignatura->alumnos($asignatura_id,array('Grupo.nombre'=>'ASC','Usuario.login'=>'ASC'));
					$fichero="asig_{$asignatura_id}_TODOS_por_grupo.xls";
					break;
				default:
					$grupo_id=$this->Grupo->buscar($asignatura_id,$grupo);
					$alumnos=$this->Grupo->alumnos($grupo_id,array('Usuario.login'=>'ASC'));
					$fichero="asig_{$asignatura_id}_grupo_$grupo.xls";
			}

			$workbook = new Spreadsheet_Excel_Writer();

			$this->Asignatura->ViewProblemasAsignatura->contain('Problema');
			$this->Asignatura->ViewProblemasAsignatura->Problema->contain('Dificultad','Agrupacion');
			$this->Asignatura->ViewProblemasAsignatura->Problema->Dificultad->contain();
			$this->Asignatura->ViewProblemasAsignatura->Problema->Agrupacion->contain('Bloque');
			$problemas=$this->Asignatura->problemas($asignatura_id,array('Problema.id'=>'ASC'),3);

			$this->ViewListadosEstadisticosAlumno->recursive=-1;
			$estadisticas=$this->ViewListadosEstadisticosAlumno->find('all',array('conditions'=>array('ViewListadosEstadisticosAlumno.asignatura_id'=>$asignatura_id)));

			if (!is_array($estadisticos)) $estadisticos=array();
			$this->_sacar_listado_num_ejec($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			if ($estadisticos['media'] != 0) $this->_sacar_listado_media($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			if ($estadisticos['max_nota'] != 0) $this->_sacar_listado_max_nota($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			if ($estadisticos['min_nota'] != 0) $this->_sacar_listado_min_nota($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			//if (in_array('primera_nota',$estadisticos)) $this->_sacar_listado_primera_nota($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			//if (in_array('ultima_nota',$estadisticos)) $this->_sacar_listado_ultima_nota($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			if ($estadisticos['num_ejec_ap'] != 0) $this->_sacar_listado_ejec_aprobadas($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			if ($estadisticos['stdev_nota'] != 0) $this->_sacar_listado_stdev_nota($problemas,$alumnos,$estadisticas,$grupo,$workbook);
			$workbook->send($fichero);
			$workbook->close();
		}
		else
		{
			$grupos=$this->Asignatura->grupos($asignatura_id);
			$grupos_nombres=array();
			foreach ($grupos as $grupo) $grupos_nombres[$grupo]="Grupo $grupo";
			$grupos_nombres['_TODOS_ID']='Todos. Orden identificador';
			$grupos_nombres['_TODOS_GR']='Todos. Orden grupo';
			$this->set('grupos',$grupos_nombres);
		}

	}


	function _write($fila, $columna, $cadena,$formato,&$worksheet)
	{
		$worksheet->write($fila,$columna,utf8_decode($cadena),$formato);
	}
	function _crear_hoja($titulo,&$workbook)
	{
		return $workbook->addWorksheet(utf8_decode($titulo));
	}

	function _cabecera_listado($titulo,$grupo,$problemas,$alumnos,$estadisticas,$workbook,$worksheet)
	{
		static $format_bold=null;
		if ($format_bold===null)
		{
		 	$format_bold = $workbook->addFormat();
			$format_bold->setBold();
		}
		$cadena="Listado de $titulo.";
		switch($grupo)
		{
			case '_TODOS_ID':
				$cadena.=__('mens_todos_identificador');
				break;
			case '_TODOS_GR':
				$cadena.=__('mens_todos_grupos');
				break;
			default:
				$cadena.=__('mens_grupo_nombre', array($grupo));
		}
		$this->_write(0,0,$cadena,$format_bold,$worksheet);
		$this->_write(4,0,__('mens_alumno'),$format_bold,$worksheet);
		$alumnos_hash=array();
		$fila=5;
		if (! $alumnos) { $alumnos=array(); }
		foreach ($alumnos as $a)
		{
			if(isset($a['Alumno'])) $a=am($a,$a['Alumno']);

			$alumnos_hash[$a['id']]=$fila;
			$this->_write($fila,0,$a['Usuario']['login'],0,$worksheet);
			$fila++;
		}

		$problemas_hash=array();
		$columna=2;

		foreach ($problemas as $p)
		{
			$problemas_hash[$p['id']]=$columna;
			$this->_write(1,$columna,'P. ' . $p['id'],$format_bold,$worksheet);
			$this->_write(2,$columna,$p['nombre'],$format_bold,$worksheet);
			$this->_write(3,$columna,$p['Dificultad']['nombre'],$format_bold,$worksheet);
			$bloques=array();

			foreach ($p['Agrupacion'] as $ag)
			{
				if ($ag['Bloque']['asignatura_id']==$this->_asignatura_id)
				{
					$bloques[]=trim($ag['Bloque']['nombre']);
				}
			}
			$bloques=join($bloques,', ');
			$this->_write(4,$columna,$bloques,$format_bold,$worksheet);
			$columna++;
		}

		return array($alumnos_hash,$problemas_hash);
	}

	function _sacar_listado_aux($titulo,$field,$problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		static $format_bold=null;
		if ($format_bold===null)
		{
		 	$format_bold = $workbook->addFormat();
			$format_bold->setBold();
		}

		$worksheet = $this->_crear_hoja($titulo,$workbook);
		// Creating the format
		list($alumnos_hash,$problemas_hash)=$this->_cabecera_listado($titulo,$grupo,$problemas,$alumnos,$estadisticas,$workbook,$worksheet);

		$totales=array();
		foreach($estadisticas as $e)
		{
			$e=$e['ViewListadosEstadisticosAlumno'];
			if (!isset($alumnos_hash[$e['alumno_id']])) continue;
			if (!isset($problemas_hash[$e['problema_id']])) continue;
			$fila=$alumnos_hash[$e['alumno_id']];
			$columna=$problemas_hash[$e['problema_id']];

			$this->_write($fila,$columna,$e[$field],0,$worksheet);

			if (! isset($totales[$e['alumno_id']]['num_problemas']))
			{
				$totales[$e['alumno_id']]['num_problemas']=0;
			}
			$totales[$e['alumno_id']]['num_problemas']++;

			if (! isset($totales[$e['alumno_id']]['num_ejec']))
			{
				$totales[$e['alumno_id']]['num_ejec']=0;
			}
			$totales[$e['alumno_id']]['num_ejec']+=$e['numero_ejecuciones'];
		}

		$fila=4;
		$columna=max($problemas_hash) + 1;
		$this->_write($fila,$columna,'#Prob',$format_bold,$worksheet);
		$this->_write($fila,$columna+1,'#Ejec',$format_bold,$worksheet);

		foreach ($totales as $alumno_id => $value)
		{
			$fila=$alumnos_hash[$alumno_id];

			$this->_write($fila,$columna,$value['num_problemas'],0,$worksheet);
			$this->_write($fila,$columna+1,$value['num_ejec'],0,$worksheet);
		}
	}

	function _sacar_listado_num_ejec($problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		$this->_sacar_listado_aux(__('mens_num_ejecuciones'),'numero_ejecuciones',$problemas,$alumnos,$estadisticas,$grupo,$workbook);
	}

	function _sacar_listado_media($problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		$this->_sacar_listado_aux(__('mens_nota_media'),'nota_media',$problemas,$alumnos,$estadisticas,$grupo,$workbook);
	}

	function _sacar_listado_max_nota($problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		$this->_sacar_listado_aux(__('mens_nota_maxima'),'maxima_nota',$problemas,$alumnos,$estadisticas,$grupo,$workbook);
	}

	function _sacar_listado_min_nota($problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		$this->_sacar_listado_aux(__('mens_nota_minima'),'minima_nota',$problemas,$alumnos,$estadisticas,$grupo,$workbook);
	}

	function _sacar_listado_ejec_aprobadas($problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		$this->_sacar_listado_aux(__('mens_num_ejecuciones_aprobadas'),'numero_ejecuciones_aprobadas',$problemas,$alumnos,$estadisticas,$grupo,$workbook);
	}

	function _sacar_listado_stdev_nota($problemas,$alumnos,$estadisticas,$grupo,&$workbook)
	{
		$this->_sacar_listado_aux(__('mens_desviacion_nota'),'desv_nota',$problemas,$alumnos,$estadisticas,$grupo,$workbook);
	}
}
