<?php
/**
 * Editor Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

//TODO: No parece que se controle si las carpetas son propias o no en algunas acciones en que es importante
class EditorController extends AppController
{
    var $name = 'Editor';
    var $uses=array('Problema','Carpeta','Profesor','ViewProblemasCarpetas','CarpetasProblemas','ViewBuscarProblema');
    var $helpers=array('Html','Tree','Tooltip');
    
    /*var $access = array(
		'profesor' => '*',
		'admin' => '*',
	);*/
	
	var $access = array
	(
	'*' => array('profesor'),
	);
		
	public function beforeFilter(){
		parent::beforeFilter();
		if ($this->request->isAjax()) {
			$this->layout = 'ajax';
		}
	}

	function index()
	{
		$profesor_id=$this->_usuario_actual();
		$propias=$this->Profesor->carpetas_propias($profesor_id);
		$subscritas=$this->Profesor->carpetas_externas_subscritas($profesor_id);
		$no_subscritas=$this->Profesor->carpetas_externas_no_subscritas($profesor_id);
		$carpetas=$this->Carpeta->carpetas();

		$problemas=$this->ViewProblemasCarpetas->problemas();

		//eliminar de $carpetas las que no estan en las carpetas visibles
		foreach ($carpetas as $i=>$c)
		{
			$id=$c['Carpeta']['id'];
			$encontrada=false;
			foreach ($propias as $c2)
			{
				if ($c2['id']==$id || $encontrada)	{ $encontrada=true; break; }
			}
			foreach ($subscritas as $c2)
			{
				if ($c2['id']==$id || $encontrada)	{ $encontrada=true; break;	}
			}
			foreach ($no_subscritas as $c2)
			{
				if ($c2['id']==$id || $encontrada)	{ $encontrada=true; break;	}
			}
			if (!$encontrada) unset($carpetas[$i]);
		}

		foreach ($problemas as $i=>$prob)
		{
			$carpeta_id=$prob['carpeta_id'];
			$encontrada=false;
			foreach ($carpetas as $c)
			{
				if ($c['Carpeta']['id']==$carpeta_id)	{ $encontrada=true; break; }
			}
			if (! $encontrada) unset($problemas[$i]);
		}

		$this->set('propias',$propias);
		$this->set('subscritas',$subscritas);
		$this->set('no_subscritas',$no_subscritas);
		$this->set('carpetas',$carpetas);
		$this->set('problemas',$problemas);
		$this->set('action','edit');
	}

	function crear_carpeta()
	{
		if (empty($this->request->data['Carpeta']['nombre'])){
			$this->render('crear_carpeta/form');
		}
		else{
			//$data=$this->_clean_data($this->data);
			$data = $this->request->data;
			$data['Carpeta']['profesor_id']=$this->_usuario_actual();
			if ($this->Carpeta->save($data)){
				$this->_mensaje(__('mens_carpeta_creada'),'/editor/');
			}
			else{
				$this->_mensaje(__('error_carpeta_creada'),'/editor/',true);
			}
		}
	}

	function crear_problema()
	{
		$form=$this->request->data;
		$data=$this->_convertir_checkbox_info($form);
		$carpeta_id=null;
		$error=false;
		foreach ($data as $info){
			//se trata de una carpeta
			if (count($info)==4){
				if ($carpeta_id===null){
					$carpeta_id=$info[3];
				}
				else{
					$error=true;
				}
			}
		}
		if ($error || $carpeta_id===null){
			$this->_mensaje(__('mens_marcar_carpeta_destino'),'/editor');
		}
		$this->ajax_redirect('/problemas/add/' . $carpeta_id);
	}

	function publicar_problemas()
	{
		$data=$this->_convertir_checkbox_info($this->request->data);
		foreach ($data as $info)
		{
			//se trata de un problema
			if (count($info)==5)
			{
				$carpeta_id=$info[3];
				$problema_id=$info[4];
				//solo se pueden publicar problemas propios
				$profesor_id=$this->_usuario_actual();

				if (! $this->Problema->publicar($problema_id,$profesor_id))
				{
					$this->_mensaje(__('Error publicando, el problema no te pertenece'),'/editor');
					
				}
			}
			else
			{
				$this->_mensaje(__('desmarcar'),'/editor');
			}
		}
		$this->_mensaje(__('mens_publicados_despublicados'),'/editor');
	}

	//TODO: No hay mensajes creados para esta funcionaliad, falta crear mensajes en todos los idiomas
	//TODO: La manera en que se comprueba si la carpeta es propia no es muy eficiente
	//		A lo mejor se puede extraer esa informacion directamente de la informacion del formulario
	function compartir_carpetas()
	{
		$data=$this->_convertir_checkbox_info($this->request->data);
		$error = false;
		$sel_carp = false;
		foreach ($data as $info){
			//se trata de una carpeta propia
			if (count($info) == 4 && $this->Carpeta->propia($info[3], $this->_usuario_actual())){
				$sel_carp = true;
				$carp_id = $info[3];	
				if(!$this->Carpeta->cambiar_compartido($carp_id)){
					$error = true;
					echo "Error compartiendo/descompartiendo $carp_id";
				}
			}
		}
		
		if(!$sel_carp){
			$this->_mensaje(__('error_no_carpeta_propia'),'/editor');
		}	
		else if($error){
			$this->_mensaje(__('error_editor_compartirdescompartir'),'/editor');
		}
		else{
			$this->_mensaje(__('editor_compartidodescompartido'),'/editor');
		}
	}

	function subscribir_carpetas()
	{
		$this->autoRender=false;
		$data=$this->_convertir_checkbox_info($this->request->data);
		foreach ($data as $info){
			//se trata de una carpeta
			if (count($info)==4){
				$carpeta_id=$info[3];
				$profesor_id=$this->_usuario_actual();
				if ($this->Profesor->subscribir($profesor_id,$carpeta_id)){
					$this->_mensaje(__('mens_subscritas_nosubscritas'),'/editor');
				}
			}
			else{
				echo "Desmarcar 'chk_{$info[0]}'";
			}
		}
	}

	function eliminar_problemas(){
		$data=$this->_convertir_checkbox_info($this->request->data);
		foreach ($data as $info){
			//se trata de un problema
			if (count($info)==5){
				$carpeta_id=$info[3];
				$problema_id=$info[4];
				if (!$this->CarpetasProblemas->desasignar($problema_id,$carpeta_id)){
					echo "Error $problema_id $carpeta_id";
				}
			}
		}
		$this->_mensaje(__('mens_eliminados_carpetas'),'/editor/');
	}

	//TODO: Copiar cambia propietario
	function copiar_problemas(){
		$data=$this->_convertir_checkbox_info($this->request->data);
		$problemas=array();
		$carpeta_id=null;
		$error=false;
		foreach ($data as $info){
			//se trata de un problema
			if (count($info)==5){
				$problemas[]=$info[4];
			}
			elseif (count($info)==4){
				if ($carpeta_id ===null){
					$carpeta_id=$info[3];
				}
				else{
					$error=true;
				}
			}
		}
		if ($error || ! count($problemas) || $carpeta_id===null){
			$this->_mensaje(__('error_copiar_problemas_carpeta'),'/editor/');

		}
		else{
			foreach ($problemas as $problema_id){
				if(!$this->Problema->copiar($problema_id,$carpeta_id)){
					$error=true;
				}
			}
		}
		if ($error){
			$this->_mensaje(__('error_problemas_copiados'),'/editor/');
		}
		else{
			$this->_mensaje(__('mens_problemas_copiados'),'/editor/');
		}
	}

	function mover_problemas()
	{
		$data=$this->_convertir_checkbox_info($this->request->data);
		$problemas=array();
		$carpeta_destino_id=null;
		$error=false;
		foreach ($data as $info){
			//se trata de un problema
			if (count($info)==5){
				$problemas[]=array('problema_id'=>$info[4],'carpeta_id'=>$info[3]);
			}
			elseif (count($info)==4){
				if ($carpeta_destino_id ===null){
					$carpeta_destino_id=$info[3];
				}
				else{
					$error=true;
				}
			}
		}
		if ($error || ! count($problemas) || $carpeta_destino_id===null)
		{
			$this->_mensaje(__('error_copiar_problemas_carpeta'),'/editor/');
		}
		else
		{
			foreach ($problemas as $p){
				$carpeta_origen_id=$p['carpeta_id'];
				$problema_id=$p['problema_id'];
				if (! $this->Problema->mover($problema_id,$carpeta_origen_id,$carpeta_destino_id)){
					$error=true;
				}
			}
		}
		if ($error){
			$this->_mensaje(__('error_problemas_copiados'),'/editor/');
		}
		else{
			$this->_mensaje(__('mens_problemas_movidos'),'/editor/');
		}
	}

	function copiar_alias_problemas()
	{
		$data=$this->_convertir_checkbox_info($this->request->data);
		$problemas=array();
		$carpeta_id=null;
		$error=false;
		foreach ($data as $info){
			//se trata de un problema
			if (count($info)==5){
				$problemas[]=$info[4];
			}
			elseif (count($info)==4){
				if ($carpeta_id===null){
					$carpeta_id=$info[3];
				}
				else{
					$error=true;
				}
			}
		}
		if ($error || ! count($problemas) || $carpeta_id===null){
			$this->_mensaje(__('error_copiar_problemas_carpeta'),'/editor/');
		}
		else{
			foreach ($problemas as $problema_id){
				if (! $this->Problema->copiar_alias($problema_id,$carpeta_id)){
					$error=true;
				}
			}
		}
		if ($error){
			$this->_mensaje(__('error_problemas_copiados'),'/editor/');
		}
		else{
			$this->_mensaje(__('mens_problemas_movidos'),'/editor/');
		}
	}

	/**
	 * Convierte el formulario de checkboxes del tipo:
	 * $form['problemas_propias_2_1']
	 * $form['carpetas_subs_7']
	 *
	 * en el array:
	 * $result[0]=array('problemas_propias_2_1','problemas','propias',2,1)
	 * $result[1]=array('carpetas_subs_7','carpetas','subs',7)
	 *
	 * @param array $form Formulario de checkboxes
	 * @return array
	 */
	function _convertir_checkbox_info($form)
	{
		$result=array();
		foreach ($form as $item=>$value)
		{
			$item_data=split('_', $item);
			if ($item_data[0]!='')
			{
				$result[]=am(array($item),$item_data);
			}
		}
		return $result;
	}

	function buscar_problemas()
	{
		if (empty($this->request->data))
		{
			$this->render('/Elements/tree/buscar');
		}
		else
		{
			//$data=$this->_clean_data();
			$data = $this->request->data;
			$this->ViewBuscarProblema->recursive=2;
			$this->ViewBuscarProblema->Problema->contain('Carpeta');
			$problemas=$this->ViewBuscarProblema->buscar($data['Problema']['nombre'],$data['Problema']);
			//$problemas=$this->Problema->buscar($search);
			$this->set('problemas',$problemas);
			$this->render('/Elements/tree/buscar_tree');
		}
	}
}
?>
