<?php
	echo $this->Html->link(__('menu_editar_encuestas'),'/encuestas');
	echo $this->Tooltip->show(__('menu_editar_encuestas'),__('explic_editar_encuestas'));

	echo $this->Html->link(__('menu_resultados'),'/encuestas_asignaturas/resultados/' . $encuesta_id);
	echo $this->Tooltip->show(__('menu_resultados'),__('explic_resultados_encuesta'));

	echo $this->Html->link(__('menu_grupo'),'/encuestas_asignaturas/resultados_grupos/' . $encuesta_id);
	echo $this->Tooltip->show(__('menu_grupo'),__('explic_grupo_encuesta'));

	echo $this->Html->link(__('menu_puntuaciones'),'/encuestas_asignaturas/resultados_puntuaciones/' . $encuesta_id);
	echo $this->Tooltip->show(__('menu_puntuaciones'),__('explic_puntuaciones_encuesta'));

?>
