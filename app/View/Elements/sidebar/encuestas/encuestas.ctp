<?php
	echo $this->Html->link(__('todas_las_encuestas'),'/encuestas');
	echo $this->Html->link(__('menu_crear_encuesta'),'/encuestas/add');
	echo $this->Tooltip->show(__('menu_crear_encuesta'),__('explic_crear_encuesta'));
	echo $this->Html->link(__('encuestas_asociadas'),'/encuestas_asignaturas');
	//echo $this->Tooltip->show(__('menu_asignar_encuesta'),__('explic_asignar_encuesta'));
?>
