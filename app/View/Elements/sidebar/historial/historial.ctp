<?php
	echo $this->Html->link(__('menu_historial'),
			"/historial_alumnos/index/$alumno_id");
	echo $this->Tooltip->show(__('menu_historial'),__('historial_pagina_principal'));

	echo $this->Html->link(__('menu_ejecuciones_realizadas'),
			"/historial_alumnos/listado_ejecuciones/$alumno_id");
	echo $this->Tooltip->show(__('menu_ejecuciones_realizadas'),__('explic_ejecuciones_realizadas'));

	echo $this->Html->link(__('menu_ejecuciones_perdidas'),
			"/historial_alumnos/ejecuciones_perdidas/$alumno_id");
	echo $this->Tooltip->show(__('menu_ejecuciones_perdidas'),__('explic_ejecuciones_perdidas'));

	echo $this->Html->link(__('menu_puntuacion_bloques'),
			"/historial_alumnos/puntuacion_bloques/$alumno_id");
	echo $this->Tooltip->show(__('menu_puntuacion_bloques'),__('explic_puntuacion_bloques'));

	echo $this->Html->link(__('listado_valoracion_problemas'),'/estadisticas_problemas/alumno_listado_valoraciones');

	if(!isset($grafico))
	{
		echo $this->Html->link(__('graficos'),"/historial_alumnos/graficos/$alumno_id");
	}
?>



<?php
	if(isset($grafico)){?>
	<p class="sideBarTitle">
	<span class="sideBarDot">·</span>
	<?php echo __('graficos') ?>
	</p>
	<div>
	<?php
		echo $this->Html->link(__('menu_grafico_puntuaciones'), "/historial_alumnos/grafico_ejecuciones_vs_puntuacion/$alumno_id");
		echo $this->Tooltip->show(__('menu_grafico_puntuaciones'),__('explic_grafico_puntuaciones'));

		echo $this->Html->link(__('menu_grafico_ejecuciones_tiempo'),	"/historial_alumnos/grafico_nota_tiempo_ejecuciones/$alumno_id");
		echo $this->Tooltip->show(__('menu_grafico_ejecuciones_tiempo'),__('explic_grafico_ejecuciones_tiempo'));

		echo $this->Html->link(__('menu_grafico_ejecuciones_orden'),"/historial_alumnos/grafico_nota_orden_ejecuciones/$alumno_id");
		echo $this->Tooltip->show(__('menu_grafico_ejecuciones_orden'),__('explic_grafico_ejecuciones_orden'));

		echo $this->Html->link(__('menu_ranking_ejecuciones'),"/historial_alumnos/ranking_ejecuciones/$alumno_id");
		echo $this->Tooltip->show(__('menu_ranking_ejecuciones'),__('explic_ranking_ejecuciones_alumnos'));


		echo $this->Html->link(__('menu_ranking_nota'),"/historial_alumnos/ranking_nota/$alumno_id");
		echo $this->Tooltip->show(__('menu_ranking_nota'),__('explic_ranking_nota_alumnos'));


		echo $this->Html->link(__('menu_ranking_puntuacion'),	"/historial_alumnos/ranking_puntuacion/$alumno_id");
		echo $this->Tooltip->show(__('menu_ranking_puntuacion'),__('explic_ranking_puntuacion_alumnos'));
		
		echo $this->Html->link(__('graficos') . ' (' . __('cerrar') . ')',"/historial_alumnos/");
	?>
	</div>
	<?php
	}
?>
