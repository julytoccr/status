<?php echo $this->element('estadisticas/alumnos/ranking',
	array(
		'pageTitle'=>__('menu_ranking_puntuacion'),
		'valueTitle'=>__('puntuacion'),
		'rankingModel'=>'EstRankingPuntuacion',
		'rankingField'=>'posicion_ranking_puntuacion',
		'valueField'=>'puntuacion_total'
	));
?>
