<?php
$this->start('sidebar');
echo $this->element('sidebar/sidebar', array(
    "title" => __('historial_alumno',''),
    "sidebar" => 'historial/historial',
    "alumno_id" => $alumno_id
));
$this->end();?>
<?php echo $this->element('historial/ranking',
	array(
		'valueTitle'=>__('nota_media'),
		'rankingModel'=>'EstRankingNota',
		'rankingField'=>'posicion_ranking_nota',
		'valueField'=>'nota_media'
	));
?>
