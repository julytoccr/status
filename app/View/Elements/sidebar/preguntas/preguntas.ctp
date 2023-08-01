<?php
	echo $this->Html->link(__('menu_editar_problema'),'/problemas/edit/' . $problema_id);
	echo $this->Tooltip->show(__('menu_editar_problema'),__('explic_editar_problema'));

	echo $this->Html->link(__('menu_cambiar_orden'),'/preguntas/sort/' . $problema_id);
	echo $this->Tooltip->show(__('menu_cambiar_orden'),__('explic_cambiar_orden_preguntas'));

	echo $this->Html->link(__('menu_crear_pregunta'),'/preguntas/add/' . $problema_id);
	echo $this->Tooltip->show(__('menu_crear_pregunta'),__('explic_crear_pregunta'));
	
	echo $this->Html->link(__('menu_preguntas'),'/preguntas/index/' . $problema_id);

?>
