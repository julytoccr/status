<?php
/**
 * MailController
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 */
App::uses('CakeEmail', 'Network/Email');
class MailController extends AppController
{
    var $name = 'Mail';
    var $uses=array('Usuario','Agrupacion','Bloque','Asignatura','Grupo','RestriccionesBloque','Problema','Lenguaje','ConfiguracionWeb');
    var $helpers=array();

	function beforeFilter(){
    if ($this->action != 'enviar_password_usuario' && ! $this->_is_localhost_call(array('192.168.0.100')))
    {
      die('Only from localhost');
    }
    $this->Auth->allow('enviar_password_usuario');
    $this->Auth->allow('enviar_notificacion_agrupacion');
    $this->autoRender = false;
  }
	
	function enviar_password_usuario($usuario_id, $asignatura_id = null)
	{
		$this->Usuario->recursive=-1;
		$usuario=$this->Usuario->findById($usuario_id);
		$usuario=$usuario['Usuario'];

    if ($asignatura_id != null) {
			$this->Asignatura->recursive=-1;
			$asignatura = $this->Asignatura->findById($asignatura_id);
			$asignatura = $asignatura['Asignatura'];
    }
                
		$Email = new CakeEmail('estatus');
		$Email->to($usuario['email']);
		$Email->subject('e-status: Alta de usuario');
		$Email->emailFormat('html');
		$Email->template('enviar_password_usuario', 'default');
		if(isset($asignatura))
			$Email->viewVars(array('asignatura'=>$asignatura));
		$Email->viewVars(array('usuario' => $usuario));
      	$Email->send();
	}

	function enviar_notificacion_agrupacion($agrupacion_id){
		$Email = new CakeEmail('estatus');
		
		$asignatura_id = $this->Agrupacion->asignatura($agrupacion_id);
		$grupos=$this->Asignatura->grupos($asignatura_id);
		$bloque_id=$this->Agrupacion->bloque($agrupacion_id);
		$this->Bloque->recursive=-1;
		$bloque=$this->Bloque->findById($bloque_id);
		$problema_id=$this->Agrupacion->problema($agrupacion_id);
		$restricciones=$this->RestriccionesBloque->restricciones_bloque($bloque_id);

		$this->Problema->recursive=-1;
		$problema=$this->Problema->findById($problema_id);

		$problema = $problema['Problema'];
		$bloque = $bloque['Bloque'];
		
		$Email->template('enviar_notificacion_agrupacion', 'default');
		$Email->viewVars(array('problema'=>$problema,'bloque'=>$bloque));
				
		foreach ($grupos as $nombre_grupo) {
			$intervalos = array();
			foreach($restricciones as &$r) {
				$r = $r['RestriccionesBloque'];
				if ($r['grupo'] == null || $r['grupo'] == $nombre_grupo) {
					$intervalos[] = array($r['fecha_inicio'],$r['fecha_fin']);
				}
			}

			if (empty($restricciones)) { //Si no hay restricciones para nadie...
				$intervalos[] = array(null, null);
			}

			$grupo_id = $this->Grupo->buscar($asignatura_id, $nombre_grupo);
			$alumnos = $this->Grupo->alumnos($grupo_id);
			$id_lenguaje_defecto = $this->ConfiguracionWeb->findByNombre('lenguaje_por_defecto');
			$id_lenguaje_defecto = $id_lenguaje_defecto['ConfiguracionWeb']['valor'];
			foreach ($alumnos as $alumno) {
				$alumno = $alumno['Usuario'];
				$lenguaje_id = $alumno['lenguaje'];
				if ($lenguaje_id == 0) $lenguaje_id = $id_lenguaje_defecto;
				Configure::write('Config.language', $lenguaje_id);
				foreach ($intervalos as $int) {
					$inicio = $int[0];
					$fin = $int[1];
					$restriccion = "";
					if ($inicio != null && $fin != null) {
						$restriccion = "\r\n\r\n\r\n" . __('notificacion_problema_restricciones') . " " . __('restriccion_desde_dia',array($inicio)) . " " . __('restriccion_hasta_dia',array($fin));
					}
					else if ($inicio != null) {
						$restriccion = "\r\n\r\n\r\n" . __('notificacion_problema_restricciones') . " " . __('restriccion_desde_dia',array($inicio));
					}
					else if ($fin != null) {
						$restriccion = "\r\n\r\n\r\n" . __('notificacion_problema_restricciones') . " " . __('restriccion_hasta_dia',array($fin));
					}
				
					$Email->to($alumno['email']);
					$Email->subject(__('mens_email_subject_problema_nuevo'));
					$Email->emailFormat('html');
					$Email->viewVars(array('restriccion'=>$restriccion,'nombre'=>$alumno['nombre']));
					$Email->send();
				}
			}
		}
	}
}
