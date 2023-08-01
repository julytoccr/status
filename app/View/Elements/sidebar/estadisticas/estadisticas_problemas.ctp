<?php
	echo $this->Html->link(__('menu_grafico'),'/estadisticas/problemas_grafico');
	echo $this->Tooltip->show(__('menu_grafico'),__('explic_grafico_nota_vs_ejecuciones'));
	echo $this->Html->link(__('menu_listado_nota'),'/estadisticas/problemas_listado_nota');
	echo $this->Tooltip->show(__('menu_listado_nota'),__('explic_listado_problemas_nota'));
	echo $this->Html->link(__('menu_listado_ejecuciones'),'/estadisticas/problemas_listado_ejecuciones');
	echo $this->Tooltip->show(__('menu_listado_ejecuciones'),__('explic_listado_ejecuciones'));
	echo $this->Html->link(__('volver'),'/estadisticas/asignatura');
	echo $this->Tooltip->show(__('volver'),__('volver_estadisticas'));
?>
