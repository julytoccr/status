<?php
class Tarea extends AppModel
{
	var $name = 'Tarea';
	var $cacheQueries=false;

	/**
	 * Crea una nueva tarea pendiente (evita repetidos)
	 * @param string $comando Comando tipo CakeURL /controller/action/param1/param2/..
	 * @param integer $prioridad 0=>Nocturna , 1=>5 minutos, 2=>15 minutos
	 * @return boolean Éxito
	 */
	function crear($comando,$prioridad=0){
		$param=array(	'comando'=>$comando,
						'ejecutado'=>0,
						'prioridad'=>$prioridad,
						'salida'=>'');
		if (! $this->find('first',array('conditions'=>$param))){
			$this->create();
			return $this->save($param);
		}
		else{
			return true;
		}

	}

	/**
	 * Ejecuta una tarea y guarda la salida en la BD
	 * @param integer $id Tarea a ejecutar
	 * @param boolean Se ha guardado la salida con éxito
	 */
	function ejecutar($id){
		$this -> recursive = -1;
		$data = $this -> findById($id);
		if ($data['Tarea']['ejecutado'] != 1) {
			$data['Tarea']['ejecutado'] = 2; // marcar com a 'servint'
			$data['Tarea']['modified'] = date("Y-m-d H:i:s");
			$this -> save($data);
			$data['Tarea']['salida'] = $this -> requestAction($data['Tarea']['comando'], array('return'));
			$data['Tarea']['ejecutado'] = 1;
			$data['Tarea']['modified'] = date("Y-m-d H:i:s");
			return $this -> save($data);
		}
		else return true;
	}

	/**
	 * Comprueba si existe una tarea de un cierto comando pendiente
	 * @param string $comando Comando tipo CakeURL /controller/action/param1/param2/..
	 * @param integer $prioridad 0=>Nocturna , 1=>Casi inmediato (segun cron)
	 *
	 * @return boolean Cierto si existe el comando pendiente
	 */
	function existe_pendiente($comando,$prioridad=null)
	{
		$this -> recursive = -1;
		
		// tasques no servides
		$cond = array('comando' => $comando, 'ejecutado' => 0);
		if ($prioridad !== null) {
			$cond['prioridad'] = $prioridad;
		}
		$cont1 = $this -> find('count',array('conditions'=>$cond));

		// tasques començades a servir i no acabades
		$cond['ejecutado'] = 2;
		$data = $this -> find('all',array('conditions'=>$cond));
		$cont2 = 0;
		$limit_timestamp = $this -> _temps_limit();
		foreach ($data as $tarea) {
			if (strtotime($tarea['Tarea']['modified']) < $limit_timestamp) ++$cont2;
		}
		return $cont1 + $cont2;
	}


	/**
	 * Ejecuta todas las tareas pendientes
	 *
	 * @param integer $prioridad Prioridad de las tareas a ejecutar. Null para ignorar la prioridad
	 * @return boolean Cierto si no ha habido ningún error.
	 *
	 */
	function ejecutar_pendientes($prioridad = null) {
		$error = false;
		$this -> recursive = -1;
		$cond = array();

		// tasques no servides
		$cond['ejecutado'] = 0;
		if ($prioridad !== null) {
			$cond['prioridad'] = $prioridad;
		}
		$data = $this -> find('all',array('conditions'=>$cond));
		foreach ($data as $tarea) {
			$id = $tarea['Tarea']['id'];
			if (! $this->ejecutar($id)) $error = true;
		}
		
		// tasques en estat 'servint' que no hagin acabat, per algun motiu
		$cond['ejecutado'] = 2;
		$data = $this -> find('all',array('conditions'=>$cond));
		foreach ($data as $tarea) {
			$tarea = $this -> findById($tarea['Tarea']['id']); // la tornem a demanar, no fos cas que en el temps d'anar processant ja s'hagués servit

			if ($tarea['Tarea']['ejecutado'] == 2 && strtotime($tarea['Tarea']['modified']) < $this -> _temps_limit()) {
				// probablement la tasca no es va completar per algun motiu... han passat + de 12 hores!
				$id = $tarea['Tarea']['id'];
				if (! $this->ejecutar($id)) $error = true;
			}
		}
		return ! $error;
	}

	private function _temps_limit() {
		return mktime  (date("H")-12, date("i"), date("s"), date("m"), date("d"), date("Y")); // hores - 12, minuts, segons, mes, dia, any
	}
}
?>
