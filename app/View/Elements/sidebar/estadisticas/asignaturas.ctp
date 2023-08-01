<?php
	echo $this->Html->link(__('menu_asignatura'),'/estadisticas/asignatura');
	echo $this->Tooltip->show(__('menu_asignatura'),__('explic_asignatura_estadisticas'));
	echo $this->Html->link(__('menu_problemas'),'/estadisticas/problemas_grafico');
	echo $this->Tooltip->show(__('menu_problemas'),__('explic_problemas_estadisticas'));
	echo $this->Html->link(__('menu_grupos'),'/estadisticas_grupos/index');
	echo $this->Tooltip->show(__('menu_grupos'),__('explic_grupos_estadisticas'));
	echo $this->Html->link(__('menu_alumnos'),'/estadisticas_alumnos/');
	echo $this->Tooltip->show(__('menu_alumnos'),__('explic_alumnos_estadisticas'));
	echo $this->Html->link(__('menu_historial_login'),'/estadisticas/historial_login');
	echo $this->Tooltip->show(__('menu_historial_login'),__('explic_historial_login_estadisticas'));
	echo $this->Html->link(__('listado_valoracion_problemas'),'/estadisticas_problemas/profesor_listado_valoraciones');
?>
