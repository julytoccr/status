<?php
	echo $this->Html->link(__('menu_cambiar_orden'),'/tipos_respuestas/sort/');
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_tipos_respuestas'));
	echo $this->Html->link(__('menu_crear_tipo'),'/tipos_respuestas/add/');
	echo $this->Tooltip->show(__('menu_crear_tipo'),__('explic_crear_tipo_respuesta'));
?>
