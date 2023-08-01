<?php
/**
 * Admin Profesores Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class TextosController extends AppController
{
    var $name = 'Textos';
    var $uses=array('Lenguaje','Etiqueta','Texto','ConfiguracionWeb','Usuario','Tarea', 'CorreccionTextos');
    var $components = array('CustomPaginate');
    var $helpers = array('CustomPagination');
    
    var $access = array
	(
	'*' => array('admin'),
	);
	
	function index(){
	}
	
	function generar_excel_nuevos_terminos(){
		$this->autoRender=false;
		header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
		header("content-disposition: attachment; filename=alta_terminos.xls");
		echo '<html><head>
		   <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
		   </head><body>';
		echo '<table><tr><td>Etiqueta</td>';
		$this->Lenguaje->recursive=-1;			
		$lenguajes=$this->Lenguaje->find('all');

			foreach ($lenguajes as $leng)
			{
			
					$leng_estandard=$leng['Lenguaje']['lenguaje_estandard'];		
					echo '<td>'.$leng_estandard.'</td>';				
			}
			echo '</tr><tr><td></td>';
			
			foreach ($lenguajes as $leng)
			{
											
					$leng_propio=$leng['Lenguaje']['lenguaje_propio'];
					echo '<td>'.$leng_propio.'</td>';
									
			}
			echo '</tr><tr><td></td>';
			
			foreach ($lenguajes as $leng)
			{
					$leng_hereda=$this->Lenguaje->lenguaje_hereda_texto($leng['Lenguaje']['id']);
					echo '<td>'.$leng_hereda.'</td>';				
									
			}
		    echo '</tr></table></body></html>';   
					
	}
	
	function alta_excel_nuevos_terminos($cont_ses_terminos=null,  $lenguaje=null){
		if(($cont_ses_terminos===null) && ($lenguaje===null)){		
			if (!empty($this->request->data)){
				App::import('Vendor','excel/AkExcelToArray');
				$info=$this->data['Termino']['excel'];
				if (! strpos($info['type'],'excel')){
					$this->_mensaje(__('error_subir_excel'),null,true);
				}
				else{
					$file=$info['tmp_name'];
					$excel_data_completo=AkExcelToArray::convert_file($file,0);
					$lenguajes=array();
					$i=1;
					while(isset($excel_data_completo[0][$i])){
										
						$lenguajes[]=array('lenguaje_estandard'=>$excel_data_completo[0][$i],
'lenguaje_propio'=>$excel_data_completo[1][$i],
'lenguaje_hereda'=>$excel_data_completo[2][$i],
);
						$indices[]=$i;
						$i++;
					}			
					$etiquetas=array();
					$cadenas=array();					
					$tope=sizeof($excel_data_completo);
					$j=3;
					while($j<$tope) 					{
						if($this->Etiqueta->existe($excel_data_completo[$j][0])){
							$this->_mensaje(__('error_etiqueta_existe_sistema',array($excel_data_completo[$j][0])),null,true);
						}
						if(in_array($excel_data_completo[$j][0],$cadenas)){
							$this->_mensaje(__('error_etiqueta_existe_excel',array($excel_data_completo[$j][0])),null,true);
						}
						$text=array();
						$i=1;
						while(isset($excel_data_completo[0][$i])){
							if(in_array($i, $indices)){
								$text[]=array(
									'lenguaje_estandard'=>$excel_data_completo[0][$i],
									'texto'=>$excel_data_completo[$j][$i]);
							}
						$i++;
						}					
						$etiquetas[]=array('cadena'=>$excel_data_completo[$j][0],
								'textos'=>$text
								);
						$cadenas[]=$excel_data_completo[$j][0];
					$j++;						
					}
					$cont_ses_terminos=$this->session->check('cont_ses_terminos');
					if($cont_ses_terminos){
						$cont_ses_terminos=$this->session->read('cont_ses_terminos');
						$cont_ses_terminos=$cont_ses_terminos+1;
					}else{
						$cont_ses_terminos=1;
					}
					$this->session->write('cont_ses_terminos', $cont_ses_terminos);
					$this->session->write('terminos'.$cont_ses_terminos, $etiquetas);			
					$muestra_etiquetas=$this->myPagination->paginate_data($etiquetas,50);
					$this->set('page_url','/admin_textos/alta_excel_nuevos_terminos/'.$cont_ses_terminos.'/0');
					$this->set('etiquetas',$muestra_etiquetas);
					$this->set('lenguaje_actual',0);
					$this->set('lenguajes',$lenguajes);
					$this->session->write('lenguajes'.$cont_ses_terminos, $lenguajes);
					$this->set('cont_ses_terminos',$cont_ses_terminos);					
					$this->render('alta_excel_terminos/post');
				}
			}
			else{
				$this->render('alta_excel_terminos/form');
			}
		}else{
			$this->set('lenguaje_actual',$lenguaje);
			$etiquetas=$this->Session->read('terminos'.$cont_ses_terminos);
			//$etiquetas=$this->cacheObject->read('lenguages/terminos'.$cont_ses_terminos);
			$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
			$this->set('page_url','/admin_textos/alta_excel_nuevos_terminos/'.$cont_ses_terminos.'/'.$lenguaje);
			$this->set('etiquetas',$muestra_etiquetas);
			$lenguajes=$this->Session->read('lenguajes'.$cont_ses_terminos);
			//$lenguajes=$this->cacheObject->read('lenguages/lenguajes_term'.$cont_ses_terminos);
			$this->set('lenguajes',$lenguajes);
			$this->set('cont_ses_terminos',$cont_ses_terminos);
			$this->render('alta_excel_terminos/post');
		}
	}


	function guardar_excel_terminos($cont_ses_terminos){
		$etiquetas=$this->Session->read('terminos'.$cont_ses_terminos);
        //$etiquetas=$this->cacheObject->read('lenguages/terminos'.$cont_ses_terminos);
		foreach ($etiquetas as $e){
			$this->Etiqueta->create();
			$data=array('cadena'=>$e['cadena']);
			$this->Etiqueta->save($data);
			$etiqueta_id=$this->Etiqueta->etiqueta_id($e['cadena']);
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
		$lenguajes=$this->Lenguaje->find('all');
		foreach($lenguajes as $leng){
			$this->requestAction('lenguajes/crear_cache/'.$leng['Lenguaje']['lenguaje_estandard']);
		}
		//$this->session->delete('terminos'.$cont_ses_terminos);
		$this->redirect('/');
	}


	function buscar_etiquetas($busqueda=null){
		if ( empty($this->request->data)){
			$this->Lenguaje->recursive=-1;			
			$data=$this->Lenguaje->find('all',array('order'=>'id'));
			foreach ($data as $leng) $lenguajes[$leng['Lenguaje']['id']]=$leng['Lenguaje']['lenguaje_propio'].' ('.$leng['Lenguaje']['lenguaje_estandard'].')';
			$this->set('lenguajes',$lenguajes);					
			$tipos=array(1=>__('buscar_etiquetas'), 2=>__('buscar_textos'), 3=>__('ver_todas'));
			$this->set('tipos', $tipos);
			$this->render('buscar_etiquetas');				
		}	
		else{
			//$data=$this->_clean_data();
			$data = $this->request->data;
			$lenguaje=$data['Etiqueta']['lenguaje_busqueda'];
			App::uses('Sanitize', 'Utility');
			$busqueda=Sanitize::paranoid($data['Etiqueta']['id'],array('-',' ','.','_','(',')'));
			$busqueda=str_replace('*','%',$data['Etiqueta']['id']);
			if($data['Etiqueta']['tipo']==1){
				if($data['Etiqueta']['id']==''){
					$this->_mensaje(__('error_no_cadena_busqueda'),'/textos/buscar_etiquetas',true);	
				}
				$etiquetas=array();				
				if($data['Etiqueta']['keysensitive']==0){
					$cadenas= $this->Etiqueta->find('all',array('conditions'=>array('Etiqueta.cadena LIKE'=>$busqueda),'order'=>array('Etiqueta.id')));
				}else{
					$cadenas=$this->Etiqueta->find('all',array('conditions'=>array('binary Etiqueta.cadena LIKE' => $busqueda)));
				}
				if(!$cadenas) $this->_mensaje(__('mens_no_resultados'),'/textos/buscar_etiquetas');	
				foreach ($cadenas as $c){
					$cadena_id=$c['Etiqueta']['id'];
					$this->Texto->recursive=-1;
					$textos_bus=$this->Texto->find('all',array('conditions'=>array('Texto.etiqueta_id'=>$cadena_id),'order'=>array('lenguaje_id')));
					$text=array();
					foreach ($textos_bus as $tb){
						$lenguaje_estandard=$this->Lenguaje->lenguaje_estandard($tb['Texto']['lenguaje_id']);
						$text[]=array(
							'lenguaje_estandard'=>$lenguaje_estandard,
							'texto'=>$tb['Texto']['texto']
						);
					}									
					$etiquetas[$cadena_id]=array('cadena'=>$c['Etiqueta']['cadena'],
							'id'=>$c['Etiqueta']['id'],
							'textos'=>$text
					);	
				}
			}elseif($data['Etiqueta']['tipo']==2){
				if($data['Etiqueta']['id']==''){
					$this->_mensaje(__('error_no_cadena_busqueda'),'/textos/buscar_etiquetas',true);	
				}
				$etiquetas=array();
				if($data['Etiqueta']['keysensitive']==0){				
					$textos=$this->Texto->find('all',array('conditions'=>array('Texto.texto LIKE'=> $busqueda, 'Texto.lenguaje_id'=> $lenguaje),'order'=>array('Texto.id')));
				}else{
					$textos=$this->Texto->find('all',array('conditions'=>array('binary Texto.texto LIKE '=>$busqueda, 'Texto.lenguaje_id'=>$lenguaje)));
				}				
				if(!$textos) $this->_mensaje(__('mens_no_resultados'),'/textos/buscar_etiquetas');	
				foreach ($textos as $t){
					$this->Etiqueta->recursive=-1;
					$cadena=$this->Etiqueta->findById($t['Texto']['etiqueta_id']);
					$cadena_id=$cadena['Etiqueta']['id'];					
					$this->Texto->recursive=-1;
					$textos_bus=$this->Texto->find('all',array('conditions'=>array('Texto.etiqueta_id' => $cadena_id),'order'=>array('Texto.lenguaje_id')));
					$text=array();
					foreach ($textos_bus as $tb){
						$lenguaje_estandard=$this->Lenguaje->lenguaje_estandard($tb['Texto']['lenguaje_id']);
						$text[]=array(
							'lenguaje_estandard'=>$lenguaje_estandard,
							'texto'=>$tb['Texto']['texto']
							);
					}	
					$etiquetas[$cadena_id]=array('cadena'=>$cadena['Etiqueta']['cadena'],
							'id'=>$cadena['Etiqueta']['id'],
							'textos'=>$text
					);
				}	
			}elseif($data['Etiqueta']['tipo']==3){
				$etiquetas=array();
				$busqueda=$data['Etiqueta']['id'];			
				$cadenas= $this->Etiqueta->find('all',array('order'=>'id'));
				if(!$cadenas) $this->_mensaje(__('mens_no_resultados'),'/textos/buscar_etiquetas');	
				foreach ($cadenas as $c){
					$cadena_id=$c['Etiqueta']['id'];					
					$this->Texto->recursive=-1;
					$textos_bus=$this->Texto->find('all',array('conditions'=>array('etiqueta_id'=>$cadena_id),'order'=>array('lenguaje_id')));
					$text=array();
					foreach ($textos_bus as $tb){
						$lenguaje_estandard=$this->Lenguaje->lenguaje_estandard($tb['Texto']['lenguaje_id']);
						$text[]=array(
							'lenguaje_estandard'=>$lenguaje_estandard,
							'texto'=>$tb['Texto']['texto']
							);
					}	
					$etiquetas[$c['Etiqueta']['id']]=array('cadena'=>$c['Etiqueta']['cadena'],
							'id'=>$c['Etiqueta']['id'],
							'textos'=>$text
					);
				}
				$busqueda=' ';
			}
			$busqueda=str_replace('%','*',$busqueda);
			$this->set('busqueda',$busqueda);
			$this->set('tipo_busqueda',$data['Etiqueta']['tipo']);		
			$this->Lenguaje->recursive=-1;			
			$dato=$this->Lenguaje->find('all',array('order'=>'id'));
			$lenguajes=array();	
			$i=0;		
			foreach ($dato as $d){
				$lenguajes[]=array('lenguaje_estandard'=>$d['Lenguaje']['lenguaje_estandard'], 
'lenguaje_propio'=>$d['Lenguaje']['lenguaje_propio'],'lenguaje_hereda'=>$d['Lenguaje']['lenguaje_hereda'],'lenguaje_hereda_texto'=>$this->Lenguaje->lenguaje_estandard($d['Lenguaje']['lenguaje_hereda']));
				if($d['Lenguaje']['id']==$lenguaje){
					$lenguaje_actual=$i;
				}
				$i++;
			}
			$cont_ses_buscador=$this->Session->check('cont_ses_buscador');
			if($cont_ses_buscador){
				$cont_ses_buscador=$this->Session->read('cont_ses_buscador');
				$cont_ses_buscador=$cont_ses_buscador+1;
			}else{
				$cont_ses_buscador=1;
			}
			$this->Session->write('cont_ses_buscador', $cont_ses_buscador);
			$this->Session->write('etiquetas_buscador'.$cont_ses_buscador, $etiquetas);
			$this->Session->write('lenguajes_buscador'.$cont_ses_buscador, $lenguajes);
			$this->set('cont_ses_buscador',$cont_ses_buscador);			
			$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
			$this->set('page_url','/textos/buscador_post/'.$cont_ses_buscador.'/'.$busqueda.'/'.$data['Etiqueta']['tipo'].'/'.$lenguaje_actual);
			$this->set('etiquetas',$muestra_etiquetas);
			$this->set('lenguaje_actual',$lenguaje_actual);
			$this->set('lenguajes',$lenguajes);		
			$this->render('buscador_post');
		}
			
	}
	
	function buscador_post($cont_ses_buscador, $busqueda=null, $tipo_busqueda=null, $lenguaje=null){
		$this->set('cont_ses_buscador',$cont_ses_buscador);
		$this->set('busqueda',$busqueda);
		$this->set('tipo_busqueda',$tipo_busqueda);
		$this->set('lenguaje_actual',$lenguaje);
		$etiquetas=$this->Session->read('etiquetas_buscador'.$cont_ses_buscador);
		$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
		$this->set('page_url','/textos/buscador_post/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$lenguaje);
		$this->set('etiquetas',$muestra_etiquetas);
		$lenguajes=$this->Session->read('lenguajes_buscador'.$cont_ses_buscador);    
		$this->set('lenguajes',$lenguajes);
	}


	function buscador_todos_lenguajes($cont_ses_buscador, $busqueda=null, $tipo_busqueda=null, $eti=null, $lenguaje=null, $pagina=null){
		$etiqueta_name=$this->Etiqueta->etiqueta_cadena($eti);		
		$this->Lenguaje->recursive=-1;
		$lenguajes=$this->Lenguaje->find('all',array('order'=>array('id')));
		$textos=array();	
		$i=0;	
		foreach ($lenguajes as $leng){
			$this->Texto->recursive=-1;
			$texto=$this->Texto->find('first',array('conditions'=>array('Texto.lenguaje_id' => $leng['Lenguaje']['id'], 'Texto.etiqueta_id' =>  $eti)));
			if($texto['Texto']['texto']!=' '){
				$leng_hereda=' ';
				$leng_hereda_texto=' ';
			}else{
				$leng_hereda=$leng['Lenguaje']['lenguaje_hereda'];
				$leng_hereda_texto=$this->Lenguaje->lenguaje_hereda_texto($leng['Lenguaje']['id']);			
			}
			$textos[]=array('lenguaje_id'=>$leng['Lenguaje']['id'],
				'lenguaje_id_session'=>$i,
				'lenguaje'=>$leng['Lenguaje']['lenguaje_estandard'],
				'lenguaje_hereda'=>$leng_hereda,
				'lenguaje_hereda_texto'=>$leng_hereda_texto,
				'texto'=>$texto['Texto']['texto']);
			
			$i++;
		}	
		$this->set('cont_ses_buscador',$cont_ses_buscador);
		$this->set('busqueda',$busqueda);
		$this->set('tipo_busqueda',$tipo_busqueda);
		$this->set('etiqueta_id',$eti);
		$this->set('lenguaje_actual',$lenguaje);
		$this->set('pagina',$pagina);
		$this->set('etiqueta',$etiqueta_name);
		$this->set('texto',$textos);
	}


	function eliminar_etiqueta($cont_ses_buscador, $busqueda=null, $tipo_busqueda=null, $etiqueta_id=null, $lenguaje=null, $pagina=null){
	  	$this->Etiqueta->delete($etiqueta_id);
		$etiquetas=$this->Session->read('etiquetas_buscador'.$cont_ses_buscador);		
		unset($etiquetas[$etiqueta_id]);
		$this->Session->write('etiquetas_buscador'.$cont_ses_buscador, $etiquetas);
		$textos_cache=array();
		$etiquetas_strip=$etiquetas;
		foreach($etiquetas_strip as $key=>$info){
			foreach($info['textos'] as $i=>$info2){
				$textos_cache[$i][$info['id']]=$info2;
			}
			unset($etiquetas_strip[$key]['textos']);
		}
		$this->cacheObject->write('lenguages/etiquetas_buscador'.$cont_ses_buscador, $etiquetas_strip, '+1 hour');
	    $i=0;
	    foreach ($textos_cache as $id=>$value){
	    	$this->cacheObject->write('lenguages/texto_buscador_'.$i.'_'.$cont_ses_buscador,$textos_cache[$i], '+1 hour');
	    	$i++;
	    }	
		$contador=$this->Etiqueta->findCount();
		if($contador==0){
			$lenguajes=$this->Lenguaje->findAll();
			foreach ($lenguajes as $leng){
				$this->Lenguaje->delete($leng['Lenguaje']['id']);
				$lenguaje=$leng['Lenguaje']['lenguaje_estandard'];
				$this->requestAction('/lenguajes/eliminar_cache/'.$lenguaje);
			}
			$this->_mensaje(__('mens_etiquetas_lenguajes_eliminados'),'/',true);
		}else{
			$lenguajes=$this->Lenguaje->findAll();
			foreach ($lenguajes as $leng){
				$lenguaje=$leng['Lenguaje']['lenguaje_estandard'];
				if(!$this->Tarea->existe_pendiente('/lenguajes/crear_cache/'.$lenguaje, 2)){
					$this->Tarea->crear('/lenguajes/crear_cache/'.$lenguaje, 2);
				}
			}
			$this->_mensaje(__('mens_etiqueta_eliminada'),'/textos/buscador_post/'.$cont_ses_buscador.'/'.$busqueda.'/'.$tipo_busqueda.'/'.$lenguaje.'/?page='.$pagina,false);
		}
	}

	function modificar_texto($cont_ses_buscador=null, $busqueda=null, $tipo_busqueda=null, $etiqueta_id=null, $lenguaje_id=null, $lenguaje_id_session=null, $lenguaje=null, $pagina=null){
		if (empty($this->request->data)){
			$data=$this->Texto->find('first',array('conditions'=>array('Texto.lenguaje_id' => $lenguaje_id, 'Texto.etiqueta_id' =>  $etiqueta_id)));
			if (!$data) $this->_mensaje(__('error_texto'),'/textos/',true);
			$this->set('cont_ses_buscador',$cont_ses_buscador);			
			$this->set('busqueda',$busqueda);
			$this->set('tipo_busqueda',$tipo_busqueda);
			$this->set('id',$data['Texto']['id']);
			$this->set('etiqueta_id',$etiqueta_id);	
			$this->set('lenguaje_id',$lenguaje_id);	
			$this->set('lenguaje_id_session',$lenguaje_id_session);		
			$this->set('lenguaje_actual',$lenguaje);
			$this->set('pagina',$pagina);
			$this->request->data=$data;
		}
		else{	
			//$data = $this->_clean_data();
			$data = $this->request->data;
			if(strlen($data['Texto']['texto'])!=0){
				$texto=$data['Texto']['texto'];
			}else{
				$texto=' ';
			}
			$dat=array('id'=>$data['Texto']['id'], 'texto'=>$texto);		
			$this->Texto->save($dat);
			$etiquetas=$this->Session->read('etiquetas_buscador'.$data['Texto']['cont_ses_buscador']);
				
			if(strlen($data['Texto']['texto'])!=0){	
				$etiquetas[$data['Texto']['etiqueta_id']]['textos'][$data['Texto']['lenguaje_id_session']]['texto']=$data['Texto']['texto'];
			}else{
				$etiquetas[$data['Texto']['etiqueta_id']]['textos'][$data['Texto']['lenguaje_id_session']]['texto']=' ';
			}
			if(strlen($data['Texto']['texto'])!=0){
				$etiquetas[$data['Texto']['etiqueta_id']]['textos'][$data['Texto']['lenguaje_id_session']]['lenguaje_hereda_texto']=' ';
			}else{
				$lenguaje_hereda=$this->Lenguaje->lenguaje_hereda_texto($data['Texto']['lenguaje_id']);
				$etiquetas[$data['Texto']['etiqueta_id']]['textos'][$data['Texto']['lenguaje']]['lenguaje_hereda_texto']=$lenguaje_hereda;
			}
			$lenguaje_estandard=$this->Lenguaje->lenguaje_estandard($data['Texto']['lenguaje_id']);			
			$this->Session->write('etiquetas_buscador'.$data['Texto']['cont_ses_buscador'],$etiquetas);

			/*if(!$this->Tarea->existe_pendiente('/admin_lenguajes/crear_cache/'.$lenguaje_estandard, 2)){
				$this->Tarea->crear('/admin_lenguajes/crear_cache/'.$lenguaje_estandard, 2);
			}*/	
			$this->requestAction('lenguajes/crear_cache/'.$lenguaje_estandard);
			$this->_mensaje(__('mens_texto_modificado'),'/textos/buscador_todos_lenguajes/'.$data['Texto']['cont_ses_buscador'].'/'.$data['Texto']['busqueda'].'/'.$data['Texto']['tipo_busqueda'].'/'.$data['Texto']['etiqueta_id'].'/'.$data['Texto']['lenguaje'].'/'.$data['Texto']['pagina']);
		}
	}


	function alta_nuevo_termino(){
		$this->Lenguaje->recursive=-1;		
		$lenguajes=$this->Lenguaje->find('all',array('order'=>'id'));
		if (empty($this->request->data)){
			$this->set('lenguajes',$lenguajes);				
		}	
		else{
			$cadena=$this->request->data['Texto']['etiqueta'];
			$data=$this->Etiqueta->findByCadena($cadena);
			if($data)$this->_mensaje(__('error_etiqueta_existe_sistema',array($cadena)),'/textos/alta_nuevo_termino',true);
			$this->Etiqueta->create();
			$data=array('cadena'=>$cadena);
			$this->Etiqueta->save($data);
			$this->Etiqueta->recursive=-1;
			$this->Etiqueta->cacheQueries=false;
			$etiqueta_id=$this->Etiqueta->etiqueta_id($cadena);
			foreach($lenguajes as $leng){
				$lenguaje_id=$leng['Lenguaje']['id'];				
				$texto=$this->request->data['Texto'][$leng['Lenguaje']['lenguaje_estandard']];
				if($texto==''){
					$texto=' ';
				}
				$this->Texto->create();
				$data=array('etiqueta_id'=>$etiqueta_id, 'lenguaje_id'=>$lenguaje_id, 'texto'=>$texto);
				$this->Texto->save($data);
			}
			foreach($lenguajes as $leng){
				$this->requestAction('lenguajes/crear_cache/'.$leng['Lenguaje']['lenguaje_estandard']);
				//if(!$this->Tarea->existe_pendiente('/admin_lenguajes/crear_cache/'.$leng['Lenguaje']['lenguaje_estandard'], 2)){		
					//$this->Tarea->crear('/admin_lenguajes/crear_cache/'.$leng['Lenguaje']['lenguaje_estandard'], 2);
				//}
			}
			$this->_mensaje(__('mens_termino_insertado'),'/');	
		}

	}
	
	function ver_notificaciones_correcciones($tipo_busqueda=NULL){
		if ($tipo_busqueda==NULL){
			if (empty($this->request->data)){
				$tipos=array(1=>__('ver_notificaciones_no_revisadas'), 2=>__('ver_todas_notificaciones'));
				$this->set('tipos', $tipos);
				$this->render('ver_notificaciones_form');						
			}	
			else{
				//$data=$this->_clean_data();
				$data = $this->request->data;
				if($data['Texto']['tipo_busqueda']==1){
					$this->paginate = array(
						'conditions' => array("CorreccionTextos.estado"=> "No revisada"),
						'order' => array('CorreccionTextos.id' => 'desc')
					);
					$correcciones = $this->paginate('CorreccionTextos');
					$this->set('correcciones',$correcciones);
					$this->set('tipo_busqueda',$data['Texto']['tipo_busqueda']);
					$this->render('ver_notificaciones_correcciones');
				}
				if($data['Texto']['tipo_busqueda']==2){
					$this->paginate = array(
						'order' => array('CorreccionTextos.id' => 'desc')
					);
					$correcciones = $this->paginate('CorreccionTextos');
					$this->set('correcciones',$correcciones);
					$this->set('tipo_busqueda',$data['Texto']['tipo_busqueda']);
					$this->render('ver_notificaciones_correcciones');
				}
			}	
		}else{
			if($tipo_busqueda==1){
				$this->paginate = array(
					'conditions' => array("CorreccionTextos.estado"=> "No revisada"),
					'order' => array('CorreccionTextos.id' => 'desc')
				);
				$correcciones = $this->paginate('CorreccionTextos');
				$this->set('correcciones',$correcciones);
				$this->set('tipo_busqueda',$tipo_busqueda);
				$this->render('ver_notificaciones_correcciones');
			}
			if($tipo_busqueda==2){
				$this->paginate = array(
					'order' => array('CorreccionTextos.id' => 'desc')
				);
				$correcciones = $this->paginate('CorreccionTextos');
				$this->set('correcciones',$correcciones);
				$this->set('tipo_busqueda',$tipo_busqueda);
				$this->render('ver_notificaciones_correcciones');
			}
		}
	}
	
	function administrar_notificacion($id_correccion)
	{
		$correccion=$this->CorreccionTextos->findById($id_correccion);
		$this->set('correccion',$correccion);
		$this->render('administrar_notificacion');
			
	}
	
	function marcar_revision_notificacion($id_correccion){
		$datos=array('id'=> $id_correccion, 'estado'=> 'Revisado');
		$this->CorreccionTextos->save($datos);
		$this->_mensaje(__('mens_notificacion_revisada'),'/textos/ver_notificaciones_correcciones');	
	}
	
	function eliminar_notificacion($id_correccion){
		$this->CorreccionTextos->delete($id_correccion);
		$this->_mensaje(__('mens_notificacion_eliminada'),'/textos/ver_notificaciones_correcciones');	
	}
	
}
?>
