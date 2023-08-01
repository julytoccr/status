<?php
/**
 * Admin Asignaturas Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */
App::import('Vendor', 'excel/AkExcelToArray');

class LenguajesController extends AppController
{
    var $name = 'Lenguajes';
    var $uses=array('Lenguaje','Etiqueta','Texto','ConfiguracionWeb','Usuario', 'CorreccionTextos');
    var $components=array('CustomPaginate');
    var $helpers=array('CustomPagination');
	
	var $access = array
	(
	'*' => array('admin'),
	'crear_cache'=> array('*'),
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->set('title_for_layout',__('titulo_administrar_lenguajes'));
        $this->Auth->allow('login');
    }

	function generar_excel_lenguaje(){
		if (empty($this->request->data)){
			$data=$this->Lenguaje->find('all');
			$lenguajes=array(0=>'-----');
			foreach ($data as $leng) $lenguajes[$leng['Lenguaje']['id']]=$leng['Lenguaje']['lenguaje_propio'].' ('.$leng['Lenguaje']['lenguaje_estandard'].')';
			$this->set('lenguajes',$lenguajes);
		}
		else{
			//$data=$this->_clean_data();
			$data = $this->request->data;
			$this->autoRender=false;
			header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
			//header("Content-Type: application/ms-excel; charset=UTF-8");
			//header("Content-Encoding: UTF-8");
			//header("Expires: 0");
			//header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("content-disposition: attachment; filename=alta_lenguajes.xls");
			echo '<html><head>
		   <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
		   </head><body>';
			if(($data['Lenguaje']['id'][0]==0) && (sizeof($data['Lenguaje']['id'])!=1)){
				$this->_mensaje(__('error_eleccion'),'/lenguajes/generar_excel_lenguaje',true);
			}
			echo '<table><tr><td>Etiqueta</td>';
			$j=1;
			$vector_insertados=array();
			foreach ($data['Lenguaje']['id'] as $leng){
					$leng_estandard=$this->Lenguaje->lenguaje_estandard($leng);
					$vector_insertados[$j]=$leng_estandard;
					echo '<td>'.$leng_estandard.'</td>';

					$j++;
			}
			echo '</tr><tr><td></td>';
			foreach ($data['Lenguaje']['id'] as $leng)
			{
					$leng_propio=$this->Lenguaje->lenguaje_propio($leng);
					echo '<td>'.$leng_propio.'</td>';
			}
			echo '</tr><tr><td></td>';
			foreach ($data['Lenguaje']['id'] as $leng){
					$leng_hereda=$this->Lenguaje->lenguaje_hereda_texto($leng);
					echo '<td>'.$leng_hereda.'</td>';
			}
		    echo '</tr>';
			$dato=$this->Etiqueta->find('all',array('order'=>'id'));
			if($data['Lenguaje']['id'][0]==0){
				foreach ($dato as $etiqueta):
					echo '<tr><td>'.$etiqueta['Etiqueta']['cadena'].'</td></tr>';
				endforeach;
			}
			else{
				foreach ($dato as $etiqueta):
					$j=1;
					echo '<tr><td>'.$etiqueta['Etiqueta']['cadena'].'</td>';	
					while($j< sizeof($vector_insertados)+1){
						$lenguaje=$this->Lenguaje->lenguaje_id($vector_insertados[$j]);
						$dat=$this->Texto->find('first',array('conditions'=>array('Texto.etiqueta_id'=> $etiqueta['Etiqueta']['id'], 'Texto.lenguaje_id'=>$lenguaje)));
						$dato=$dat['Texto']['texto'];
						echo '<td>'.$dato.'</td>';
						$j++;
					}
					echo '</tr>';
					
				endforeach;
			}
			echo '</table></body></html>';
		}
	}

	function idiomas(){
	}

	function alta_excel_lenguajes($cont_ses_lenguajes=null, $lenguaje=null){
		if(($cont_ses_lenguajes===null) && ($lenguaje===null)){
			if (!empty($this->request->data)){
				$info=$this->request->data['Lenguaje']['excel'];
				if (! strpos($info['type'],'excel')){
					$this->_mensaje(__('error_subir_excel'),null,true);
				}
				else{
					$file=$info['tmp_name'];
					$excel_data_completo=AkExcelToArray::convert_file($file,0);
					$lenguajes=array();
					$i=1;
					$lenguajes_entrados=array();
					while(isset($excel_data_completo[0][$i])){
						$lenguajes_entrados[]=$excel_data_completo[0][$i];
						$i++;
					}
					$i=1;
					while(isset($excel_data_completo[0][$i])){
						if(!$this->Lenguaje->existe_estandard($excel_data_completo[0][$i])){
							if($excel_data_completo[2][$i]!=''){
								$data=$this->Lenguaje->findBylenguaje_estandard($excel_data_completo[2][$i]);
								if((!$data) && (!in_array($excel_data_completo[2][$i], $lenguajes_entrados))){
									$this->_mensaje(__('error_heredar_lenguajes'),'/',true);
								}
							}
							$lenguajes[]=array('lenguaje_estandard'=>$excel_data_completo[0][$i],
												'lenguaje_propio'=>$excel_data_completo[1][$i],
												'lenguaje_hereda'=>$excel_data_completo[2][$i],
							);
							$indices[]=$i;
						}
						$i++;
					}
					if(sizeof($indices)==0)$this->_mensaje(__('error_lenguajes_existen'),'/',true);
					$etiquetas=array();
					$tope=sizeof($excel_data_completo);
					$j=3;
					while($j<$tope){
						$text=array();
						$i=1;
						while(isset($excel_data_completo[0][$i])){
							if(in_array($i, $indices)){
								if($excel_data_completo[$j][$i]!=''){
									$leng_hereda='';
								}else{
									$leng_hereda=$excel_data_completo[2][$i];
								}
								$text[]=array(
									'lenguaje_estandard'=>$excel_data_completo[0][$i],
									'texto'=>$excel_data_completo[$j][$i]);
							}
							$i++;
						}
						$etiquetas[]=array('cadena'=>$excel_data_completo[$j][0],'textos'=>$text);
						$j++;
					}
					$cont_ses_lenguajes=$this->Session->check('cont_ses_lenguajes');
					if($cont_ses_lenguajes){
						$cont_ses_lenguajes=$this->Session->read('cont_ses_lenguajes');
						$cont_ses_lenguajes=$cont_ses_lenguajes+1;
					}else{
						$cont_ses_lenguajes=1;
					}
					$this->Session->write('cont_ses_lenguajes', $cont_ses_lenguajes);
					$this->Session->write('etiquetas_leng'.$cont_ses_lenguajes, $etiquetas);
					$this->Session->write('lenguajes'.$cont_ses_lenguajes, $lenguajes);
					$this->set('cont_ses_lenguajes',$cont_ses_lenguajes);
					$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
					$this->set('page_url','/lenguajes/alta_excel_lenguajes/'.$cont_ses_lenguajes.'/0');
					$this->set('etiquetas',$muestra_etiquetas);
					$this->set('lenguaje_actual',0);
					$this->set('lenguajes',$lenguajes);
					$this->render('alta_excel_lenguajes/post');
				}
			}
			else{
				$this->render('alta_excel_lenguajes/form');
			}
		}else{
			$this->set('cont_ses_lenguajes',$cont_ses_lenguajes);
			$this->set('lenguaje_actual',$lenguaje);
			$etiquetas=$this->Session->read('etiquetas_leng'.$cont_ses_lenguajes);
			$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
			$this->set('page_url','/lenguajes/alta_excel_lenguajes/'.$cont_ses_lenguajes.'/'.$lenguaje);
			$this->set('etiquetas',$muestra_etiquetas);
			$lenguajes=$this->Session->read('lenguajes'.$cont_ses_lenguajes);
			//$lenguajes=$this->cacheObject->read('lenguages/lenguajes'.$cont_ses_lenguajes);
			$this->set('lenguajes',$lenguajes);
			$this->render('alta_excel_lenguajes/post');
		}

	}


	function guardar_excel_lenguajes($cont_ses_lenguajes){
		$etiquetas=$this->Session->read('etiquetas_leng'.$cont_ses_lenguajes);
        $lenguajes=$this->Session->read('lenguajes'.$cont_ses_lenguajes);
       	$cont_lenguajes_antes=$this->Lenguaje->find('count');
		foreach ($lenguajes as $leng){
			$this->Lenguaje->create();
			$data=array('lenguaje_propio'=>$leng['lenguaje_propio'],'lenguaje_estandard'=>$leng['lenguaje_estandard'], 'lenguaje_hereda'=>0);
			$this->Lenguaje->save($data);
		}

		foreach ($lenguajes as $l){
			if(isset($l['lenguaje_hereda'])){
				$lenguaje_id=$this->Lenguaje->lenguaje_id($l['lenguaje_estandard']);
				$hereda_id=$this->Lenguaje->lenguaje_id($l['lenguaje_hereda']);
				$data=array('id'=>$lenguaje_id, 'lenguaje_hereda'=>$hereda_id);
				$this->Lenguaje->save($data);
			}
		}
		if(is_array($etiquetas)){
			foreach ($etiquetas as $e){
				$etiqueta_id=$this->Etiqueta->findByCadena($e['cadena']);
				$etiqueta_id=$etiqueta_id['Etiqueta']['id'];
				foreach ($e['textos'] as $t){
					if($t['texto']==''){
						$texto=' ';
					}else{
						$texto=$t['texto'];
					}
					$lenguaje_id=$this->Lenguaje->lenguaje_id($t['lenguaje_estandard']);
					$this->Texto->create();
					$data=array('etiqueta_id'=> $etiqueta_id, 'lenguaje_id'=> $lenguaje_id, 'texto'=>$texto);
					$this->Texto->save($data);
				}
			}
		}
		foreach ($lenguajes as $le){
			$this->requestAction('admin_lenguajes/crear_cache/'.$le['lenguaje_estandard']);
		}
		if($cont_lenguajes_antes==0){
			$lenguaje_defecto=$this->Lenguaje->lenguaje_id($lenguajes[0]['lenguaje_estandard']);
			$this->ConfiguracionWeb->definir_lenguaje_por_defecto($lenguaje_defecto);
			$this->Session->write('lenguaje', $lenguajes[0]['lenguaje_estandard']);
		}

		$this->Session->delete('etiquetas_leng'.$cont_ses_lenguajes);
		$this->Session->delete('lenguajes'.$cont_ses_lenguajes);
		$this->_mensaje(__('mens_carga_lenguajes'),'/');
	}


	function eliminar_lenguajes(){
		if (empty($this->request->data)){
			$this->Lenguaje->recursive=-1;
			$data=$this->Lenguaje->findAll(null,null,'id');
			//$lenguajes=array(0=>'-----');
			foreach ($data as $leng) $lenguajes[$leng['Lenguaje']['id']]=$leng['Lenguaje']['lenguaje_propio'].' ('.$leng['Lenguaje']['lenguaje_estandard'].')';
			$this->set('lenguajes',$lenguajes);
		}
		else{
			$data=$this->_clean_data();
			foreach ($data['Lenguaje']['id'] as $leng){
				$lenguaje_actual=$this->Lenguaje->lenguaje_estandard($leng);
				$lenguaje_actual_id=$leng;
				$this->Lenguaje->delete($leng);
				$this->Usuario->eliminar_lenguaje_seleccionado($leng);
				$lenguaje_sesion=$this->Session->read('lenguaje');
				$this->requestAction('/lenguajes/eliminar_cache/'.$lenguaje_actual);
				$this->_desheredar($lenguaje_actual_id);
				$lenguaje_defecto=$this->ConfiguracionWeb->lenguaje_por_defecto();
				if($lenguaje_actual_id == $lenguaje_defecto){
					$this->Lenguaje->cacheQueries=false;
					$contador=$this->Lenguaje->findCount();
					if($contador>0){
						$lenguaje_nuevo=$this->Lenguaje->find('all');
						$lenguaje_nuevo_id=$lenguaje_nuevo[0]['Lenguaje']['id'];
						$lenguaje_nuevo=$lenguaje_nuevo[0]['Lenguaje']['lenguaje_estandard'];
						$this->ConfiguracionWeb->definir_lenguaje_por_defecto($lenguaje_nuevo_id);
						$lenguaje_sesion=$this->session->read('lenguaje');
						if($lenguaje_sesion = $lenguaje_actual){
							$this->session->write('lenguaje',$lenguaje_nuevo);
							$existe=$this->cacheObject->expired('lenguages/'.$lenguaje_nuevo);
							if($existe){
								$this->requestAction('admin_lenguajes/crear_cache/'.$lenguaje_nuevo);
							}
						}
					}else{
						$this->ConfiguracionWeb->definir_lenguaje_por_defecto();
						$this->session->delete('lenguaje');
					}
				}else{
					$lenguaje_sesion=$this->session->read('lenguaje');
					if($lenguaje_sesion = $lenguaje_actual){
						$lenguaje_defecto_id=$this->ConfiguracionWeb->lenguaje_por_defecto();
						$lenguaje_defecto2=$this->Lenguaje->lenguaje_estandard($lenguaje_defecto_id);
						$this->session->write('lenguaje',$lenguaje_defecto2);
						$existe=$this->cacheObject->expired('lenguages/'.$lenguaje_defecto2);
						if($existe){
							$this->requestAction('admin_lenguajes/crear_cache/'.$lenguaje_defecto2);
						}
					}
				}

			}
			$this->redirect('/');
		}
	}


	function _desheredar($lenguaje_id){

		$lenguaje_hereda_id=$this->Lenguaje->lenguaje_hereda($lenguaje_id);

		$lenguajes_herederos=$this->Lenguaje->find(array('Lenguaje.lenguaje_hereda' => '='.$lenguaje_id));
		if(is_array($lenguajes_herederos)){
			foreach ($lenguajes_herederos as $leng)
			{
				$data=array('id'=>$leng['id'],'lenguaje_hereda'=>$lenguaje_hereda_id);
				$this->Lenguaje->save($data);
				$this->requestAction('admin_lenguajes/crear_cache/'.$leng['lenguaje_estandard']);
				$this->_actualizar_cache_heredada($leng['id']);
	
			}
		}

	}


	function _actualizar_cache_heredada($lenguaje_id){
		$lenguajes_herederos=$this->Lenguaje->find('all',array('conditions'=>array('Lenguaje.lenguaje_hereda' => $lenguaje_id)));
		foreach ($lenguajes_herederos as $leng){
			$this->requestAction('admin_lenguajes/crear_cache/'.$leng['lenguaje_estandard']);
			$this->_actualizar_cache_heredada($leng['id']);
		}
	}

	function cambiar_lenguaje_defecto(){
		if (empty($this->request->data)){
			$this->Lenguaje->recursive=-1;
			$data=$this->Lenguaje->find('all',array('order'=>'id'));
			foreach ($data as $leng) $lenguajes[$leng['Lenguaje']['id']]=$leng['Lenguaje']['lenguaje_propio'].' ('.$leng['Lenguaje']['lenguaje_estandard'].')';
			$this->set('lenguajes',$lenguajes);
		}
		else{
			//$data=$this->_clean_data();
			$data = $this->request->data;
			$lenguaje_id=$data['Lenguaje']['id'];
			$this->ConfiguracionWeb->definir_lenguaje_por_defecto($lenguaje_id);
			$this->_mensaje(__('mens_cambio_lenguaje_defecto'),'/');
		}
	}

	function gestion_herencia(){
		$this->Lenguaje->recursive=-1;
		$lenguajes=$this->Lenguaje->find('all');
		$lenguajes2=array();
		foreach ($lenguajes as $leng){
			$herencia_texto=$this->Lenguaje->lenguaje_propio($leng['Lenguaje']['lenguaje_hereda']).' ('.$this->Lenguaje->lenguaje_estandard($leng['Lenguaje']['lenguaje_hereda']).')';
			$lenguajes2[]=array('id'=>$leng['Lenguaje']['id'],
					    'lenguaje_estandard'=>$leng['Lenguaje']['lenguaje_estandard'],
					    'lenguaje_propio'=>$leng['Lenguaje']['lenguaje_propio'],
					    'lenguaje_hereda'=>$leng['Lenguaje']['lenguaje_hereda'],
					    'lenguaje_hereda_texto'=>$herencia_texto
						);
		}
		$this->set('lenguajes',$lenguajes2);
	}


	function cambiar_lenguaje_herencia($heredero){
		if (empty($this->request->data)){
			$this->Lenguaje->recursive=-1;
			$dato=$this->Lenguaje->find('all',array('order'=>'id'));
			$lenguajes2=array(0=>'-----');
			foreach ($dato as $leng) $lenguajes2[$leng['Lenguaje']['id']]=$leng['Lenguaje']['lenguaje_propio'].' ('.$leng['Lenguaje']['lenguaje_estandard'].')';
			$this->set('heredero',$heredero);
			$this->set('lenguajes2',$lenguajes2);
		}
		else{
			//$data=$this->_clean_data();
			$data = $this->request->data;
			if($data['Lenguaje']['lenguaje_prioritario']==0){
				$this->Lenguaje->recursive=-1;
				$id_heredero=$this->Lenguaje->lenguaje_id($data['Lenguaje']['lenguaje_heredero']);
				$contador_sintexto=$this->Texto->find('count',array('conditions'=>array('Texto.lenguaje_id'=>$id_heredero, 'Texto.texto' => ' ')));
				if($contador_sintexto>0){
					$this->_mensaje(__('no_quitar_herencia'),'/lenguajes/gestion_herencia', true);
				}
				$campos=array('id'=> $id_heredero, 'lenguaje_hereda'=> 0);
				$this->Lenguaje->save($campos);
				$this->requestAction('lenguajes/crear_cache/'.$data['Lenguaje']['lenguaje_heredero']);
				$this->_actualizar_cache_heredada($id_heredero);
		        $lenguaje_heredero=$data['Lenguaje']['lenguaje_heredero'];
				$this->_mensaje(__('mens_no_lenguaje_herencia',array($lenguaje_heredero)),'/lenguajes/gestion_herencia');
			}else{
				$lenguaje_prioritario_id=$data['Lenguaje']['lenguaje_prioritario'];
				$lenguaje_prioritario=$this->Lenguaje->lenguaje_estandard($lenguaje_prioritario_id);
				$lenguaje_heredero=$data['Lenguaje']['lenguaje_heredero'];
				$id_heredero=$this->Lenguaje->lenguaje_id($lenguaje_heredero);
				if($lenguaje_heredero==$lenguaje_prioritario){
					$this->_mensaje(__('error_no_herencia_propia'),'/lenguajes/gestion_herencia',true);
				}
				if($id_heredero==$this->Lenguaje->lenguaje_hereda_recursivo($lenguaje_prioritario_id)){
					$this->_mensaje(__('error_bucle_herencia'),'lenguajes/gestion_herencia',true);
				}
				$campos=array('id'=> $id_heredero, 'lenguaje_hereda'=> $lenguaje_prioritario_id);
				$this->Lenguaje->save($campos);
				$this->requestAction('lenguajes/crear_cache/'.$lenguaje_heredero);
				$this->_actualizar_cache_heredada($id_heredero);
				$this->_mensaje(__('mens_lenguaje_hereda_lenguaje',array($lenguaje_heredero, $lenguaje_prioritario)),'admin_lenguajes/gestion_herencia');
			}
		}
	}

	function crear_cache($lenguaje){
		$this->autoRender=false;
		$id_lenguaje_defecto=$this->Lenguaje->lenguaje_id($lenguaje);
		$resultado= $this->Texto->find('all',array('conditions'=>array('Texto.lenguaje_id' => $id_lenguaje_defecto),'order'=>array('id')));
		foreach ($resultado as $rest){
			if($rest['Texto']['texto']==' '){
				$texto=' ';
				$lenguaje_hereda_id=$this->Lenguaje->lenguaje_hereda($rest['Texto']['lenguaje_id']);
				while (($lenguaje_hereda_id!=0) && ($texto==' ')){
					$texto=$this->Texto->find('first',array('conditions'=>array('Texto.lenguaje_id' => $lenguaje_hereda_id, 'Texto.etiqueta_id' => $rest['Texto']['etiqueta_id'])));
					$texto=$texto['Texto']['texto'];
					$lenguaje_hereda_id=$this->Lenguaje->lenguaje_hereda($lenguaje_hereda_id);
				}
			}else{
				$texto=$rest['Texto']['texto'];
			}
			$cadena=$this->Etiqueta->findById($rest['Texto']['etiqueta_id']);
			$cadena=$cadena['Etiqueta']['cadena'];
			Cache::write($id_lenguaje_defecto.'.'.$cadena,$texto,'redis');
		}
		return true;
	}


	function eliminar_cache($lenguaje){
		if(!$this->params['bare']) return;
		$this->autoRender=false;
		$contenido=$this->cacheObject->read('lenguages/'.$lenguaje);
		$this->cacheObject->write('lenguages/'.$lenguaje,$contenido, '+3 hours');
	}

	function administrar_lenguajes(){
	}
}
?>
