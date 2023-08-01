<?php
	echo $this->Html->link(__('menu_alta_excel_alumnos'),'/alumnos/alta_excel');
	echo $this->Tooltip->show(__('menu_alta_excel_alumnos'),__('explic_alta_excel_alumnos'));
	echo $this->Html->link(__('menu_crear_alumno'),'/alumnos/add');
	echo $this->Tooltip->show(__('menu_crear_alumno'),__('explic_crear_alumno'));
	echo $this->Html->link(__('envio_passwords'),'/alumnos/envio_passwords');
?>