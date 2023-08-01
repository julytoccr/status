<?php
	echo $this->Html->link(__('menu_editar_bloques'),'/bloques/');
	echo $this->Tooltip->show(__('menu_editar_bloques'),__('explic_editar_bloques'));

	echo $this->Html->link(__('menu_crear_restriccion'),'/restricciones_bloques/add/' . $bloque_id);
	echo $this->Tooltip->show(__('menu_crear_restriccion'),__('explic_crear_restriccion_bloque'));
?>
