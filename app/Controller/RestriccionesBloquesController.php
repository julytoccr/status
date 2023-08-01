<?php
/**
 * Restricciones Bloques Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class RestriccionesBloquesController extends AppController
{
    var $name = 'RestriccionesBloques';
    var $uses=array('RestriccionesBloque','Bloque','Asignatura');
    var $helpers = array('Html','Form','Js');
    
	var $access = array
	(
	'*' => array('profesor'),
	);

	//TODO: Comprobar que se puede editar las restricciones del bloque indicado
	function index($bloque_id){
		$this->set('bloque_id',$bloque_id);
		$this->recursive=-1;
		$this->paginate = array(
			'conditions' => array('RestriccionesBloque.bloque_id'=>$bloque_id),
			'limit' => 10
		);
		$data = $this->paginate('RestriccionesBloque');
		$this->set('restricciones',$data);
	}


	function add($bloque_id=null){
		//uses('sanitize');
		if(empty($this->request->data)){
			//$bloque_id=Sanitize::paranoid($bloque_id);
			$this->_set_grupos($bloque_id);
			$this->set('bloque_id',$bloque_id);
		}
		else{
			$this->_clean_data();
			$data = $this->request->data;
			$bloque_id=$data['RestriccionesBloque']['bloque_id'];
			if ($this->_save($data)){
				$this->_mensaje(__('mens_restriccion_creada'),'/restricciones_bloques/index/' . $bloque_id);
			}
			else{
				$this->set('bloque_id',$bloque_id);
				$this->_set_activos($this->request->data);
				$this->_set_grupos($bloque_id);
			}
		}
	}

	function delete($restriccion_id)
	{
		$this->RestriccionesBloque->recursive=-1;
		$data=$this->RestriccionesBloque->findById($restriccion_id);
		$bloque_id=$data['RestriccionesBloque']['bloque_id'];

		$this->RestriccionesBloque->delete($restriccion_id);
		$this->_mensaje(__('mens_restriccion_eliminada'),'/restricciones_bloques/index/' . $bloque_id);
	}

	function edit($restriccion_id=null){
		if (empty($this->request->data)){
			//uses('sanitize');
			//$restriccion_id=Sanitize::paranoid($restriccion_id);
			$this->RestriccionesBloque->recursive=-1;
			$data=$this->RestriccionesBloque->findById($restriccion_id);
			if (! $data) $this->_mensaje(__('error_restriccion'),'/bloques/',true);
			$bloque_id=$data['RestriccionesBloque']['bloque_id'];
			$this->_set_grupos($bloque_id);
			$this->request->data=$data;
			$this->_set_activos($this->request->data);
			$this->set('restriccion_id',$restriccion_id);
		}
		else{
			$this->_clean_data();
			$data = $this->request->data;
			$restriccion_id=$data['RestriccionesBloque']['id'];
			$this->RestriccionesBloque->recursive=-1;
			$restriccion=$this->RestriccionesBloque->findById($restriccion_id);
			if (! $restriccion) $this->_mensaje(__('error_restriccion'),'/',true);
			$bloque_id=$restriccion['RestriccionesBloque']['bloque_id'];
			if ($this->_save($data)){
				$this->_mensaje(__('mens_restriccion_modificada'),'/restricciones_bloques/index/' . $bloque_id);
			}
			else{
				$this->_set_activos($this->data);
				$this->_set_grupos($bloque_id);
				$this->render('add');
			}
		}
	}

	function _set_activos(& $data)
	{
			$data['RestriccionesBloque']['activar_fecha_inicio']=
				($data['RestriccionesBloque']['fecha_inicio']!==null);

			$data['RestriccionesBloque']['activar_fecha_fin']=
				($data['RestriccionesBloque']['fecha_fin']!==null);

	}

	function _save($data){
		if ($data['RestriccionesBloque']['grupo']==''){
			$data['RestriccionesBloque']['grupo']=null;
		}
		if (!$data['RestriccionesBloque']['activar_fecha_inicio']){
			$data['RestriccionesBloque']['fecha_inicio']=null;
		}
		if (!$data['RestriccionesBloque']['activar_fecha_fin']){
			$data['RestriccionesBloque']['fecha_fin']=null;
		}
		return $this->RestriccionesBloque->guardar($data['RestriccionesBloque']);
	}

	/**
	 * Establece la variable de vista 'grupos' que contiene
	 * un array asociativo para el select de grupo
	 */
	function _set_grupos($bloque_id){
		$asignatura_id=$this->Bloque->asignatura($bloque_id);
		$num_grupos=$this->Asignatura->grupos($asignatura_id);
		$grupos=array(''=>'Todos');
		foreach ($num_grupos as $num_grupo){
			$grupos[$num_grupo]=$num_grupo;
		}
		$this->set('grupos',$grupos);
	}
}
?>
