<?php
/**
 * EncuestasAsignaturas Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class EncuestasAsignaturasController extends AppController
{
    var $name = 'EncuestasAsignaturas';
    var $uses=array('EncuestasAsignatura','Encuesta','Grupo', 'Asignatura');
    
    var $access = array
	(
	'*' => array('profesor'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('encuestas_asociadas'));
    }

	function index(){
		$asignatura_id=$this->_profesor_asignatura_actual();
		$this->recursive=1;
		$this->EncuestasAsignatura->contain('Encuesta');
		$this->paginate = array('conditions' => array('EncuestasAsignatura.asignatura_id'=>$asignatura_id));
		$data_tmp = $this->paginate('EncuestasAsignatura');
		$data=array();
		foreach ($data_tmp as $row){
			if ($this->EncuestasAsignatura->es_activa($row['EncuestasAsignatura'])){
				$data[]=$row;
			}
		}
		$this->set('encuestas',$data);
	}

	function add($encuesta_id = null){
		//uses('sanitize');
		if (empty($this->request->data)){
			//$encuesta_id=Sanitize::paranoid($encuesta_id);
			$this->set('encuesta_id',$encuesta_id);
		}
		else{
			//$data = $this->_clean_data();
			$data = $this->request->data;
			$asignatura_id=$this->_profesor_asignatura_actual();
			$data['EncuestasAsignatura']['asignatura_id']=$asignatura_id;
			if ($this->EncuestasAsignatura->save($data)){
				$this->_mensaje(__('mens_encuesta_asignada'),'/encuestas_asignaturas');
			}
		}
	}
	
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->EncuestasAsignatura->id = $id;
		if (!$this->EncuestasAsignatura->exists()) {
			throw new NotFoundException(__('Invalid usuario'));
		}
		if ($this->EncuestasAsignatura->delete($id)) {
			$this->_mensaje(__('mens_encuesta_desasignada'),'/encuestas_asignaturas');
		}
		$this->_mensaje(__('error_desasignando'),'/encuestas/',true);
	}

	function edit($encuestas_asignatura_id=null){
		if(empty($this->request->data)){
			//uses('sanitize');
			//$encuestas_asignatura_id=Sanitize::paranoid($encuestas_asignatura_id);
			$this->EncuestasAsignatura->recursive=-1;
			$data=$this->EncuestasAsignatura->findById($encuestas_asignatura_id);
			if (!$data)
				$this->_mensaje(__('error_asignacion_encuesta'),'/encuestas_asignaturas',true);
			if ($data['EncuestasAsignatura']['asignatura_id'] != $this->_profesor_asignatura_actual()){
				$this->_mensaje(__('error_permiso_denegado'),'/encuestas_asignaturas',true);
			}
			$this->_set_activos($data);
			$this->set('id',$encuestas_asignatura_id);
			$this->request->data=$data;
		}
		else{
			//$data = $this->_clean_data();
			//$encuestas_asignatura_id=$data['EncuestasAsignatura']['id'];
			$data = $this->request->data;
			$this->EncuestasAsignatura->recursive=-1;
			$old_data=$this->EncuestasAsignatura->findById($encuestas_asignatura_id);
			if (!$old_data)
				$this->_mensaje(__('error_accediendo'),'/encuestas_asignaturas/',true);
			if ($old_data['EncuestasAsignatura']['asignatura_id'] !=  $this->_profesor_asignatura_actual()){
				$this->_mensaje(__('error_permiso_denegado'),'/encuestas_asignaturas',true);
			}
			if ($this->_save($data)){
				$this->_mensaje(__('mens_asignacion_modificada'),'/encuestas_asignaturas');
			}
		}
	}

	function _save($data)
	{
		if (!$data['EncuestasAsignatura']['activar_fecha_inicio']){
			$data['EncuestasAsignatura']['fecha_inicio']=null;
		}
		if(! $data['EncuestasAsignatura']['activar_fecha_fin']){
			$data['EncuestasAsignatura']['fecha_fin']=null;
		}
		return $this->EncuestasAsignatura->save($data);
	}

	function _set_activos(& $data){
			$data['EncuestasAsignatura']['activar_fecha_inicio']=($data['EncuestasAsignatura']['fecha_inicio']!==null);
			$data['EncuestasAsignatura']['activar_fecha_fin']=($data['EncuestasAsignatura']['fecha_fin']!==null);
	}

	function resultados($encuestas_asignatura_id){
		$this->EncuestasAsignatura->recursive=1;
		$this->EncuestasAsignatura->contain('Encuesta');
		$data=$this->EncuestasAsignatura->findById($encuestas_asignatura_id);
		if (!$data) return null;
		if ($data['EncuestasAsignatura']['asignatura_id']!=$this->_profesor_asignatura_actual()){
			$this->_mensaje(__('error_no_acceso'),'/',true);
		}
		$resultados=$this->EncuestasAsignatura->resultados_simples_votacion($encuestas_asignatura_id);
		$this->set('encuesta_id',$encuestas_asignatura_id);
		$this->set('encuesta',$data['Encuesta']);
		$this->set('resultados',$resultados);
	}

	function resultados_grupos($encuestas_asignatura_id){

		$this->EncuestasAsignatura->recursive=1;
		$this->EncuestasAsignatura->contain('Encuesta');
		$data=$this->EncuestasAsignatura->findById($encuestas_asignatura_id);
		if (!$data) return null;

		if ($data['EncuestasAsignatura']['asignatura_id']!=$this->_profesor_asignatura_actual()){
			$this->_mensaje(__('error_no_acceso'),'/',true);
		}
		$resultados=$this->EncuestasAsignatura->resultados_grupos_votacion($encuestas_asignatura_id);
		$grupos=$this->Grupo->grupos_asignatura($data['EncuestasAsignatura']['asignatura_id']);
		$est_grupos = $this->Grupo->estadisticas_grupos($data['EncuestasAsignatura']['asignatura_id']);
		$total_alumnos = $this->Asignatura->alumnos($data['EncuestasAsignatura']['asignatura_id']);
		$total_alumnos = count($total_alumnos);
		$total_por_grupo = array();
		$ids = $this->Asignatura->ids_grupos($data['EncuestasAsignatura']['asignatura_id']);
		foreach($ids as $grupo){
			//$total_por_grupo[$grupo['Grupo']['nombre']] = ++$i;
			$total_por_grupo[$grupo['Grupo']['nombre']] = round(100 * count($this->Grupo->alumnos($grupo['Grupo']['id'])) / $total_alumnos, 2);
		}
		$this->set('encuesta_id', $encuestas_asignatura_id);
		$this->set('total_por_grupo', $total_por_grupo);
		$this->set('grupos',$grupos);
		$this->set('encuesta',$data['Encuesta']);
		$this->set('resultados',$resultados);
	}


	function resultados_puntuaciones($encuestas_asignatura_id){
		$this->EncuestasAsignatura->recursive=1;
		$this->EncuestasAsignatura->contain('Encuesta');
		$data=$this->EncuestasAsignatura->findById($encuestas_asignatura_id);
		if (!$data) return null;
		if ($data['EncuestasAsignatura']['asignatura_id']!=$this->_profesor_asignatura_actual()){
			$this->_mensaje(__('error_no_acceso'),'/',true);
		}
		$resultados=$this->EncuestasAsignatura->resultados_puntuaciones_votacion($encuestas_asignatura_id);
		$this->set('encuesta_id', $encuestas_asignatura_id);
		$this->set('encuesta',$data['Encuesta']);
		$this->set('resultados',$resultados);
	}
}
?>
