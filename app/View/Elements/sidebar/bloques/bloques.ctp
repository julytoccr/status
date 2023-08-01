<?php
	echo $this->Html->link(__('menu_cambiar_orden'),'/bloques/sort');
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_bloques'));
	echo $this->Html->link(__('menu_crear_bloque'),'/bloques/add');
	echo $this->Tooltip->show(__('menu_crear_bloque'),__('explic_crear_bloque'));
	echo $this->Html->link(__('asignaciones'),'/bloques/problemas');
	echo $this->Tooltip->show(__('asignaciones'),__('explic_asignar_problemas_bloque'));
	echo $this->Html->link(__('bloques'),'/bloques');
?>
