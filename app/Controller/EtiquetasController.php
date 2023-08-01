<?php
/**
 * Admin Profesores Controller
 *
 * @author RosSoft
 * @version 0.1
 * @license MIT
 * @package controllers
 */

class EtiquetasController extends AppController
{
    var $name = 'AdminEtiquetas';
    var $uses=array('Lenguaje','Etiqueta','Texto','ConfiguracionWeb','Usuario');
	var $access = array
	(
	'*' => array('admin'),
	);
	
	function alta_excel_etiquetas($cont_ses_etiquetas=null){
		if($cont_ses_etiquetas===null){
			if (! empty($this->request->data)){
					App::import('Vendor','excel/AkExcelToArray');
					$info=$this->data['Etiqueta']['excel'];
					if (! strpos($info['type'],'excel')){
						$this->_mensaje(__('error_subir_excel'),null,true);
					}
					else{
						$file=$info['tmp_name'];
						$excel_data=AkExcelToArray::convert_file($file,1);
						$etiquetas=array();
						$cadenas=array();
						foreach ($excel_data as $row){
							if(in_array($row[0],$cadenas)){
								$this->_mensaje('La etiqueta '.$row[0].' esta repetida en el excel',null,true);
							}
							$etiquetas[]=array(	'cadena'=>$row[0],
										'error'=>false);
							$cadenas[]=$row[0];
						}
						$cont_ses_etiquetas=$this->Session->check('cont_ses_etiquetas');
						if($cont_ses_etiquetas){	
							$cont_ses_etiquetas=$this->Session->read('cont_ses_etiquetas');
							$cont_ses_etiquetas=$cont_ses_etiquetas+1;
						}else{
							$cont_ses_etiquetas=1;
						}
						$this->Session->write('cont_ses_etiquetas', $cont_ses_etiquetas);
						$this->Session->write('etiquetas'.$cont_ses_etiquetas, $etiquetas);
												
						$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
						$this->set('page_url','/admin_etiquetas/alta_excel_etiquetas/'.$cont_ses_etiquetas);
						$this->set('cont_ses_etiquetas',$cont_ses_etiquetas);
						$this->set('etiquetas',$muestra_etiquetas);
						$this->render('alta_excel_etiquetas/post');
					}
			}
			else{
				$this->render('alta_excel_etiquetas/form');
			}
		}else{			
			$etiquetas=$this->Session->read('etiquetas'.$cont_ses_etiquetas);
			$muestra_etiquetas=$this->CustomPaginate->paginate_data($etiquetas,50);
			$this->set('page_url','/admin_etiquetas/alta_excel_etiquetas/'.$cont_ses_etiquetas);
			$this->set('cont_ses_etiquetas',$cont_ses_etiquetas);
			$this->set('etiquetas',$muestra_etiquetas);
			$this->render('alta_excel_etiquetas/post');
		}
	}

	function guardar_excel_etiquetas($cont_ses_etiquetas){
	 	$etiquetas=$this->session->read('etiquetas'.$cont_ses_etiquetas);
		foreach ($etiquetas as $e){
			$this->Etiqueta->create();
			$data=array('cadena'=>$e['cadena']);
			$this->Etiqueta->save($data);
		}
		$this->Session->delete('etiquetas'.$cont_ses_etiquetas);
		$this->_mensaje(__('mens_carga_etiquetas'),'/');
	}

	function eliminar_etiquetas(){
		$this->Lenguaje->recursive=-1;
		$lenguajes=$this->Lenguaje->find('all');
		foreach ($lenguajes as $leng){
			$this->Lenguaje->delete($leng['Lenguaje']['id']);
			$this->Usuario->eliminar_lenguaje_seleccionado($leng['Lenguaje']['id']);
			$this->requestAction('lenguajes/eliminar_cache/'.$leng['Lenguaje']['lenguaje_estandard']);
		}
		$this->Etiqueta->recursive=-1;
		$etiquetas=$this->Etiqueta->find('all');
		foreach ($etiquetas as $eti){
			$this->Etiqueta->delete($eti['Etiqueta']['id']);
		}
		$this->ConfiguracionWeb->definir_lenguaje_por_defecto();
		$this->Session->delete('lenguaje');
		$this->_mensaje('La aplicación no tiene etiquetas ni lenguajes','/');
	}
}
?>
