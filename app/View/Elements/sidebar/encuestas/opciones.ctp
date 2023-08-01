<?php
	echo $this->Html->link(__('menu_encuestas'),'/encuestas/');
	echo $this->Tooltip->show(__('menu_encuestas'),__('explic_encuestas'));

	echo $this->Html->link(__('menu_preguntas'),'/preguntas_encuestas/index/' . $encuesta_id);
	echo $this->Tooltip->show(__('menu_preguntas'),__('explic_preguntas'));

	echo $this->Html->link(__('menu_cambiar_orden'),'/opciones_encuestas/sort/' . $pregunta_id);
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_opciones'));

	echo $this->Html->link(__('menu_crear_opcion'),'/opciones_encuestas/add/' . $pregunta_id);
	echo $this->Tooltip->show(__('menu_crear_opcion'),__('explic_crear_opcion'));

?>
