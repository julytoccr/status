<?php
App::uses('AppController', 'Controller');
App::import('Vendor', 'simplexml/IsterXmlSimpleXMLImpl');
App::import('Vendor', 'simplexml/array2xml');
/**
 * Problemas Controller
 *
 * @property Problema $Problema
 */
class ProblemasController extends AppController {

   var $access = array
	(
	'*' => array('profesor'),
	'view'=>array('*'),
	);
	
	var $uses = array('Problema','Pregunta','ViewProblemaEliminable','ViewProblemaNoEliminable');
	
	function _comprobar_submits($problema_id){
		if (isset($this->request->data['editar_preguntas'])){
			$this->redirect('/preguntas/index/' . $problema_id);
		}
		elseif (isset($this->request->data['probar_problema'])){
			$this->redirect("/ejecuciones/iniciar_problema/$problema_id/45");
		}
		elseif (isset($this->request->data['generar_xml'])){
			$this->redirect("/problemas/generar_xml/$problema_id");
		}
		else{
			$this->_mensaje(__('mens_problema_guardado'),"/problemas/edit/$problema_id");
		}
	}
	
	function _save($data){
		if (!isset($data['Problema']['id'])){
			$data['Problema']['profesor_id']=$this->_usuario_actual();
		}
		if ($this->Problema->save($data)){
			if (isset($data['Problema']['id'])){
				$problema_id=$data['Problema']['id'];
			}
			else{
				$problema_id=$this->Problema->getInsertID();
			}
			if (!isset($data['Problema']['id'])){
				//se ha creado, guardar carpeta
				$carpeta_id=$data['Problema']['carpeta_id'];
				//TODO: Comprobar carpeta
				if ($this->Problema->CarpetasProblemas->asignar($problema_id,$carpeta_id,false)){
					return $problema_id;
				}
				else{
					return null;
				}
			}
			else{
				return $problema_id;
			}
		}
		return null;
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->set('title_for_layout',__('titulo_administrar_problemas'));
		$this->Problema->recursive = -1;
		$this->set('problemas', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null, $agrupacion_id = null) {
		// No se permite ver la descripcion de los problemas nunca mas (los alumnos intentan hacer plantillas)
		throw new NotFoundException();
		
		$this->Problema->recursive=-1;
		$problema=$this->Problema->findById($id);
		if (!$problema)
		{
			$this->_mensaje(__('error_problema'),'/',true);
		}
		$problema=$problema['Problema'];
		$this->set('title_for_layout',__('problema_problema',$problema['nombre']));
		$preguntas=$this->Problema->Preguntas($id);

		$img_pesos_url = $this->grafico_pesos($preguntas);
		$this->set('img_pesos_url',$img_pesos_url);	

		$this->set(compact('problema','preguntas'));
		$this->set('agrupacion', $agrupacion_id);
	}
	
	function grafico_pesos($preguntas)
	{
		App::import('Vendor', 'estatus/rserve/graficos');

		$graficos = new GraficosRserve();
		$graficos->ancho=400;
		$graficos->alto=400;

		$sum = 0;
		foreach($preguntas as $pre)
		{
			$sum += $pre['peso'];
		}
		
		$vals = array();
		$nombres = array();
		$i = 0;
		foreach($preguntas as $pre)
		{
			$vals[$i] = $pre['peso'] / $sum;
			$nombres[$i] = 'P' . ($i + 1) . ': ' . intval($vals[$i] * 10000)/100 . '%';
			
			$i++;
		}
		
		$valores=array();
		
		$file = $graficos->pastel($vals,$nombres);
		unlink($file);
		
		return $graficos->guardar_grafico_img();
	}
	
	function eliminables()
	{
		$this->set('title_for_layout',__('problemas_no_ejecutados'));
		$this->set('problemas',$this->paginate('ViewProblemaEliminable'));
	}

	function no_eliminables()
	{
		$this->set('title_for_layout',__('problemas_ejecutados'));
		$this->set('problemas',$this->paginate('ViewProblemaNoEliminable'));
	}

/**
 * add method
 *
 * @return void
 */
	
	function add($carpeta_id=null){
		$this->set('action','add');
		if (empty($this->request->data)){
			//uses('sanitize');
			//$carpeta_id=Sanitize::paranoid($carpeta_id);
			$this->set('carpeta_id',$carpeta_id);
		}
		else{
			if (isset($this->request->data['restaurar_xml'])){
				$carpeta_id=$this->request->data['Problema']['carpeta_id'];
				$this->redirect("/problemas/restaurar_xml/0/$carpeta_id");
			}
			$problema_id=$this->_save($this->request->data);
			if ($problema_id!==null){
				$this->_comprobar_submits($problema_id);
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	
	function edit($problema_id=null){
		$profesor_id=$this->_profesor_actual();
		$this->set('action','edit');

		if (empty($this->request->data)){
			$this->Problema->recursive=-1;
        	$this->request->data= $this->Problema->findById($problema_id);
        	if (!$this->request->data){
        		$this->_mensaje(__('error_problema'),'/editor/',true);
        	}
        	if ($this->request->data['Problema']['profesor_id'] != $profesor_id){
        		if (!$this->request->data['Problema']['expresiones_publicas']){
        			$this->redirect("/problemas/view/$problema_id");
        		}
        		$this->set('visualizar',true);
        	}
        	$this->_set_variables_respuestas($problema_id);
			$this->render('add');
		}
		else{
			$this->Problema->recursive=-1;
        	$data= $this->Problema->findById($this->request->data['Problema']['id']);
        	if ($data['Problema']['profesor_id'] != $profesor_id){
        		if (! isset($this->request->data['probar_problema'])){
        			$this->_mensaje(__('error_no_acceso'),'/editor/',true);
        		}
        		else{
        			$this->_comprobar_submits($this->data['Problema']['id']);
        		}
        	}
			if (isset($this->request->data['restaurar_xml'])){
				$problema_id=$this->data['Problema']['id'];
				$this->redirect("/problemas/restaurar_xml/$problema_id/0");
			}
			if ($problema_id=$this->_save($this->request->data)){
				$this->_comprobar_submits($problema_id);
			}
			else{
				$this->_set_variables_respuestas($problema_id);
				$this->render('add');
			}
		}
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	function delete($problema_id){
		$data=$this->ViewProblemaEliminable->find('first',array('conditions'=>
					array('ViewProblemaEliminable.problema_id'=>$problema_id)));
		if ($data){
			//es eliminable
			/*$carpetas=$this->Problema->carpetas($problema_id);
			foreach ($carpetas as $c)
			{
				$this->CarpetasProblemas->desasignar($problema_id,$c['carpeta_id']);
			}*/
			if ($this->Problema->eliminar($problema_id)){
				$this->_mensaje(__('mens_problema_eliminado'),'/problemas/eliminables');
			}
			else{
				$this->_mensaje(__('error_problema_eliminado'),'/problemas/eliminables',true);
			}
		}
	}


	function restaurar_xml($problema_id,$carpeta_id){
		$this->set('problema_id',$problema_id);
		$this->set('carpeta_id',$carpeta_id);

		if (!empty($this->request->data)){
			$this->set('problema_id',$problema_id);
			$this->set('carpeta_id',$carpeta_id);
			$info=$this->request->data['Problema']['XML'];
			if (! strpos($info['type'],'xml')){
				$this->_mensaje(__('error_subir_xml'),"/problemas/restaurar_xml/$problema_id/$carpeta_id",true);
			}
			else{
				$file=$info['tmp_name'];
				$data=array();
				$doc = simplexml_load_file($file);
				if (!isset($doc->Problema)){
					$this->_mensaje(__('error_xml_no_valido'),"/problemas/restaurar_xml/$problema_id/$carpeta_id",true);
				}
				//Parsear problema
				$campos=array(	'nombre','descripcion','enunciado','expresiones',
								'dificultad_id','expresiones_publicas',
								'variables_mostrar');
				foreach ($campos as $c){
					if (!isset($doc->Problema->$c)){
						$this->_mensaje(__('error_fichero_no_valido',array($c)),"/problemas/restaurar_xml/$problema_id/$carpeta_id",true);
					}
					$data['Problema'][$c]="" . $doc->Problema->$c;
				}

				//Parsear preguntas
				$campos=array(	'orden','enunciado','respuesta','sintaxis_respuesta_id',
								'sintaxis_parametro','expresiones_respuesta',
								'expresiones_parametro','peso','intentos','ayuda',
								'link1','link2','mostrar_solucion','mensaje_correcto',
								'mensaje_semicorrecto','mensaje_incorrecto');

				$i=0;
				$continuar=true;
				while ($continuar){
					$p="Pregunta_$i";
					$continuar=isset($doc->Pregunta->$p);
					if ($continuar){
						foreach ($campos as $c){
							if (!isset($doc->Pregunta->$p->$c)){
								$this->_mensaje(__('error_fichero_no_valido',array($c)),"/problemas/restaurar_xml/$problema_id/$carpeta_id",true);
							}
							$data['Pregunta'][$i][$c]="" . $doc->Pregunta->$p->$c;
						}
						$i++;
					}
				}
				//Guardar problema
				if ($carpeta_id){
					$data['Problema']['carpeta_id']=$carpeta_id;
				}
				if ($problema_id){
					$data['Problema']['id']=$problema_id;
				}
				else{
					unset($data['Problema']['id']);
				}
				$this->data=$data;
				$prid=$this->_save($data);
				if (!$prid){
					$this->_mensaje(__('error_problema_guardando'),"/problemas/restaurar_xml/$problema_id/$carpeta_id",true);
				}
				$problema_id=$prid;
				//borrar las ya existentes
				if ($problema_id){
					$this->Pregunta->deleteAll(array('Pregunta.problema_id' => $problema_id));
				}
				//guardar las nuevas
				foreach ($data['Pregunta'] as $preg){
					$this->Pregunta->id=null;
					$preg['problema_id']=$problema_id;
					$this->Pregunta->save($preg);
				}
				$this->redirect("/problemas/edit/$problema_id");
			}
		}
	}
	
	function generar_xml($problema_id){
		$this->autoRender=false;
		$this->Problema->recursive=-1;
       	$data= $this->Problema->findById($problema_id);
       	$data['Pregunta']=$this->Problema->preguntas($problema_id);
       	//Eliminar campos no necesarios
       	$no_nec=array('id','created','modified','published');
       	foreach ($no_nec as $no) unset($data['Problema'][$no]);
       	$no_nec=array('id','created','modified','problema_id');
       	for($i=0;$i<count($data['Pregunta']);$i++){
       		foreach ($no_nec as $no) unset($data['Pregunta'][$i][$no]);
       	}
       	$xml=array2xml($data,'Problema','ProblemaXML');
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"problema_${problema_id}.xml\"");
		header('Content-Transfer-Encoding: binary');
		echo $xml;
		exit;
	}


	/**
	 * Establece el array de variables-respuestas.
	 * El array que retorna Problema->variables es del tipo 0=>'x', 1=>'y'
	 * Se ha de transformar a 'x'=>'x', 'y'=>'y' para el
	 * select Tag
	 * @param integer $problema_id Problema
	 */
	function _set_variables_respuestas($problema_id)
	{
		$variables = $this->Problema->variables($problema_id);
		$ret = array();
		foreach ($variables as $var)
			$ret[$var] = $var;
		$this->set('variables_respuestas_hash',$ret);
		$this->set('variables_respuestas_array',$variables);
	}
}
