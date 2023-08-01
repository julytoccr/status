<?php
	echo $this->Html->link(__('menu_listado_alumnos'),'/estadisticas_alumnos/index');
	echo $this->Tooltip->show(__('menu_listado_alumnos'),__('explic_listado_alumnos'));
	echo $this->Html->link(__('menu_buscar_alumnos'),'/estadisticas_alumnos/search');
	echo $this->Tooltip->show(__('menu_buscar_alumnos'),__('explic_buscar_alumnos'));
	echo $this->Html->link(__('menu_listado_excel'),'/listados_alumnos/index');
	echo $this->Tooltip->show(__('menu_listado_excel'),__('explic_listado_excel_alumnos'));
	echo $this->Html->link(__('menu_ranking_ejecuciones'),'/estadisticas_alumnos/ranking_ejecuciones/');
	echo $this->Tooltip->show(__('menu_ranking_ejecuciones'),__('explic_ranking_ejecuciones_alumnos'));
	echo $this->Html->link(__('menu_ranking_nota'),'/estadisticas_alumnos/ranking_nota/');
	echo $this->Tooltip->show('Ranking nota',__('explic_ranking_nota_alumnos'));
	echo $this->Html->link(__('menu_ranking_puntuacion'),'/estadisticas_alumnos/ranking_puntuacion/');
	echo $this->Tooltip->show(__('menu_ranking_puntuacion'),__('explic_ranking_puntuacion_alumnos'));
	echo $this->Html->link(__('volver'),'/estadisticas/asignatura');
	echo $this->Tooltip->show(__('volver'),__('volver_estadisticas'));

?>
