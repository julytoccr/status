<?php
	echo $this->Html->link(__('menu_crear_profesor'), array('action'=>'add'));
	echo $this->Tooltip->show(__('menu_crear_profesor'),__('explic_crear_profesor'));
	echo $this->Html->link(__('orden_apellidos'), '/profesores/index/sort:apellidos/direction:asc');
	echo $this->Html->link(__('orden_login'), '/profesores/index/sort:login/direction:asc');
?>
