<?php
	echo $this->Html->link(__('menu_encuestas'),'/encuestas');
	echo $this->Tooltip->show(__('menu_encuestas'),__('explic_encuestas'));

	echo $this->Html->link(__('menu_cambiar_orden'),'/preguntas_encuestas/sort/' . $encuesta_id);
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_preguntas_encuesta'));


	echo $this->Html->link(__('menu_crear_pregunta'),'/preguntas_encuestas/add/' . $encuesta_id);
	echo $this->Tooltip->show(__('menu_crear_pregunta'),__('explic_crear_pregunta_encuesta'));

?>
