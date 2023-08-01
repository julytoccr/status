<?php
	echo $this->Html->link(__('menu_estadisticas_generales'),
			'/estadisticas_problemas/index/' . $problema_id);
	echo $this->Tooltip->show(__('menu_estadisticas_generales'),__('explic_estadisticas_generales'));
	echo $this->Html->link(__('menu_listado_ejecuciones'),
			'/estadisticas_problemas/listado_ejecuciones/' . $problema_id);
	echo $this->Tooltip->show(__('menu_listado_ejecuciones'),__('explic_listado_ejecuciones_problema'));
	echo $this->Html->link(__('menu_preguntas'),
			'/estadisticas_problemas/preguntas/' . $problema_id);
	echo $this->Tooltip->show(__('menu_preguntas'),__('explic_estadisticas_preguntas'));
	echo $this->Html->link(__('menu_grafico_repeticiones'),
			'/estadisticas_problemas/grafico_repeticiones/' . $problema_id);
	echo $this->Tooltip->show(__('menu_grafico_repeticiones'),__('explic_grafico_repeticiones'));
	echo $this->Html->link(__('menu_grafico_opiniones'),
			'/estadisticas_problemas/grafico_opiniones/' . $problema_id);
	echo $this->Tooltip->show(__('menu_grafico_opiniones'),__('explic_grafico_opiniones'));
?>
