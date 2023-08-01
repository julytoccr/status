<?php echo $this->element('estadisticas/alumnos/ranking',
	array(
		'pageTitle'=>__('menu_ranking_nota'),
		'valueTitle'=>__('nota_media'),
		'rankingModel'=>'EstRankingNota',
		'rankingField'=>'posicion_ranking_nota',
		'valueField'=>'nota_media'
	));
?>
