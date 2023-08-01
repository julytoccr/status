<?php
	echo $this->Html->link(__('menu_cambiar_orden'),'/programas/sort/');
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_programas'));
	echo $this->Html->link(__('menu_crear_programa'),'/programas/add/');
	echo $this->Tooltip->show(__('menu_crear_programa'),__('explic_crear_programa'));
?>
