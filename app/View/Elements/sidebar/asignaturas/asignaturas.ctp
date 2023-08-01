<?php
	echo $this->Html->link(__('menu_crear_asignatura'),'/asignaturas/add');
	echo $this->Tooltip->show(__('menu_crear_asignatura'),__('explic_crear_asignatura'));
	echo $this->Html->link(__('orden_alfabetico'),'/asignaturas/index/sort:nombre/direction:asc');
	echo $this->Html->link(__('orden_creacion'),'/asignaturas/index/sort:fecha_inicio/direction:asc');
?>
