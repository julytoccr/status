<?php
class ConfiguracionWeb extends AppModel
{
	var $name = 'ConfiguracionWeb';
	var $useTable = 'configuracion_web';
	
	function lenguaje_por_defecto(){
		$this->cacheQueries=false;
		$this->recursive=-1;
		$data=$this->findByNombre('lenguaje_por_defecto');
		if (!$data) return null;
		return $data['ConfiguracionWeb']['valor'];	
	}

	function definir_lenguaje_por_defecto($lenguaje=null){
		if($lenguaje===null){
			$this->recursive=-1;		
			$lenguaje_defecto=$this->findByNombre('lenguaje_por_defecto');
			$data=array('id'=>$lenguaje_defecto['ConfiguracionWeb']['id'], 'valor'=>0);
			$this->save($data);
		}else{
			$this->recursive=-1;		
			$lenguaje_defecto=$this->findByNombre('lenguaje_por_defecto');
			$this->id = $lenguaje_defecto['ConfiguracionWeb']['id'];
            $this->saveField('valor', $lenguaje); 
		}
	}
}
?>
