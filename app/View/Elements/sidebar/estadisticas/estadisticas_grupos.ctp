<?php
	echo $this->Html->link(__('menu_grupos'),'/estadisticas_grupos/index');
	echo $this->Tooltip->show(__('menu_grupos'),__('explic_estadisticas_grupos'));
	echo $this->Html->link(__('menu_grafico_nota_vs_puntuacion'),'/estadisticas_grupos/grafico_nota_puntuacion');
	echo $this->Tooltip->show(__('menu_grafico_nota_vs_puntuacion'),__('explic_grafico_nota_vs_puntuacion'));
	echo $this->Html->link(__('menu_grafico_nota'),'/estadisticas_grupos/grafico_nota');
	echo $this->Tooltip->show(__('menu_grafico_nota'),__('explic_grafico_nota'));
	echo $this->Html->link(__('menu_grafico_puntuacion'),'/estadisticas_grupos/grafico_puntuacion');
	echo $this->Tooltip->show(__('menu_grafico_puntuacion'),__('explic_grafico_puntuacion'));
	echo $this->Html->link(__('menu_grafico_problemas_alumno'),'/estadisticas_grupos/grafico_problemas_por_alumno');
	echo $this->Tooltip->show(__('menu_grafico_problemas_alumno'),__('explic_grafico_problemas_alumno'));
	echo $this->Html->link(__('volver'),'/estadisticas/asignatura');
	echo $this->Tooltip->show(__('volver'),__('volver_estadisticas'));
?>
