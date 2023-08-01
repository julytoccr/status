<?php echo $this->element('estadisticas/alumnos/ranking',
	array(
		'pageTitle'=>__('menu_ranking_ejecuciones'),
		'valueTitle'=>__('numero_ejecuciones'),
		'rankingModel'=>'EstRankingEjecucion',
		'rankingField'=>'posicion_ranking_ejecuciones',
		'valueField'=>'num_ejecuciones'
	));
?>
