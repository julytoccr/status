<?php
	echo $this->Html->link(__('menu_cambiar_orden'),'/sintaxis_respuestas/sort/');
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_sintaxis_respuesta'));
	echo $this->Html->link(__('menu_crear_sintaxis_respuesta'),'/sintaxis_respuestas/add/');
	echo $this->Tooltip->show(__('menu_crear_sintaxis_respuesta'),__('explic_crear_sintaxis_respuesta'));
?>
