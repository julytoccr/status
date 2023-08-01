<?php 
echo $this->Form->create(null,array('url'=>'crear_carpeta'));
echo $this->Form->input('Carpeta.nombre');
echo $this->Form->input('Carpeta.compartida',array('type'=>'checkbox'));
echo $this->Form->submit(__('aceptar'));
echo $this->Form->end();
?>
